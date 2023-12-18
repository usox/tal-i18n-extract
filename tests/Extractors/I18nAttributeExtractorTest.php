<?php

declare(strict_types=1);

namespace Usox\TalI18nExtract\Extractors;

use DOMDocument;
use DOMXPath;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class I18nAttributeExtractorTest extends TestCase
{
    private I18nAttributeExtractor $subject;

    protected function setUp(): void
    {
        $this->subject = new I18nAttributeExtractor();
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

    /**
     * @return Generator<array{string, list<string>}>
     */
    public static function translationKeyDataProvider(): Generator
    {
        yield [
            '<div i18n:translate="">snafu</div>',
            [],
        ];

        yield [
            '<div i18n:attributes="alt snafu">foobar</div>',
            ['snafu'],
        ];

        yield [
            '<img i18n:attributes="alt" alt="snafu">foobar</img>',
            ['snafu'],
        ];

        yield [
            '<img i18n:attributes="alt; title" title="bar" alt="baz">foobar</img>',
            ['baz', 'bar'],
        ];
    }
}
