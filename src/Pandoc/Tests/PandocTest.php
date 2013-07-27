<?php
/**
 * Pandoc PHP
 * Copyright (c) Ryan Kadwell <ryan@riaka.ca>
 */

namespace Pandoc\Tests;

use Pandoc\Pandoc;

/**
 * @author Ryan Kadwell <ryan@riaka.ca>
 */
class PandocTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Pandoc\PandocException
     */
    public function testInvalidPandocCommandThrowsException()
    {
        $pandoc = new Pandoc('/bin/notpandoc');
    }

    public function testBasicMarkdownToHTML()
    {
        $pandoc = new Pandoc();
        $this->assertEquals(
            '<h1>Test Heading</h1>',
            $pandoc->convert("#Test Heading", "markdown_github", "html")
        );
    }
}
