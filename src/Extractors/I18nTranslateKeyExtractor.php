<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMNode;
use DOMNodeList;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\ExtractorDecorator;

/**
 * Extracts translation-keys defined as value of i18n:translate-attributes
 *
 * e.g. <div i18n:translate="my-translation-key">content</div>
 */
final class I18nTranslateKeyExtractor implements ExtractorInterface
{
    /**
     * @return Generator<string>
     */
    public function extract(DOMXPath $xpath): Generator
    {
        $result = $xpath->query('//*[@i18n:translate[string-length()!=0]]');

        if ($result instanceof DOMNodeList) {
            /** @var DOMNode $item */
            foreach ($result as $item) {
                $node = $item->attributes?->getNamedItemNS(ExtractorDecorator::I18N_NAMESPACE, 'translate');

                if ($node !== null) {
                    yield (string) $node->nodeValue;
                }
            }
        }
    }
}
