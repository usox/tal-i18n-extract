<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract;

use DOMDocument;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\Extractors\ExtractorInterface;
use Usox\TalI18nExtract\Extractors\I18nAttributeExtractor;
use Usox\TalI18nExtract\Extractors\I18nTranslateEmptyExtractor;
use Usox\TalI18nExtract\Extractors\I18nTranslateKeyExtractor;

final class Extractor
{
    /** @var string */
    public const I18N_NAMESPACE = 'http://xml.zope.org/namespaces/i18n';

    /** @var list<ExtractorInterface> */
    private array $extractors;

    /**
     * @param list<ExtractorInterface>|null $extractors
     */
    public function __construct(
        ?array $extractors = null
    ) {
        $this->extractors = $extractors ?? [
            new I18nTranslateKeyExtractor(),
            new I18nTranslateEmptyExtractor(),
            new I18nAttributeExtractor(),
        ];
    }

    /**
     * @return Generator<non-empty-string>
     */
    public function run(string $data): Generator
    {
        $dom = new DOMDocument();
        $dom->loadXML($data);

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('i18n', self::I18N_NAMESPACE);

        yield from $this->extract($xpath);
    }

    /**
     * @return Generator<non-empty-string>
     */
    private function extract(DOMXPath $xpath): Generator
    {
        foreach ($this->extractors as $extractor) {
            /** @var string $translationKey */
            foreach ($extractor->extract($xpath) as $translationKey) {
                $value = $this->normalize($translationKey);
                if ($value !== '') {
                    yield $value;
                }
            }
        }
    }

    private function normalize(string $value): string
    {
        return htmlspecialchars(
            trim(
                (string) preg_replace(['/\n/', '/\r/', '/\t/'], [' ', ' ', ''], $value)
            )
        );
    }
}
