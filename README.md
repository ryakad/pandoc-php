Pandoc PHP
==========

This is a naive wrapper class for the pandoc haskell command line tool. All
you will need to run this is to install pandoc. You can do this with cabal.

```shell
cabal install pandoc
```

Once installed you can use it like:

```php
$pandoc = new Pandoc();
$html = $pandoc->convert("#Hello Pandoc", "markdown_github", "html");
// $html == "<h1>Hello Pandoc</h1>"
```

for a full listing of formats that can be converted to/from you can run:

```shell
pandoc --help
```
