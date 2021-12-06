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
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

/**
 * @author Ryan Kadwell <ryan@riaka.ca>
 */
class PandocTest extends \PHPUnit\Framework\TestCase
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        $this->pandoc = new Pandoc();
    }

    public function testInvalidPandocCommandThrowsException()
    {
        $this->expectException(\Pandoc\PandocException::class);
        $pandoc = new Pandoc('/bin/notpandoc');
    }

    public function testUnWritableTempDirThrowsException()
    {
        $this->expectException(\Pandoc\PandocException::class);
        // Assuming that were not running with write access to the /usr dir
        $pandoc = new Pandoc(null, '/usr');
    }

    public function testInvalidTmpDirectoryThrowsException()
    {
        $this->expectException(\Pandoc\PandocException::class);
        $pandoc = new Pandoc(null, '/this-is-probably-not-a-valid-directory');
    }

    public function testCanSetDifferentTempDir()
    {
        $pandoc = new Pandoc(null, '/tmp');
        $this->assertTrue($pandoc->getTmpDir() === '/tmp');
    }

    public function testInvalidFromTypeTriggersException()
    {
        $this->expectException(\Pandoc\PandocException::class);
        $this->pandoc->convert("#Test Content", "not_value", "plain");
    }

    public function testInvalidToTypeTriggersException()
    {
        $this->expectException(\Pandoc\PandocException::class);
        $this->pandoc->convert("#Test Content", "html", "not_valid");
    }

    public function testBasicMarkdownToHTML()
    {
        if (false === strpos($this->pandoc->getVersion(), '1.12.')) {
            $this->markTestSkipped(
                'This test requires pandoc version 1.12'
            );
        }

        $this->assertEquals(
            '<h1 id="test-heading">Test Heading</h1>',
            $this->pandoc->convert("#Test Heading", "markdown_github", "html")
        );
    }

    public function testRunWithConvertsBasicMarkdownToJSON()
    {
        if (false === strpos($this->pandoc->getVersion(), '1.12.')) {
            $this->markTestSkipped(
                'This test requires pandoc version 1.12'
            );
        }

        $options = array(
            'from' => 'markdown',
            'to'   => 'json'
        );

        $this->assertEquals(
            '[{"unMeta":{}},[{"t":"Header","c":[1,["heading",[],[]],[{"t":"Str","c":"Heading"}]]}]]',
            $this->pandoc->runWith('#Heading', $options)
        );
    }

    public function testCanConvertMultipleSuccessfully()
    {
        $this->pandoc->convert(
            "# Heading 1\n## Heading 2",
            "markdown",
            "html"
        );

        $this->assertEquals(
            "<h3 id=\"heading-3\">Heading 3</h3>\n<h4 id=\"heading-4\">Heading 4</h4>",
            $this->pandoc->convert(
                "### Heading 3\n#### Heading 4",
                "markdown",
                "html"
            )
        );
    }
}
