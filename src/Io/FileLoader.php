<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Io;

use DOMDocument;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\ExtractorDecorator;

/**
 * Gets file-paths and returns fully-loaded XPath-objects one-by-one
 */
final class FileLoader implements FileLoaderInterface
{
    /**
     * Loads every given-file path and return a XPath object
     *
     * @param iterable<string> $filePaths
     *
     * @return Generator<DOMXPath>
     */
    public function read(
        iterable $filePaths
    ): Generator {
        foreach ($filePaths as $path) {
            if (!file_exists($path)) {
                trigger_error('Not existing: '.$path, E_USER_WARNING);

                continue;
            }

            $content = @file_get_contents($path);
            if ($content === false) {
                trigger_error('Not readable: '.$path, E_USER_WARNING);

                continue;
            }

            $dom = new DOMDocument();

            if (@$dom->loadXML($content) === false) {
                trigger_error('Does not contain valid xml: '.$path, E_USER_WARNING);

                continue;
            }

            yield new DOMXPath($dom);
        }
    }
}
