<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMNode;
use DOMXPath;
use Generator;

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
                    if ($childNode->nodeType === XML_TEXT_NODE) {
                        $tokens[] = $childNode->nodeValue;

                        continue;
                    }

                    $attribute = $childNode->attributes?->getNamedItemNS('http://xml.zope.org/namespaces/i18n', 'name');
                    if ($attribute !== null) {
                        $tokens[] = sprintf('${%s}', $attribute->nodeValue);
                    }
                }

                yield implode('', $tokens);
            }
        }
    }
}
