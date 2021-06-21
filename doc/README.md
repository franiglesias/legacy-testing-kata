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

We have two of these dependencies in BlogAuctionTasks. One is because of a static invocation. The other one seems unnecessary, but it is better to isolate it. This is specially true if we have several calls to the same dependency.

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

This way, the main method remains static while we will be able to inject dependencies as needed. If we run the script then we get the same result as in the beginning. In fact the logic 

So we can commit this change before starting with the test. 

## Testing Application
