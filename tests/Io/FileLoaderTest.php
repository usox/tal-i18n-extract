<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Io;

use DOMXPath;
use Exception;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class FileLoaderTest extends TestCase
{
    private vfsStreamDirectory $vfsStream;

    private FileLoader $subject;

    protected function setUp(): void
    {
        $this->subject = new FileLoader();

        $this->vfsStream = vfsStream::setup('/');

        set_error_handler(static function (int $errno, string $errstr): never {
            throw new Exception($errstr, $errno);
        }, E_USER_WARNING);
    }

    public function testReadFailsIfFileDoesNotExist(): void
    {
        $filename = $this->vfsStream->url() . '/snafu';

        static::expectException(Exception::class);
        static::expectExceptionMessage('Not existing: '.$filename);

        static::assertSame(
            [],
            iterator_to_array($this->subject->read([$filename]))
        );
    }

    public function testReadFailsIfFileIsNotReadable(): void
    {
        $file = new vfsStreamFile('snafu', 0o000);

        $this->vfsStream->addChild($file);

        $filename = $file->url();

        static::expectException(Exception::class);
        static::expectExceptionMessage('Not readable: '.$filename);

        static::assertSame(
            [],
            iterator_to_array($this->subject->read([$filename]))
        );
    }

    public function testReadFailsIfFileDoesNotContainValidXml(): void
    {
        $file = new vfsStreamFile('snafu');

        $this->vfsStream->addChild($file);

        $filename = $file->url();

        file_put_contents($filename, 'snafu');

        static::expectException(Exception::class);
        static::expectExceptionMessage('Does not contain valid xml: '.$filename);

        static::assertSame(
            [],
            iterator_to_array($this->subject->read([$filename]))
        );
    }

    public function testReadReturnsPreparedXPathObject(): void
    {
        $file = new vfsStreamFile('snafu');

        $this->vfsStream->addChild($file);

        $filename = $file->url();

        file_put_contents($filename, '<?xml version="1.0" encoding="utf-8"?><root>foobar</root>');

        $xpath = current(
            iterator_to_array($this->subject->read([$filename]))
        );

        static::assertInstanceOf(
            DOMXPath::class,
            $xpath
        );
    }

    protected function tearDown(): void
    {
        restore_error_handler();
    }
}
