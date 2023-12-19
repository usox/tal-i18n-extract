<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMNode;
use DOMNodeList;
use DOMXPath;
use Generator;
use Usox\TalI18nExtract\ExtractorDecorator;

/**
 * Extracts translation-keys from i18n attributes
 *
 * e.g. <div title="Some text" i18n:attributes="title">content</div>
 * Will also work when the translation-key is defined within the attribute like
 * <img src="some-url" i18n:attributes="title my-translation-key;alt" alt="Nice view" />
 */
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
                $node = $item->attributes?->getNamedItemNS(ExtractorDecorator::I18N_NAMESPACE, 'attributes');

                if ($node === null) {
                    continue;
                }

                // split up all attributes which are separated by a semicolon
                $tuples = explode(';', (string) $node->nodeValue);

                foreach ($tuples as $tuple) {
                    $tuple = trim($tuple);

                    // see if the translation-key is defined within the attribute
                    $attribute = preg_split('/\s/', $tuple);

                    if ($attribute === false) {
                        continue;
                    }

                    // we have attribute-name and translation-key (`i18n:attributes="attribute_name translation-key"`)
                    if (count($attribute) === 2) {
                        [$_, $translationKey] = $attribute;

                        // yield only the translation-key if defined within the attribute
                        yield $translationKey;
                    } else {
                        // yield the content of the attribute referred to by the i18n-attribute
                        yield (string) $item->attributes?->getNamedItem($tuple)?->nodeValue;
                    }
                }
            }
        }
    }
}
