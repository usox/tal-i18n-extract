<?php

namespace Usox\TalI18nExtract\Io;

use DOMXPath;
use Generator;

interface FileLoaderInterface
{
    /**
     * Loads every given-file path and return a XPath object
     *
     * @param iterable<string> $filePaths
     *
     * @return Generator<DOMXPath>
     */
    public function read(iterable $filePaths): Generator;
}
