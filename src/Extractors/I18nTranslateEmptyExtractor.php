<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMNode;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\ExtractorDecorator;

/**
 * Extracts the content of empty translation attributes
 *
 * e.g. <div i18n:translate="">My text</div>
 * Does also support variables like
 * <div i18n:translate="">Some text <span i18n:name="user">user</span></div>
 */
final class I18nTranslateEmptyExtractor implements ExtractorInterface
{
    /**
     * @return Generator<string>
     */
    public function extract(DOMXPath $xpath): Generator
    {
        $result = $xpath->query('//*[@i18n:translate=""]');

        if ($result !== false) {
            /** @var DOMNode $item */
            foreach ($result as $item) {
                $tokens = [];

                /** @var DOMNode $childNode */
                foreach ($item->childNodes as $childNode) {
                    // Simply add text-nodes
                    if ($childNode->nodeType === XML_TEXT_NODE) {
                        $tokens[] = $childNode->nodeValue;

                        continue;
                    }

                    // other nodes may contain a name-attribute. add it with as a placeholder - if available
                    $attribute = $childNode->attributes?->getNamedItemNS(ExtractorDecorator::I18N_NAMESPACE, 'name');
                    if ($attribute !== null) {
                        $tokens[] = sprintf('${%s}', $attribute->nodeValue);
                    }
                }

                // merge all tokens
                yield implode('', $tokens);
            }
        }
    }
}
