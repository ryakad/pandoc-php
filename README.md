Pandoc PHP
==========


Pandoc PHP is a naive wrapper for the Pandoc command. Pandoc is a Haskell
program that allows you to convert documents from one format to another. For
more information on Pandoc you can look [here](https://github.com/jgm/pandoc).

Installation
------------

First you will need [Pandoc](https://github.com/jgm/pandoc). The easiest method
to get this is with a quick `cabal install pandoc` providing you have Haskell
installed. (apt-get also supports installation of Pandoc using `apt-get install pandoc`
you just need to make sure you have at least version 1.10 for this library).

The recommended method to installing Pandoc PHP is with [composer](http://getcomposer.org)

```json
{
    "require": {
        "gafhyb/pandoc-php": "~1.0"
    }
}
```

Once installed you can start converting your content to other formats like:

```php
$pandoc = new Pandoc();
$html = $pandoc->convert("#Hello Pandoc", "markdown_github", "html");
// $html == "<h1>Hello Pandoc</h1>"
```

For a full listing of formats that can be converted to/from you should take
a look at the Pandoc documentation or the Pandoc help message `pandoc --help`.

If you need more control over how the conversion will be executed, use the `runWith` method.
This method takes two arguments, content to convert and an array containing options.
The options array can hold any of the pandoc command line options, see `pandoc --help`.

```php
$pandoc = new Pandoc\Pandoc();
$options = array(
    "from"  => "markdown",
    "to"    => "html",
    "css"   => "/assets/css/documents.css"
);
$html = $pandoc->runWith("#Hello Pandoc", $options);
```
