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

Our first step could be isolating dependencies inside the classes that use them. This way we can be in better position for testing. This refactoring can be done without having tests at all.
