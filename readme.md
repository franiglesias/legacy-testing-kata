Legacy testing kata
===

## Notes from the original README:

A legacy codebase that resist a bit testing, by Cyrille Martraire.

[Original kata](https://github.com/cyriux/legacy-testing-kata-java)

First try to run it.

Then your goal is to make it testable so that you can make changes (FIXME inside) and perhaps refactor it a bit.

This code draws on a C# code kata authored by my distinguished colleague Nicolas M.; Thanks Nicolas for the good ideas in the kata!

## Personal notes about this kata

This kata is very interesting to practice several refactor techniques. 

At first, you won't be able to test it, so you should relay on secure refactor techniques as provided by your IDE.

One interesting suggestion is to keep two enviornments for this exercise: one that simulates a **production** setting, and one for the **development/testing**. This way, you can introduce changes and see how they would affect the production side. You should commit **small sets of changes** that doesn't break the application in production environment.

## Notes on PHP version

This PHP version tries to mimic the behavior of the original java.

To try the code, clone or download this repo.

Then, run composer:

```
    composer install
```

This will install PHPUnit. PSR4 autoload for the `QuoteBot` namespace and `lib` folder is loaded via classmap.

And then, execute the run.php script to see how this program works (to say something):

```
    php run.php
```

Or use the IDE facilities to run.

**Important note:** you must not touch the `lib` folder, given it is considered a vendor. 

## Notes on practicing this kata inside docker

Start containers. First time docker needs to download images, so it could take a while.

```
docker-compose up -d
```

You can jump into the container

```
docker exec -it quotebot bash
```

Run composer install inside de container or 

```
docker exec -it quotebot composer install
```

## Notes on configuring PHPStorm with the dockerized environment

First of all, start up docker containers.

```
docker-compose up
```

### Configure PHP CLI

Go to **PHP Storm > Preferences > Languages and Frameworks > PHP**

Clic **CLI Interpreter …**

Add Remote Interpreter from Docker

Select (radio button) **docker-compose** and select **php** service.

### Configure XDebug

Go to **PHP Storm > Preferences > Languages and Frameworks > PHP > Debug**

Make sure XDebug port is 9001

### Configure PhpUnit

Go to **PHP Storm > Preferences > Languages and Frameworks > PHP > Test Frameworks**

Add **PhpUnit by remote interpreter** configuration. Select the remote Interpreter you've configured before.

PHPUnit Library from composer autoloader. Path to script should be: vendor/autoload.php

Select phpunit.xml as default configuration file.



## Where to start? Extracted notes from the original

If you hesitate where to start, here are some of the tricky bits that make it hard to test:

* Lack of dependency injection:
* A static main with no args
* Static service
* Hard-coded instantiation of a service that itself instantiates its dependencies, and again

Implementation issues:

* Very slow service
* Hidden dependency to a license key in env variable
* Random return value -> non-deterministic test
* Dialog poping up prompting for user input

Other tricks:

* New Date() in the middle of calculations -> non-deterministic test
* High combinatorial of calculations lead to too many required test cases
* Stateful behavior from internal cache: first call different from next calls
* Heavy dependency called within a large loop with different values
* Use a dependency or another depending on the passed parameter
