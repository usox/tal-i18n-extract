<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract;

use DOMDocument;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\Extractors\ExtractorInterface;
use Usox\TalI18nExtract\Extractors\I18nTranslateEmptyExtractor;
use Usox\TalI18nExtract\Extractors\I18nTranslateKeyExtractor;

final class Extractor
{
    /** @var list<ExtractorInterface> */
    private array $extractors = [];

    public function __construct()
    {
        $this->extractors = [
            new I18nTranslateKeyExtractor(),
            new I18nTranslateEmptyExtractor(),
        ];
    }

    /**
     * @return Generator<string>
     */
    public function run(string $data): Generator
    {
        $dom = new DOMDocument();
        $dom->loadXML($data);

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('i18n', 'http://xml.zope.org/namespaces/i18n');

        foreach ($this->extractors as $extractor) {
            yield from $extractor->extract($xpath);
        }
    }
}
