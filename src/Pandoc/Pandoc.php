<?php
/**
 * Pandoc PHP
 *
 * Copyright (c) Ryan Kadwell <ryan@riaka.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pandoc;

/**
 * Naive wrapper for haskell's pandoc utility
 *
 * @author Ryan Kadwell <ryan@riaka.ca>
 */
class Pandoc
{
    /**
     * Where is the executable located
     * @var string
     */
    private $executable;

    /**
     * Where to take the content for pandoc from
     * @var string
     */
    private $tmpFile;

    /**
     * List of valid input types
     * @var array
     */
    private $inputFormats = array(
        "native",
        "json",
        "markdown",
        "markdown_strict",
        "markdown_phpextra",
        "markdown_github",
        "markdown_mmd",
        "rst",
        "mediawiki",
        "docbook",
        "textile",
        "html",
        "latex"
    );

    /**
     * List of valid output types
     * @var array
     */
    private $outputFormats = array(
        "native",
        "json",
        "docx",
        "odt",
        "epub",
        "epub3",
        "fb2",
        "html",
        "html5",
        "s5",
        "slidy",
        "slideous",
        "dzslides",
        "docbook",
        "opendocument",
        "latex",
        "beamer",
        "context",
        "texinfo",
        "man",
        "markdown",
        "markdown_strict",
        "markdown_phpextra",
        "markdown_github",
        "markdown_mmd",
        "plain",
        "rst",
        "mediawiki",
        "textile",
        "rtf",
        "org",
        "asciidoc"
    );

    /**
     * Setup path to the pandoc binary
     *
     * @param string $executable Path to the pandoc executable
     */
    public function __construct($executable = null)
    {
        $this->tmpFile = sprintf(
            "%s/%s", sys_get_temp_dir(), uniqid("pandoc")
        );

        // Since we can not validate that the command that they give us is
        // *really* pandoc we will just check that its something.
        // If the provide no path to pandoc we will try to find it on our own
        if ( ! $executable) {
            exec('which pandoc', $output, $returnVar);
            if ($returnVar === 0) {
                $this->executable = $output[0];
            } else {
                throw new PandocException('Unable to locate pandoc');
            }
        } else {
            $this->executable = $executable;
        }

        if ( ! is_executable($this->executable)) {
            throw new PandocException('Pandoc executable is not executable');
        }
    }

    /**
     * Run the conversion from one type to another
     *
     * @param string $from The type we are converting from
     * @param string $to   The type we want to convert the document to
     *
     * @return string
     */
    public function convert($content, $from, $to)
    {
        if ( ! in_array($from, $this->inputFormats)) {
            throw new PandocException(
                sprintf('%s is not a valid input format for pandoc', $to)
            );
        }

        if ( ! in_array($to, $this->outputFormats)) {
            throw new PandocException(
                sprintf('%s is not a valid output format for pandoc', $to)
            );
        }

        file_put_contents($this->tmpFile, $content);

        $command = sprintf(
            '%s --from=%s --to=%s %s',
            $this->executable,
            $from,
            $to,
            $this->tmpFile
        );

        exec($command, $output);

        return implode("\n", $output);
    }

    /**
     * Run the pandoc command with specific options.
     *
     * Provides more control over what happens. You simply pass an array of
     * key value pairs of the command options omitting the -- from the start.
     * If you want to pass a command that takes no argument you set its value
     * to null.
     *
     * @param string $content The content to run the command on
     * @param array  $options The options to use
     *
     * @return string The returned content
     */
    public function runWith($content, $options)
    {
        $commandOptions = array();
        foreach ($options as $key => $value) {
            if (null === $value) {
                $commandOptions[] = "--$key";
                continue;
            }

            $commandOptions[] = "--$key=$value";
        }

        file_put_contents($this->tmpFile, $content);

        $command = sprintf(
            "%s %s %s",
            $this->executable,
            implode(' ', $commandOptions),
            $this->tmpFile
        );

        exec($command, $output);

        return implode("\n", $output);
    }

    /**
     * Remove the temporary files that were created
     */
    public function __destruct()
    {
        if (file_exists($this->tmpFile)) {
            @unlink($this->tmpFile);
        }
    }
}
