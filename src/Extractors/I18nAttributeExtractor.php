<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMNode;
use DOMNodeList;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\Extractor;

final class I18nAttributeExtractor implements ExtractorInterface
{
    /**
     * @return Generator<string>
     */
    public function extract(DOMXPath $xpath): Generator
    {
        $result = $xpath->query('//*[@i18n:attributes]');

        if ($result instanceof DOMNodeList) {
            /** @var DOMNode $item */
            foreach ($result as $item) {
                $node = $item->attributes?->getNamedItemNS(Extractor::I18N_NAMESPACE, 'attributes');

                if ($node === null) {
                    continue;
                }

                $tuples = preg_split('/;/', (string) $node->nodeValue);
                if ($tuples === false) {
                    continue;
                }

                foreach ($tuples as $tuple) {
                    $tuple = trim($tuple);
                    $attribute = preg_split('/\s/', $tuple);

                    if ($attribute === false) {
                        continue;
                    }

                    if (count($attribute) === 2) {
                        [$_, $translationKey] = $attribute;

                        yield $translationKey;
                    } else {
                        yield (string) $item->attributes?->getNamedItem($tuple)?->nodeValue;
                    }
                }
            }
        }
    }
}
