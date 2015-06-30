Address Format
=============

[![Latest Version](https://img.shields.io/github/tag/adamlc/address-format.svg?label=release&style=flat-square)](https://github.com/adamlc/address-format/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/adamlc/address-format/master.svg?style=flat-square)](https://travis-ci.org/adamlc/address-format)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/adamlc/address-format.svg?style=flat-square)](https://scrutinizer-ci.com/g/adamlc/address-format/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/adamlc/address-format.svg?style=flat-square)](https://scrutinizer-ci.com/g/adamlc/address-format)
[![Total Downloads](https://img.shields.io/packagist/dt/adamlc/address-format.svg?style=flat-square)](https://packagist.org/packages/adamlc/address-format)

A PHP library to parse street addresses to localized formats. The address formats are based on the formats supplied by Google's libaddressinput.

I have written a few basic unit tests, but they could probably be improved. Feel free to submit a pull request if you improve them!


## Composer

To install AddressFormat as a Composer package add this to your composer.json:

```json
"adamlc/address-format": "~1.3"
```

Run `composer update`


## Formatting a Street Address

```php
//Create an address formatter instance
$address_formatter = new Adamlc\AddressFormat\Format;

//Set a locale using a two digit ISO country code.
$address_formatter->setLocale('GB');

//Set the address parts / attributes
$address_formatter['ADMIN_AREA'] = 'London';
$address_formatter['LOCALITY'] = 'Greenwich';
$address_formatter['RECIPIENT'] = 'Joe Bloggs';
$address_formatter['ORGANIZATION'] = 'Novotel London';
$address_formatter['POSTAL_CODE'] = 'SE10 8JA';
$address_formatter['STREET_ADDRESS'] = '173-185 Greenwich High Road';
$address_formatter['COUNTRY'] = 'United Kingdom';

//Get the address in localised format
$html = true; // Optional - return the address in HTML <br> instead of \n new lines

echo $address_formatter->formatAddress($html);
```


The above code will produce the following:

```
Joe Bloggs
Novotel London
173-185 Greenwich High Road
Greenwich
London
SE10 8JA
```


Note: Look in the i18n directory to view the meta data for the locales.


The following attributes are available:

ADMIN_AREA  
LOCALITY  
RECIPIENT  
ORGANIZATION  
DEPENDENT_LOCALITY  
POSTAL_CODE  
SORTING_CODE  
STREET_ADDRESS  
COUNTRY  
