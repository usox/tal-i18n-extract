<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMDocument;
use DOMXPath;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class I18nTranslateKeyExtractorTest extends TestCase
{
    private I18nTranslateKeyExtractor $subject;

    protected function setUp(): void
    {
        $this->subject = new I18nTranslateKeyExtractor();
    }

    #[DataProvider(methodName: 'translationKeyDataProvider')]
    public function testExtractExtractsKeys(string $html, array $expectedKeys): void
    {
        $body = <<<HTML
            <html xmlns:i18n="http://xml.zope.org/namespaces/i18n">%s</html>
            HTML;

        $dom = new DOMDocument();
        $dom->loadXML(sprintf($body, $html));

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('i18n', 'http://xml.zope.org/namespaces/i18n');

        static::assertSame(
            $expectedKeys,
            iterator_to_array(
                $this->subject->extract($xpath)
            )
        );
    }

    public static function translationKeyDataProvider(): Generator
    {
        yield [
            '<div i18n:translate="">snafu</div>',
            [],
        ];

        yield [
            <<<HTML
                <div>
                <div i18n:translate="">snafu</div>
                <span i18n:translate="">foobar</span>
                </div>
                HTML,
            [],
        ];

        yield [
            '<div i18n:translate="snafu">meh</div>',
            ['snafu'],
        ];

        yield [
            <<<HTML
                <div>
                <div i18n:translate="bar">snafu</div>
                <span i18n:translate="baz">foobar</span>
                </div>
                HTML,
            ['bar', 'baz'],
        ];
    }
}
