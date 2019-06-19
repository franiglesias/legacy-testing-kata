legacy-testing-kata-java
===

A legacy codebase that resist a bit testing, by Cyrille Martraire.

[Original kata](https://github.com/cyriux/legacy-testing-kata-java)

First try to run it.

Then your goal is to make it testable so that you can make changes (FIXME inside) and perhaps refactor it a bit.

This code draws on a C# code kata authored by my distinguished colleague Nicolas M.; Thanks Nicolas for the good ideas in the kata!

## Personal notes about this kata

This kata is very interesting to practice several refactor techniques. 

At first, you won't be able to test it, so you should relay on secure refactor techniques as provided by your IDE.

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

--

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
