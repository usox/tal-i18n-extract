<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMDocument;
use DOMXPath;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class I18nTranslateEmptyExtractorTest extends TestCase
{
    private I18nTranslateEmptyExtractor $subject;

    protected function setUp(): void
    {
        $this->subject = new I18nTranslateEmptyExtractor();
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
            ['snafu'],
        ];

        yield [
            <<<HTML
                <div>
                <div i18n:translate="">snafu</div>
                <span i18n:translate="">foobar</span>
                </div>
                HTML,
            ['snafu', 'foobar'],
        ];

        yield [
            '<div i18n:translate="snafu">meh</div>',
            [],
        ];

        yield [
            '<div i18n:translate="">This is <span i18n:name="sparta">what?!</span></div>',
            ['This is ${sparta}'],
        ];

        yield [
            '<div i18n:translate="">This is <span i18n:name="sparta">what?!</span> or <span i18n:name="wayne">who?!</span></div>',
            ['This is ${sparta} or ${wayne}'],
        ];
    }
}
