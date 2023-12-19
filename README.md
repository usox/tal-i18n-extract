# tal-i18n-extract

[![Unittests](https://github.com/usox/tal-i18n-extract/actions/workflows/php.yml/badge.svg)](https://github.com/usox/tal-i18n-extract/actions/workflows/php.yml)

Extract translation keys out of [PHPTAL](https://github.com/phptal/PHPTAL) templates.

This is a php port of the [I18NFool](https://metacpan.org/dist/I18NFool) CPAN perl-package.

## Usage

```php
./bin/tal-i18n-extract path-to-file(s).xhtml
```

All found translation keys will be printed out POT-formatted.

## Beware

All templates must contain valid xml and have to define the i18n-namespace (see example).
