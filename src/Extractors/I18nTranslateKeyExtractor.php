<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMNode;
use DOMNodeList;
use DOMXPath;
use Generator;

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
                $node = $item->attributes?->getNamedItemNS('http://xml.zope.org/namespaces/i18n', 'translate');

                if ($node !== null) {
                    yield (string) $node->nodeValue;
                }
            }
        }
    }
}
