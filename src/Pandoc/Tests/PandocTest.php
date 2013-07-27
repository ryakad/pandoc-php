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
    public function setup()
    {
        $this->pandoc = new Pandoc();
    }

    /**
     * @expectedException Pandoc\PandocException
     */
    public function testInvalidPandocCommandThrowsException()
    {
        $pandoc = new Pandoc('/bin/notpandoc');
    }

    /**
     * @expectedException Pandoc\PandocException
     */
    public function testInvalidFromTypeTriggersException()
    {
        $this->pandoc->convert("#Test Content", "not_value", "plain");
    }

    /**
     * @expectedException Pandoc\PandocException
     */
    public function testInvalidToTypeTriggersException()
    {
        $this->pandoc->convert("#Test Content", "html", "not_valid");
    }

    public function testBasicMarkdownToHTML()
    {
        $this->assertEquals(
            '<h1>Test Heading</h1>',
            $this->pandoc->convert("#Test Heading", "markdown_github", "html")
        );
    }

    public function testRunWithConvertsBasicMarkdownToHTML()
    {
        $options = array(
            'from' => 'markdown',
            'to'   => 'json'
        );

        $this->assertEquals(
            '[{"docTitle":[],"docAuthors":[],"docDate":[]},[{"Header":[1,["heading",[],[]],[{"Str":"Heading"}]]}]]',
            $this->pandoc->runWith('#Heading', $options)
        );
    }

    public function testCanConvertMultipleSuccessfully()
    {
        $this->pandoc->convert(
            "#Heading 1\n##Heading 2",
            "markdown",
            "html"
        );

        $this->assertEquals(
            "<h3 id=\"heading-3\">Heading 3</h3>\n<h4 id=\"heading-4\">Heading 4</h4>",
            $this->pandoc->convert(
                "###Heading 3\n####Heading 4",
                "markdown",
                "html"
            )
        );


    }
}
