Pandoc PHP
==========

Pandoc PHP is a naive wrapper for the pandoc command. Pandoc is a Haskell
program that allows you to convert docuemnts from one format to another. For
more information on Pandoc you can look [here](https://github.com/jgm/pandoc).

Installation
------------

First you will need [Pandoc](https://github.com/jgm/pandoc). The easiest method
to get this is with a quick `cabal install pandoc`.

The recomended method to installing pandoc php is with [composer](http://getcomposer.org)

```json
{
    "require": {
        "ryakad/pandoc-php": "dev-master"
    }
}
```

Once installed you can start converting your content to other formats like:

```php
$pandoc = new Pandoc();
$html = $pandoc->convert("#Hello Pandoc", "markdown_github", "html");
// $html == "<h1>Hello Pandoc</h1>"
```

for a full listing of formats that can be converted to/from you should take
a look at the pandoc documentation or from a terminal you can run:

```shell
pandoc --help
```
