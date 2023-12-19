<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Io;

use DOMDocument;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\ExtractorDecorator;

final class FileLoader implements FileLoaderInterface
{
    /**
     * @param iterable<string> $filePaths
     *
     * @return Generator<DOMXPath>
     */
    public function read(
        iterable $filePaths
    ): Generator {
        foreach ($filePaths as $path) {
            $path = (string) realpath($path);

            if (!file_exists($path)) {
                trigger_error('Not existing: '.$path, E_USER_WARNING);

                continue;
            }

            $content = file_get_contents($path);
            if ($content === false) {
                trigger_error('Not readable: '.$path, E_USER_WARNING);

                continue;
            }

            $dom = new DOMDocument();

            if (@$dom->loadXML($content) === false) {
                trigger_error('Does not contain valid xml: '.$path, E_USER_WARNING);

                continue;
            }

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('i18n', ExtractorDecorator::I18N_NAMESPACE);

            yield $xpath;
        }
    }
}
