<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Io;

use ArrayIterator;
use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class PotWriterTest extends TestCase
{
    private PotWriter $subject;

    private vfsStreamDirectory $vfsStream;

    protected function setUp(): void
    {
        $this->subject = new PotWriter();

        $this->vfsStream = vfsStream::setup('/');
    }

    public function testWriteFailsIfDestinationDoesNotExist(): void
    {
        $destination = $this->vfsStream->url() . '/snafu';

        static::expectException(InvalidArgumentException::class);
        static::expectExceptionMessage(sprintf('Cannot open `%s` for writing', $destination));

        $this->subject->write(
            new ArrayIterator(),
            $destination
        );
    }

    public function testWriteFailsIfDestinationIsNotWriteable(): void
    {
        $filename = 'snafu';
        $file = new vfsStreamFile($filename, '0000');

        $this->vfsStream->addChild($file);

        $destination = $file->url();

        static::expectException(InvalidArgumentException::class);
        static::expectExceptionMessage(sprintf('Cannot open `%s` for writing', $destination));

        $this->subject->write(
            new ArrayIterator(),
            $destination
        );
    }

    public function testWriteWritesToStream(): void
    {
        $filename = 'snafu';
        $file = new vfsStreamFile($filename);

        $this->vfsStream->addChild($file);

        $destination = $file->url();

        $this->subject->write(
            new ArrayIterator(['content', 'content', 'snafu',]),
            $destination
        );

        static::assertSame(
            <<<MSG
                msgid "content"
                msgstr "content"\n
                msgid "snafu"
                msgstr "snafu"\n\n
                MSG,
            file_get_contents($file->url())
        );
    }
}
