<?php
/**
 * Pandoc PHP
 *
 * Copyright (c) Ryan Kadwell <ryan@riaka.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
