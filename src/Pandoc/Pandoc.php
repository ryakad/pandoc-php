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
     * Directory to store temporary files
     * @var string
     */
    private $tmpDir;

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
     * @param string $tmpDir     Path to where we want to store temporary files
     *
     * @throws PandocException
     */
    public function __construct($executable = null, $tmpDir = null)
    {
        if ( ! $tmpDir) {
            $tmpDir = sys_get_temp_dir();
        }

        if ( ! file_exists($tmpDir)) {
            throw new PandocException(
                sprintf('The directory %s does not exist!', $tmpDir)
            );
        }

        if ( ! is_writable($tmpDir)) {
            throw new PandocException(
                sprintf('Unable to write to the directory %s!', $tmpDir)
            );
        }

        $this->tmpDir = $tmpDir;

        $this->tmpFile = sprintf("%s/%s", $this->tmpDir, uniqid("pandoc"));

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

        // If the HOME env variable is not defined, pandoc fails, so we set a faked value
        if ( ! isset($_ENV['HOME'])) {
            putenv('HOME='.getcwd());
        }
    }

    /**
     * Run the conversion from one type to another
     *
     * @param string $content
     * @param string $from The type we are converting from
     * @param string $to   The type we want to convert the document to
     *
     * @return string
     * @throws PandocException
     */
    public function convert($content, $from, $to)
    {
        if ( ! in_array($from, $this->inputFormats)) {
            throw new PandocException(
                sprintf('%s is not a valid input format for pandoc', $from)
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
     * @throws PandocException
     */
    public function runWith($content, $options)
    {
        $commandOptions = array();

        $extFilesFormat = array(
            'docx',
            'odt',
            'epub',
            'fb2',
            'pdf'
        );

        $extFilesHtmlSlide = array(
            's5',
            'slidy',
            'dzslides',
            'slideous'
        );

        foreach ($options as $key => $value) {
            if ($key == 'to' && in_array($value, $extFilesFormat)) {
                $commandOptions[] = '-s -S -o '.$this->tmpFile.'.'.$value;
                $format = $value;
                continue;
            } else if ($key == 'to' && in_array($value, $extFilesHtmlSlide)) {
                $commandOptions[] = '-s -t '.$value.' -o '.$this->tmpFile.'.html';
                $format = 'html';
                continue;
            } else if ($key == 'to' && $value == 'epub3') {
                $commandOptions[] = '-S -o '.$this->tmpFile.'.epub';
                $format = 'epub';
                continue;
            } else if ($key == 'to' && $value == 'beamer') {
                $commandOptions[] = '-s -t beamer -o '.$this->tmpFile.'.pdf';
                $format = 'pdf';
                continue;
            } else if ($key == 'to' && $value == 'latex') {
                $commandOptions[] = '-s -o '.$this->tmpFile.'.tex';
                $format = 'tex';
                continue;
            } else if ($key == 'to' && $value == 'rst') {
                $commandOptions[] = '-s -t rst --toc -o '.$this->tmpFile.'.text';
                $format = 'text';
                continue;
            } else if ($key == 'to' && $value == 'rtf') {
                $commandOptions[] = '-s -o '.$this->tmpFile.'.'.$value;
                $format = $value;
                continue;
            } else if ($key == 'to' && $value == 'docbook') {
                $commandOptions[] = '-s -S -t docbook -o '.$this->tmpFile.'.db';
                $format = 'db';
                continue;
            } else if ($key == 'to' && $value == 'context') {
                $commandOptions[] = '-s -t context -o '.$this->tmpFile.'.tex';
                $format = 'tex';
                continue;
            } else if ($key == 'to' && $value == 'asciidoc') {
                $commandOptions[] = '-s -S -t asciidoc -o '.$this->tmpFile.'.txt';
                $format = 'txt';
                continue;
            }


            if (null === $value) {
                $commandOptions[] = "--$key";
                continue;
            }

            $commandOptions[] = "--$key=$value";
        }

        file_put_contents($this->tmpFile, $content);
        chmod($this->tmpFile, 0777);

        $command = sprintf(
            "%s %s %s",
            $this->executable,
            implode(' ', $commandOptions),
            $this->tmpFile
        );


        exec($command, $output, $returnval);
        if ($returnval === 0) {
            if (isset($format)) {
                return file_get_contents($this->tmpFile.'.'.$format);
            } else {
                return implode("\n", $output);
            }
        } else {
            throw new PandocException(
                sprintf(
                    'Pandoc could not convert successfully, error code: %s. Tried to run the following command: %s',
                    $returnval,
                    $command
                )
            );
        }
    }

    /**
     * Remove the temporary files that were created
     */
    public function __destruct()
    {
        if (file_exists($this->tmpFile)) {
            @unlink($this->tmpFile);
        }

        foreach (glob($this->tmpFile.'*') as $filename) {
            @unlink($filename);
        }
    }

    /**
     * Returns the pandoc version number
     *
     * @return string
     */
    public function getVersion()
    {
        exec(sprintf('%s --version', $this->executable), $output);

        return trim(str_replace('pandoc', '', $output[0]));
    }

    /**
     * Return that path where we are storing temporary files
     * @return string
     */
    public function getTmpDir()
    {
        return $this->tmpDir;
    }
}
