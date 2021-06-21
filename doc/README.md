# The QuoteBot kata

## Trying to run

```bash
docker exec -it quotebot php run.php
```
We find this problem:

```
[Stupid license] Missing license!!!! in /application/lib/MarketStudyVendor.php:8
```

If we examine the code, we find that there is a hidden requirement:

```php
class MarketStudyVendor
{
    public function averagePrice(string $blog): float
    {
        if (empty(getenv("license"))) {
            throw new RuntimeException("[Stupid license] Missing license!!!!");
        }
        return (hashCode($blog) * (mt_rand() / mt_getrandmax()));
    }
}
```

We need to set an environment variable. We can bypass this setting the env variable in the command line, when executing the script:

```bash
docker exec -it -e license=some quotebot php run.php
```

Nevertheless, we have a second problem here:

```
You've pushed a dummy auction to a real ads platform, the business is upset!%
```

That means that we are executing some real service from the local setup. Something that we shouldn't be doing. This happens here:

```php
class QuotePublisher
{

    public static function publish(float $todayPrice): void
    {
        echo("You've pushed a dummy auction to a real ads platform, the business is upset!");
        die();
    }
}
```

So, we are coupled with at least two concrete vendors, so we need to apply dependency inversion in order to be able to put the application in better state. In fact, we also have hidden dependencies here:

```php
class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
        $blogs = AdSpace::getAdSpaces($mode);
        foreach ($blogs as $blog) {
            $blogAuctionTask = new BlogAuctionTask();
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }
}
```

Here:

```php
class AdSpace
{
    private static $cache = [];

    /** Get all ad spaces */
    public static function getAdSpaces()
    {
        if (isset(static::$cache['bloglist'])) {
            return static::$cache['bloglist'];
        }

        // FIXME : only return blogs that start with a 'T'

        $listAllBlogs = TechBlogs::listAllBlogs();
        static::$cache['bloglist'] = $listAllBlogs;

        return $listAllBlogs;
    }
}
```
One of the problems is that the coupling is pretty strong due to several reasons:

* Static dependencies inside de code
* There is no dependency injection at all

## Isolate dependencies

Our first step could be isolating dependencies inside the classes that use them. This way we can be in better position for testing. This refactoring can be done without having tests at all.

General procedure consists of moving the calls to dependencies to protected methods. This will allow us to create testable classes overriding the methods containing these calls, so we can avoid the coupling in the test environment. When we have tests in place, we can start moving to a decoupled set up.

Let's see some examples.

Before:

```php
class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
        $blogs = AdSpace::getAdSpaces($mode);
        foreach ($blogs as $blog) {
            $blogAuctionTask = new BlogAuctionTask();
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }
}
```
After:

```php
class AutomaticQuoteBot
{
    public function sendAllQuotes(string $mode): void
    {
		$blogs = $this->getBlogs($mode);
		foreach ($blogs as $blog) {
            $blogAuctionTask = new BlogAuctionTask();
            $blogAuctionTask->priceAndPublish($blog, $mode);
        }
    }

	protected function getBlogs(string $mode)
	{
		return AdSpace::getAdSpaces($mode);
	}
}
```

That's for the static dependency. We have also a hidden dependency with `BlogAuctionTask`, something that we can improve, isolating it, but also by allowing it to be injected.

BlogAuctionTask has no state to maintain, so we only need a single instance. We promote it to member of AutomaticQuoteBot and instantiate it in the constructor.

```php
class AutomaticQuoteBot
{
	private BlogAuctionTask $blogAuctionTask;

	public function __construct()
	{
		$this->blogAuctionTask = new BlogAuctionTask;
	}

	public function sendAllQuotes(string $mode): void
	{
		$blogs = $this->getBlogs($mode);
		foreach ($blogs as $blog) {
			$this->blogAuctionTask->priceAndPublish($blog, $mode);
		}
	}

	protected function getBlogs(string $mode)
	{
		return AdSpace::getAdSpaces($mode);
	}
}
```

Now, we make it optionally injectable, so we don't break the current way of instantiating it, but allowing us to replace it via injection.

```php
class AutomaticQuoteBot
{
	private BlogAuctionTask $blogAuctionTask;

	public function __construct(?BlogAuctionTask $blogAuctionTask = null)
	{
		$this->blogAuctionTask = $blogAuctionTask ?? new BlogAuctionTask;
	}

	public function sendAllQuotes(string $mode): void
	{
		$blogs = $this->getBlogs($mode);
		foreach ($blogs as $blog) {
			$this->blogAuctionTask->priceAndPublish($blog, $mode);
		}
	}

	protected function getBlogs(string $mode)
	{
		return AdSpace::getAdSpaces($mode);
	}
}
```

We have two of these dependencies in BlogAuctionTask. One is because of a static invocation. The other one can be isolated. This is specially true when we have several calls to the same dependency. Also, we want to be able to inject the dependency.

Before:

```php
class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;

    public function __construct()
    {
        $this->marketDataRetriever = new MarketStudyVendor();
    }

    public function priceAndPublish(string $blog, string $mode)
    {
        $avgPrice = $this->marketDataRetriever->averagePrice($blog);

        // FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

        \QuotePublisher::publish($proposal);
    }
}
```

After, isolating the `QuotePublisher`:

```php
class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;

    public function __construct()
    {
        $this->marketDataRetriever = new MarketStudyVendor();
    }

    public function priceAndPublish(string $blog, string $mode)
    {
        $avgPrice = $this->marketDataRetriever->averagePrice($blog);

        // FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		\QuotePublisher::publish($proposal);
	}
}
```

After, isolating also the average price retrieving:

```php
class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;

    public function __construct()
    {
        $this->marketDataRetriever = new MarketStudyVendor();
    }

    public function priceAndPublish(string $blog, string $mode)
    {
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		\QuotePublisher::publish($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
```

Finally, making dependency injectable:

```php
class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;

    public function __construct(?MarketStudyVendor $marketStudyVendor = null)
    {
        $this->marketDataRetriever = $marketStudyVendor ?? new MarketStudyVendor();
    }

    public function priceAndPublish(string $blog, string $mode)
    {
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		\QuotePublisher::publish($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
```

AddSpace has algo this kind of static dependency, but AdSpace has its own problems with its singleton implementation. We can wait for improving it later.

```php
class AdSpace
{
    private static $cache = [];

    /** Get all ad spaces */
    public static function getAdSpaces()
    {
        if (isset(static::$cache['bloglist'])) {
            return static::$cache['bloglist'];
        }

        // FIXME : only return blogs that start with a 'T'

        $listAllBlogs = TechBlogs::listAllBlogs();
        static::$cache['bloglist'] = $listAllBlogs;

        return $listAllBlogs;
    }
}
```

Note that there are some TODO/FIXME comments in the code. We will address them later in the development. In the meantime, we will commit the changes.

## Let's put application under test

Examining the code, we can see that the script runs by issuing a static call to Application::main method. This method instantiates AutomaticQuoteBot making it difficult to replace it with a testable version.

One possible approach can be to test AutomaticQuoteBot, given that we simply instantiate it to make a call to its main method. Nevertheless, if we want to really improve the design we should test from the outside. Is there a way to put the Application in a test harness, even being statically called.

```php
class Application
{
    /** main application method */
    public static function main(array $args = null)
    {
        $bot = new AutomaticQuoteBot();
        $bot->sendAllQuotes('FAST');
    }
}
```

Can we do it in a way that allows injectable dependencies? This is one possible approach. First, we introduce a static member to hold the AutomaticQuoteBot, and we initialize it with a new instance if it is not previously defined.

```php
class Application
{
	private static ?AutomaticQuoteBot $quoteBot;
	
	/** main application method */
    public static function main(array $args = null): void
	{
    	if (null === self::$quoteBot) {
    		self::$quoteBot = new AutomaticQuoteBot();
		}
        
        self::$quoteBot->sendAllQuotes('FAST');
    }
}
```

Next step is to introduce a method that will allow us to inject an instance of AutomaticQuoteBot.

```php
class Application
{
	private static ?AutomaticQuoteBot $quoteBot;

	public static function injectBot(AutomaticQuoteBot $quoteBot): void
	{
		self::$quoteBot = $quoteBot;
	}
	
	/** main application method */
    public static function main(array $args = null): void
	{
    	if (null === self::$quoteBot) {
    		self::$quoteBot = new AutomaticQuoteBot();
		}
        
        self::$quoteBot->sendAllQuotes('FAST');
    }
}
```

This way, the `main` method remains static while we will be able to inject dependencies as needed. If we run the script then we get the same result as in the beginning. In fact the logic 

So we can commit this change before starting with the test. 

## Testing Application

To write the test we will need to understand something about the expected behavior of the application. The basic idea is to generate proposals to publish ads in several blogs. So the expected outcome is to have one proposal for each of the blogs registered in the application.

The list of blogs comes from the `TechBlog` class, that simulates a database, via the `AdSpace` class, and it is used as input by `AutomaticQuoteBot`. Thankfully, we've extracted this in the `getBlogs` method, that we can override in a test crafted version of `AutomaticQuoteBot`.

The same can be said about `BlogAuctionTask`. It is responsible for both calculating the proposal and publishing it with the `QuotePublisher`. We can override the `publishProposal` method to convert it into a spy, so we can check the outcome without touching any external service.

Also, this class depends on `MarketStudyVendor`, a service that requires a license to run. We will need to mock its behavior.

This is the test. At this point we don't have details about how the system calculates the prices, but it is enough to have a safety net to start refactoring the whole application. 

```php
class ApplicationTest extends TestCase
{
	/** @test */
	public function shouldPublishOneProposalForEachBlog(): void
	{
		$marketStudyVendor = $this->createMock(MarketStudyVendor::class);
		$marketStudyVendor->method('averagePrice')->willReturn(0.0);

		$blogAuctionTask = $this->buildBlogAuctionTask($marketStudyVendor);
		$quoteBot        = $this->buildQuoteBot($blogAuctionTask, ['Blog 1', 'Blog 2']);

		Application::injectBot($quoteBot);
		Application::main();

		self::assertCount(2, $blogAuctionTask->proposals);
	}

	private function buildQuoteBot(BlogAuctionTask $blogAuctionTask, array $blogs): AutomaticQuoteBot
	{
		return new class($blogAuctionTask, $blogs) extends AutomaticQuoteBot {
			private array $blogs;

			public function __construct(BlogAuctionTask $blogAuctionTask, array $blogs)
			{
				$this->blogs = $blogs;
				parent::__construct($blogAuctionTask);
			}

			protected function getBlogs(string $mode): array
			{
				return $this->blogs;
			}
		};
	}

	private function buildBlogAuctionTask(MarketStudyVendor $marketStudyVendor): BlogAuctionTask
	{
		return new class($marketStudyVendor) extends BlogAuctionTask {
			public array $proposals = [];

			protected function publishProposal($proposal): void
			{
				$this->proposals[] = $proposal;
			}
		};
	}
}
```
## Starting the architectural refactoring

We could take different approaches to drive the refactor of this application. It depends on the trait we want to prioritize. At this point, we managed to reduce the coupling with the vendor dependencies by applying the Dependency Injection pattern when possible.

Now, we need to move forward a bit, by extracting the static dependencies and applying the Dependency Inversion Principle, so we can evolve our code independently of the vendors.

Let's start with the QuotePublisher inside BlogAuctionTask. We have it isolated already. Now we need to extract the `publishProposal` method to a new class that we can inject. So, we can extract first an interface. This interface will be part of the Domain layer.

```php
namespace Quotebot\Domain;

interface Publisher
{
	public function publishProposal($proposal): void;
}
```

Now, we can implement this Publisher interface using the QuotePublisher vendor creating an Adapter in the Infrastructure layer. As you can see, we only have to copy the method in `BlogAuctionTask`. 

```php
namespace Quotebot\Infrastructure;


use Quotebot\Domain\Publisher;
use QuotePublisher;

class VendorQuotePublisher implements Publisher
{

	public function publishProposal($proposal): void
	{
		QuotePublisher::publish($proposal);
	}
}
```

For the moment, changes should not affect our `ApplicationTest`. Anyway, the next steps should.

First, we are going to inject the new VendorQuotePublisher in BlogAuctionTask. For the moment, it is better to let it be optional. W

```php
use MarketStudyVendor;
use Quotebot\Domain\Publisher;
use Quotebot\Infrastructure\VendorQuotePublisher;

class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;
	private ?Publisher $publisher;

	public function __construct(
    	?MarketStudyVendor $marketStudyVendor = null,
		?Publisher $publisher = null
	)
    {
        $this->marketDataRetriever = $marketStudyVendor ?? new MarketStudyVendor();
		$this->publisher = $publisher ?? new VendorQuotePublisher();
	}

    public function priceAndPublish(string $blog, string $mode)
    {
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		\QuotePublisher::publish($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
```

We run ApplicationTest again to verify that we haven't broken things. If all goes green, then we will introduce this change to make the injected dependency publish the proposals_

```php
class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;
	private ?Publisher $publisher;

	public function __construct(
    	?MarketStudyVendor $marketStudyVendor = null,
		?Publisher $publisher = null
	)
    {
        $this->marketDataRetriever = $marketStudyVendor ?? new MarketStudyVendor();
		$this->publisher = $publisher ?? new VendorQuotePublisher();
	}

    public function priceAndPublish(string $blog, string $mode)
    {
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
```

Run the ApplicationTest again. It should confirm that all is working as expected. We have managed to remove a hidden, static dependency, making it injectable and inverting the control.

We should change the test to reflect this changes. This will remove the need to make private the `publishProposal` method, given we will no longer need to override it for testing. Also, we won't need to spy on BlogAuctionTask. We can move this logic to a special Publisher for testing, a PublisherSpy.

```php
namespace Quotebot\Tests\E2e\Doubles;


use Quotebot\Domain\Publisher;

class PublisherSpy implements Publisher
{
	private array $proposals = [];

	public function publishProposal($proposal): void
	{
		$this->proposals[] = $proposal;
	}

	public function proposals(): array
	{
		return $this->proposals;
	}
}
```

Here is the test:

```php
namespace Quotebot\Tests\E2e;

use MarketStudyVendor;
use PHPUnit\Framework\TestCase;
use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Tests\E2e\Doubles\PublisherSpy;

class ApplicationTest extends TestCase
{
	/** @test */
	public function shouldPublishOneProposalForEachBlog(): void
	{
		$marketStudyVendor = $this->createMock(MarketStudyVendor::class);
		$marketStudyVendor->method('averagePrice')->willReturn(0.0);

		$publisherSpy    = new PublisherSpy();
		$blogAuctionTask = new BlogAuctionTask($marketStudyVendor, $publisherSpy);
		$quoteBot        = $this->buildQuoteBot($blogAuctionTask, ['Blog 1', 'Blog 2']);

		Application::injectBot($quoteBot);
		Application::main();

		self::assertCount(2, $publisherSpy->proposals());
	}

	private function buildQuoteBot(BlogAuctionTask $blogAuctionTask, array $blogs): AutomaticQuoteBot
	{
		return new class($blogAuctionTask, $blogs) extends AutomaticQuoteBot {
			private array $blogs;

			public function __construct(BlogAuctionTask $blogAuctionTask, array $blogs)
			{
				$this->blogs = $blogs;
				parent::__construct($blogAuctionTask);
			}

			protected function getBlogs(string $mode): array
			{
				return $this->blogs;
			}
		};
	}
}
```

The test is passing, so we can stop here and commit the changes so far.

## Inverting MarketStudyVendor dependency

Next change I would like to perform is to invert the MarketStudyVendor dependency.

So, first, we make sure that the current ApplicationTest passes. Once it does, we extract an interface. We can use the
current MarketStudyVendor as template for that.

```php
interface MarketDataProvider
{
	public function averagePrice(string $blog): float;
}
```

Now, we are going to create an Adapter that implements is by using the MarketStudyVendor class. It is pretty simple:

```php
namespace Quotebot\Infrastructure;


use Quotebot\Domain\MarketDataProvider;

class VendorMarketDataProvider implements MarketDataProvider
{
	private \MarketStudyVendor $marketStudyVendor;

	public function __construct(\MarketStudyVendor $marketStudyVendor)
	{
		$this->marketStudyVendor = $marketStudyVendor;
	}

	public function averagePrice(string $blog): float
	{
		return $this->marketStudyVendor->averagePrice($blog);
	}
}
```

We will need to change the test in order to use the new interface.

```php
class ApplicationTest extends TestCase
{
	/** @test */
	public function shouldPublishOneProposalForEachBlog(): void
	{
		$marketDataProvider = $this->createMock(MarketDataProvider::class);
		$marketDataProvider->method('averagePrice')->willReturn(0.0);

		$publisherSpy    = new PublisherSpy;
		$blogAuctionTask = new BlogAuctionTask($marketDataProvider, $publisherSpy);
		$quoteBot        = $this->buildQuoteBot($blogAuctionTask, ['Blog 1', 'Blog 2']);

		Application::injectBot($quoteBot);
		Application::main();

		self::assertCount(2, $publisherSpy->proposals());
	}

	private function buildQuoteBot(BlogAuctionTask $blogAuctionTask, array $blogs): AutomaticQuoteBot
	{
		return new class($blogAuctionTask, $blogs) extends AutomaticQuoteBot {
			private array $blogs;

			public function __construct(BlogAuctionTask $blogAuctionTask, array $blogs)
			{
				$this->blogs = $blogs;
				parent::__construct($blogAuctionTask);
			}

			protected function getBlogs(string $mode): array
			{
				return $this->blogs;
			}
		};
	}
}
```

Change the BlogAuctionTask code to use the new Interface.

```php
namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Publisher;
use Quotebot\Infrastructure\VendorMarketDataProvider;
use Quotebot\Infrastructure\VendorQuotePublisher;

class BlogAuctionTask
{
    private ?MarketDataProvider $marketDataRetriever;
	private ?Publisher $publisher;

	public function __construct(
    	?MarketDataProvider $marketStudyVendor = null,
		?Publisher $publisher = null
	)
    {
        $this->marketDataRetriever = $marketStudyVendor ?? new VendorMarketDataProvider(new MarketStudyVendor);
		$this->publisher = $publisher ?? new VendorQuotePublisher();
	}

    public function priceAndPublish(string $blog, string $mode)
    {
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
```

Right now, the injection is not finished yet. We need to clean things inside `BlogAuctionTask` constructor, so we'll have to add production code that makes use of the new injection capabilities.

Basically, we need to change **run.php**, so we instantiate all the things we will need in production code, allowing us to make BlogAuctionTask mandatory instead of optional.

```php
require 'vendor/autoload.php';

use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Infrastructure\VendorMarketDataProvider;
use Quotebot\Infrastructure\VendorQuotePublisher;

$marketDataProvider = new VendorMarketDataProvider(new MarketStudyVendor);
$publisher = new VendorQuotePublisher;
$blogAuctionTask    = new BlogAuctionTask($marketDataProvider, $publisher);
$automaticQuoteBot  = new AutomaticQuoteBot($blogAuctionTask);

Application::injectBot($automaticQuoteBot);
Application::main();
```

If we execute run.php as if it was in the production environment, we can verify that the behavior is exactly the same. Also, if we execute the test.

We are ready to fix the problem of having the dependencies as optional in BlogAuctionTask constructor, making them required.

```php
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Publisher;

class BlogAuctionTask
{
	private MarketDataProvider $marketDataRetriever;
	private Publisher $publisher;

	public function __construct(
		MarketDataProvider $marketStudyVendor,
		Publisher $publisher
	) {
		$this->marketDataRetriever = $marketStudyVendor;
		$this->publisher           = $publisher;
	}

    public function priceAndPublish(string $blog, string $mode): void
	{
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = 1;

        if ($mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
```

Finally, all these changes deserve a commit.

## Improving BlogAuctionTask design

`BlogAuctionTask` holds a lot of logic. It is easy to see that we can organize code better and move some responsibilities to other clases. Where to start? Let's review some interesting points:

* TimeFactor calculation, for example, can be easily isolated. In fact, it strongly suggests a Value Object to represent all the modes, and their associated time factors.
* We have a global state dependency because we are instantiating DateTime.

## TimeFactor isolation

We will start with TimeFactor isolation. It is more important to address the problem with the system clock dependency, but moving the calculation to its own method is safe and will help us to learn how to manage it.

```php
<?php

namespace Quotebot;

use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Publisher;

class BlogAuctionTask
{
	private MarketDataProvider $marketDataRetriever;
	private Publisher $publisher;

	public function __construct(
		MarketDataProvider $marketStudyVendor,
		Publisher $publisher
	) {
		$this->marketDataRetriever = $marketStudyVendor;
		$this->publisher           = $publisher;
	}

    public function priceAndPublish(string $blog, string $mode): void
	{
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        
		$timeFactor = $this->timeFactor($mode);

		$proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}

	protected function timeFactor(string $mode): int
	{
		$timeFactor = 1;

		if ($mode === 'SLOW') {
			$timeFactor = 2;
		}

		if ($mode === 'MEDIUM') {
			$timeFactor = 4;
		}

		if ($mode === 'FAST') {
			$timeFactor = 8;
		}

		if ($mode === 'ULTRAFAST') {
			$timeFactor = 13;
		}

		return $timeFactor;
	}
}
```

## Managing global state dependencies

Inside BlogAuctionTask we require some timestamps in order to calculate proposals. This makes testing difficult because of the nondeterministic results. The best approach is to isolate and extract the dependency to an external Clock Service that we can mock in tests.

This is the part of the code that we want to address:

```php
$proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
    * $timeFactor
    * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();
```

First, a little cosmetic reorganization to understand what's going on here:

```php
$proposal = $proposal % 2 === 0 
    ? 3.14 * $proposal 
    : 3.15 * $timeFactor * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();
```

So, we have two ways for the proposal calculation. We will start by isolating the dependency in its own method. This will make the code easier to read and understand:

```php
    $proposal = $proposal % 2 === 0 
        ? 3.14 * $proposal 
        : 3.15 * $timeFactor * $this->timeDiff('2000-1-1');

    $this->publishProposal($proposal);
}

private function timeDiff(string $from): int
{
    return (new \DateTime())->getTimestamp()
        - (new \DateTime($from))->getTimestamp();
}
```

Now, it is easy to see that we want a timestamp diff since an arbitrary date. We rename the method to reflect that:

```php
	private function timestampDiff(string $since): int
	{
		return (new \DateTime())->getTimestamp()
			- (new \DateTime($since))->getTimestamp();
	}
```

We can now extract this method to a new class implementing a ClockService interface. The new class can be called `SystemClockService`. By using the automated refactor of PhpStorm we get this:

```php
namespace Quotebot;

use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Publisher;
use Quotebot\Infrastructure\SystemClockService;

class BlogAuctionTask
{
	private MarketDataProvider $marketDataRetriever;
	private Publisher $publisher;
	private SystemClockService $systemClockService;

	public function __construct(
		MarketDataProvider $marketStudyVendor,
		Publisher $publisher
	) {
		$this->marketDataRetriever = $marketStudyVendor;
		$this->publisher           = $publisher;
		$this->systemClockService  = new SystemClockService;
	}

	public function priceAndPublish(string $blog, string $mode): void
	{
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

		$proposal = $avgPrice + 1;

		$timeFactor = $this->timeFactor($mode);

		$proposal = $proposal % 2 === 0
			? 3.14 * $proposal
			: 3.15 * $timeFactor * $this->timestampDiff('2000-1-1');

		$this->publishProposal($proposal);
	}

	private function timestampDiff(string $since): int
	{
		return $this->systemClockService->timestampDiff($since);
	}

	protected function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}

	protected function timeFactor(string $mode): int
	{
		$timeFactor = 1;

		if ($mode === 'SLOW') {
			$timeFactor = 2;
		}

		if ($mode === 'MEDIUM') {
			$timeFactor = 4;
		}

		if ($mode === 'FAST') {
			$timeFactor = 8;
		}

		if ($mode === 'ULTRAFAST') {
			$timeFactor = 13;
		}

		return $timeFactor;
	}
}
```

Now, we will use the automatic refactoring to extract the interface:

```php
namespace Quotebot\Domain;

interface ClockService
{
	public function timestampDiff(string $since): int;
}
```

We verify that ApplicationTest is passing. Our next step is to inject the dependency and invert it. We will need to make changes in the test and in production code. This will be **run.php**:

```php
require 'vendor/autoload.php';

use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Infrastructure\SystemClockService;
use Quotebot\Infrastructure\VendorMarketDataProvider;
use Quotebot\Infrastructure\VendorQuotePublisher;

$marketDataProvider = new VendorMarketDataProvider(new MarketStudyVendor);
$publisher          = new VendorQuotePublisher;
$clockService       = new SystemClockService;
$blogAuctionTask    = new BlogAuctionTask(
	$marketDataProvider,
	$publisher,
	$clockService
);
$automaticQuoteBot  = new AutomaticQuoteBot($blogAuctionTask);

Application::injectBot($automaticQuoteBot);
Application::main();
```

The test:

```php
class ApplicationTest extends TestCase
{
	/** @test */
	public function shouldPublishOneProposalForEachBlog(): void
	{
		$marketDataProvider = $this->createMock(MarketDataProvider::class);
		$marketDataProvider->method('averagePrice')->willReturn(0.0);

		$publisherSpy    = new PublisherSpy;
		
		$clockService    = $this->createMock(ClockService::class);
		$clockService->method('timestampDiff')->willReturn(0);
		
		$blogAuctionTask = new BlogAuctionTask(
			$marketDataProvider,
			$publisherSpy,
			$clockService
		);
		$quoteBot        = $this->buildQuoteBot($blogAuctionTask, ['Blog 1', 'Blog 2']);

		Application::injectBot($quoteBot);
		Application::main();

		self::assertCount(2, $publisherSpy->proposals());
	}

	private function buildQuoteBot(BlogAuctionTask $blogAuctionTask, array $blogs): AutomaticQuoteBot
	{
		return new class($blogAuctionTask, $blogs) extends AutomaticQuoteBot {
			private array $blogs;

			public function __construct(BlogAuctionTask $blogAuctionTask, array $blogs)
			{
				$this->blogs = $blogs;
				parent::__construct($blogAuctionTask);
			}

			protected function getBlogs(string $mode): array
			{
				return $this->blogs;
			}
		};
	}
}
```

Here is the BlogAuctionTask class with the dependency injected:

```php
namespace Quotebot;

use Quotebot\Domain\ClockService;
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Publisher;

class BlogAuctionTask
{
	private MarketDataProvider $marketDataRetriever;
	private Publisher $publisher;
	private ClockService $clockService;

	public function __construct(
		MarketDataProvider $marketStudyVendor,
		Publisher $publisher,
		ClockService $clockService
	) {
		$this->marketDataRetriever = $marketStudyVendor;
		$this->publisher           = $publisher;
		$this->clockService        = $clockService;
	}

	public function priceAndPublish(string $blog, string $mode): void
	{
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

		$proposal = $avgPrice + 1;

		$timeFactor = $this->timeFactor($mode);

		$proposal = $proposal % 2 === 0
			? 3.14 * $proposal
			: 3.15 * $timeFactor * $this->timestampDiff('2000-1-1');

		$this->publishProposal($proposal);
	}

	private function timestampDiff(string $since): int
	{
		return $this->clockService->timestampDiff($since);
	}

	protected function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}

	protected function timeFactor(string $mode): int
	{
		$timeFactor = 1;

		if ($mode === 'SLOW') {
			$timeFactor = 2;
		}

		if ($mode === 'MEDIUM') {
			$timeFactor = 4;
		}

		if ($mode === 'FAST') {
			$timeFactor = 8;
		}

		if ($mode === 'ULTRAFAST') {
			$timeFactor = 13;
		}

		return $timeFactor;
	}
}
```

Right now, we can make all protected methods private, because we don't need to override any of them, and we can commit the changes.

## Addressing primitive obsession

Let's consider the situation: we have concepts like blog, time factor, mode, price and proposal, that belongs to the domain of the application. They all are modelled with primitive types. We can do better.

Mode and TimeFactor have a direct relation: TimeFactor is a direct function of Mode. We can express this using a value object: 

From this:

```php
    $timeFactor = $this->timeFactor($mode);
```

To this:

```php
    $mode = new Mode($mode);
    $timeFactor = $mode->timeFactor();
```

So, basically we can create the class Mode by extracting the timeFactor method. First step: 

```php
namespace Quotebot\Domain;


class Mode
{

	public function timeFactor(string $mode): int
	{
		$timeFactor = 1;

		if ($mode === 'SLOW') {
			$timeFactor = 2;
		}

		if ($mode === 'MEDIUM') {
			$timeFactor = 4;
		}

		if ($mode === 'FAST') {
			$timeFactor = 8;
		}

		if ($mode === 'ULTRAFAST') {
			$timeFactor = 13;
		}

		return $timeFactor;
	}
}
```

Now, we move the parameter to the constructor:

```php
<?php
declare(strict_types=1);

namespace Quotebot\Domain;


class Mode
{

	private string $mode;

	public function __construct(string $mode)
	{
		$this->mode = $mode;
	}
	
	public function timeFactor(): int
	{
		$timeFactor = 1;

		if ($this->mode === 'SLOW') {
			$timeFactor = 2;
		}

		if ($this->mode === 'MEDIUM') {
			$timeFactor = 4;
		}

		if ($this->mode === 'FAST') {
			$timeFactor = 8;
		}

		if ($this->mode === 'ULTRAFAST') {
			$timeFactor = 13;
		}

		return $timeFactor;
	}
}
```

It is easy to see that we can do better with a map:

```php
namespace Quotebot\Domain;


class Mode
{

	private string $mode;

	private const SLOW      = 'SLOW';
	private const MEDIUM    = 'MEDIUM';
	private const FAST      = 'FAST';
	private const ULTRAFAST = 'ULTRAFAST';

	private const TIME_FACTOR = [
		self::SLOW      => 2,
		self::MEDIUM    => 4,
		self::FAST      => 8,
		self::ULTRAFAST => 13,
	];

	public function __construct(string $mode)
	{
		$this->mode = $mode;
	}

	public function timeFactor(): int
	{
		return self::TIME_FACTOR[$this->mode];
	}
}
```

There are two problems, though:

What should happen if we try to instantiate the mode with an invalid or unknown value? From the previous code we can expect that invalid mode should have associated a TimeFactor equal to 1. 

What if we try to instantiate with a mode raw value in lower case?.

We can TDD changes in Mode to address those problems. Here is the first test:

```php
namespace Quotebot\Tests\Domain;

use Quotebot\Domain\Mode;
use PHPUnit\Framework\TestCase;

class ModeTest extends TestCase
{
	/** @test */
	public function shouldAllowUnknownModesWithNeutralTimeFactor(): void
	{
		$mode = new Mode('UNKNOWN');

		self::assertEquals(1, $mode->timeFactor());
	}
}
```

This test should fail, and it does, prompting us to implement something that makes it pass. This is the production code after some refactoring:

```php
<?php
declare(strict_types=1);

namespace Quotebot\Domain;


class Mode
{

	private string $mode;

	private const SLOW      = 'SLOW';
	private const MEDIUM    = 'MEDIUM';
	private const FAST      = 'FAST';
	private const ULTRAFAST = 'ULTRAFAST';
	private const UNKNOWN   = 'UNKNOWN';

	private const TIME_FACTOR = [
		self::UNKNOWN   => 1,
		self::SLOW      => 2,
		self::MEDIUM    => 4,
		self::FAST      => 8,
		self::ULTRAFAST => 13,
	];

	public function __construct(string $mode)
	{
		if ($this->modelRawValueIsKnown($mode)) {
			$mode = self::UNKNOWN;
		}

		$this->mode = $mode;
	}

	public function timeFactor(): int
	{
		return self::TIME_FACTOR[$this->mode];
	}

	protected function modelRawValueIsKnown(string $mode): bool
	{
		$knownValues = array_keys(self::TIME_FACTOR);

		return !in_array($mode, $knownValues, true);
	}
}
```

The second concert is a sanitization. We want to make sure that the constructor is case-insensitive:

```php
namespace Quotebot\Tests\Domain;

use Quotebot\Domain\Mode;
use PHPUnit\Framework\TestCase;

class ModeTest extends TestCase
{
	/** @test */
	public function shouldAllowUnknownModesWithNeutralTimeFactor(): void
	{
		$mode = new Mode('UNKNOWN');

		self::assertEquals(1, $mode->timeFactor());
	}

	/** @test */
	public function shouldBeCaseInsensitive(): void
	{
		$mode = new Mode('fast');

		self::assertEquals(8, $mode->timeFactor());
	}
}
```

We can make the test pass with this simple addition:

```php
<?php
declare(strict_types=1);

namespace Quotebot\Domain;


class Mode
{

	private string $mode;

	private const SLOW      = 'SLOW';
	private const MEDIUM    = 'MEDIUM';
	private const FAST      = 'FAST';
	private const ULTRAFAST = 'ULTRAFAST';
	private const UNKNOWN   = 'UNKNOWN';

	private const TIME_FACTOR = [
		self::UNKNOWN   => 1,
		self::SLOW      => 2,
		self::MEDIUM    => 4,
		self::FAST      => 8,
		self::ULTRAFAST => 13,
	];

	public function __construct(string $mode)
	{
		$mode = strtoupper($mode);
		
		if ($this->modelRawValueIsKnown($mode)) {
			$mode = self::UNKNOWN;
		}

		$this->mode = $mode;
	}

	public function timeFactor(): int
	{
		return self::TIME_FACTOR[$this->mode];
	}

	protected function modelRawValueIsKnown(string $mode): bool
	{
		$knownValues = array_keys(self::TIME_FACTOR);

		return !in_array($mode, $knownValues, true);
	}
}
```

Last thing to do is that we should pass the new Mode object as parameter to BlogAuctionTask::priceAndPublish. So we look for its usages to see where we should make the change. The only usage is in AutomaticQuoteBot:

```php
namespace Quotebot;

use Quotebot\Domain\Mode;

class AutomaticQuoteBot
{
	private BlogAuctionTask $blogAuctionTask;

	public function __construct(?BlogAuctionTask $blogAuctionTask = null)
	{
		$this->blogAuctionTask = $blogAuctionTask ?? new BlogAuctionTask;
	}

	public function sendAllQuotes(string $mode): void
	{
		$blogs = $this->getBlogs($mode);
		foreach ($blogs as $blog) {
			$this->blogAuctionTask->priceAndPublish($blog, new Mode($mode));
		}
	}

	protected function getBlogs(string $mode)
	{
		return AdSpace::getAdSpaces($mode);
	}
}
```

We could improve this code, because $mode has a unique value for all the blogs. We can make some naming changes and allow `Mode` to have a canonical string representation by using the magic of `__toString`. We also take the chance to make dependency mandatory.

```php
namespace Quotebot;

use Quotebot\Domain\Mode;

class AutomaticQuoteBot
{
	private BlogAuctionTask $blogAuctionTask;

	public function __construct(BlogAuctionTask $blogAuctionTask)
	{
		$this->blogAuctionTask = $blogAuctionTask;
	}

	public function sendAllQuotes(string $rawMode): void
	{
		$mode = new Mode($rawMode);

		$blogs = $this->getBlogs($mode);
		foreach ($blogs as $blog) {
			$this->blogAuctionTask->priceAndPublish($blog, $mode);
		}
	}

	protected function getBlogs(Mode $mode)
	{
		return AdSpace::getAdSpaces((string)$mode);
	}
}
```

When we run ApplicationTest we will notice that the anonymous class extending from AutomaticQuoteBot should be changed to honor the signature change in the getBlogs method.

All this changes can be commited.
