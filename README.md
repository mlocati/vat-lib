[![Tests](https://github.com/mlocati/vat-lib/actions/workflows/tests.yml/badge.svg)](https://github.com/mlocati/vat-lib/actions/workflows/tests.yml)

# A PHP library to validate and normalize and VAT numbers

This project provides a library that checks and normalizes VAT numbers (including querying the [VIES VoW](https://ec.europa.eu/taxation_customs/vies) (VAT Information Exchange System - Vies-on-the-web service for European countries).


## Requirements

VATLib has these requirements:

- It works with any PHP version starting from 5.5 (PHP **5.5.x**, **5.6.x**, **7.x**, and **8.x** are fully supported).
- Very flexible dependencies for querying online services like VIES:
  - the SOAP extension is **not** required
  - the library automatically uses these adapters (you only need one, not all):
    - the [Guzzle library](https://github.com/guzzle/guzzle)
    - the [PHP cURL extension](https://www.php.net/manual/en/book.curl.php)
    - the [Zend HTTP library](https://github.com/zendframework/zend-http)
    - the [PHP `http` stream wrapper](https://www.php.net/manual/en/wrappers.php)
- Checking the syntax of VAT numbers of some countries (Netherlands and Slovakia) requires handling of large numbers: on 32 bit systems you may need the `bcmath` or the `gmp` PHP extension.


## Installation

### Manual installation

[Download](https://github.com/mlocati/vat-lib/releases) the latest version, unzip it and add these lines in our PHP files:

```php
require_once 'path/to/vatlib/vat-lib.php';
```

### Installation with Composer

Simply run:

```sh
composer require mlocati/vat-lib
```

or add these lines to your `composer.json` file:

```json
"require": {
    "mlocati/vat-lib": "^1"
}
```

## Checking and normalizing VAT numbers

You can use the `VATLib\Checker` class to validate and normalize VAT numbers.

You can obtain an instance of it simply with:

```php
$checker = new \VATLib\Checker();
```

You can then use its `check` method.
This method accepts one to two arguments: the first argument is the VAT number to be checked, the second argument (optional) is the [ISO 3166](https://www.iso.org/iso-3166-country-codes.html) alpha-2 code of the Country associated to that VAT number.

Some examples (that lead to the same *valid* result):

```php
$checkResult = $checker->check('IT00159560366');
$checkResult = $checker->check('00159560366', 'IT');
$checkResult = $checker->check('IT00159560366', 'IT');
$checkResult = $checker->check('IT 00159560366');
$checkResult = $checker->check('IT 001-595-603-66');
$checkResult = $checker->check('00.15-95 603  66', 'IT');
```

### Verifying the validity

`$checkResult` provides the `isSyntaxValid()` method, which returns:

- `true` if the VAT number is syntactically valid
- `false` if the VAT number is syntactically invalid
- `null` if it's impossible to verify if the VAT number is syntactically valid. Some examples of this case are:
  - you provided a country code that's not supported (or that's invalid)
  - you didn't provide a country code and it's not possible to determine the country associated to the VAT number provided (for example if you call `$checker->check('00159560366')`)

When the VAT number is syntactically valid, the library also queries the VIES service for EU countries.

You can check the VIES response with some code like this:

```php
$viesCheck = $checker->getViesResult();
if ($viesCheck === null) {
    // The VIES service has not been queried, or an error occurred while querying the VIES service.
} elseif ($viesCheck->isValid() === true) {
    // The VIES service told us that the VAT number is valid
} elseif ($viesCheck->isValid() === false) {
    // The VIES service told us that the VAT number is NOT valid
    // That is, even if the VAT number is syntactically valid, it's not actually valid.
}
```

`$checkResult` provides a quick way to check both `isSyntaxValid()` and the VIES result: you can use its `isValid()` method, which returns:

- `true` if the VAT number is syntactically valid, and the VIES was not queried or its response is successful
- `false` if the VAT number is syntactically invalid, or the VIES service told us that it's not a valid
- `null` if it's impossible to verify if the VAT number is valid

### Retrieving the formatted VAT number

To obtain the *normalized* VAT number, you can use the `getShortVatNumber()` or the `getLongVatNumber()` methods of `$checkResult`:

```php
$checkResult = $checker->check('IT 00-15 956.0366');
echo $checkResult->getShortVatNumber();
// prints 00159560366
echo $checkResult->getLongVatNumber();
// prints IT00159560366
```

### Getting VAT number info

You can obtain some details about a VAT number:

```php
$checker = new \VATLib\Checker();
$checkResult = $checker->check('EL999080536');
$format = $checkResult->getFormat();
if ($format === null) {
    echo 'The VAT number is not valid';
} else {
    echo "The VAT number is for the country with ISO 3166 alpha-2 code {$format->getCountryCode()}";
}
```

The code above would print:

```
The VAT number is for the country with ISO 3166 alpha-2 code GR
```


### Checking for errors

Unexpected problems may occur when checking VAT numbers (for example, because the VIES service is temporarily not available).

You can check if any errors occurred using the `hasExceptions` method of `$checkResult`:

```php
if ($checkResult->hasExceptions()) {
    echo "The following errors occurred:\n";
    foreach ($checkResult->getExceptions() as $exception) {
        echo "- {$exception->getMessage()}\n";
    }
}
```

## Detecting the possible countries given a VAT number

If you have a generic VAT number and you would like to know the possible countries where it may be defined, you can use the `getApplicableFormats()` method of the `VATLib\Checker` class:

```php
$checker = new \VATLib\Checker();
$formats = $checker->getApplicableFormats('00159560366');
if ($formats === []) {
    echo 'The VAT number is not valid';
} else {
    echo "The VAT number may be used in these countries:\n";
    foreach ($formats as $format) {
        echo "- {$format->getCountryCode()}\n";
    }
}
```

The code above would print:

```
The VAT number may be used in these countries:
- FR
- HR
- IT
- LV
```


## Checking the status of the VIES service

```php
$viesClient = new \VATLib\Vies\Client();
$status = $viesClient->checkStatus();
if ($status->getVowStatus()->isAvailable()) {
    echo "The Vies-on-the-web service is available\n";
} else {
    echo "The Vies-on-the-web service is NOT available\n";
}

$countryCodes = $status->getCountryCodes();
echo 'Vies supports these countries: ', implode(', ', $countryCodes), "\n";
// Sample outout: AT, BE, BG, CY, CZ, DE, DK, EE, EL, ES, FI, FR, HR, HU, IE, IT, LT, LU, LV, MT, NL, PL, PT, RO, SE, SI, SK, XI

$countryStatus = $status->getCountryStatus('IT');
if ($countryStatus->isAvailable()) {
    echo "Italian VAT validation is available\n";
} else {
    echo "Italian VAT validation is NOT available (", $countryStatus->getAvailability(), ")\n";
}
```


## Manually querying VIES

You can also query VIES directly:

```php
$viesClient = new \VATLib\Vies\Client();
$request = new \VATLib\Vies\CheckVat\Request('IT', '00159560366');
$response = $viesClient->checkVatNumber($request);
if ($response->isValid()) {
    echo "The VAT number {$request->getCountryCode()}{$request->getVatNumber()} is correct: it's assigned to the '{$response->getName()}' company\n";
} else {
    echo "The VAT number {$request->getCountryCode()}{$request->getVatNumber()} is NOT correct\n";
}
```

The code above may output:

```
The VAT number IT00159560366 is correct: it's assigned to the 'FERRARI S.P.A.' company
```


## Customizing the HTTP adapter for VIES

By default, VATLib automatically detects the available adapter, but of course you can specify your own.

For example, if you are using Guzzle and you need to configure a proxy, you can write something like this:

```php
$guzzle = new \GuzzleHttp\Client([
    'proxy' => 'tcp://username:password@192.168.16.1:10',
]);
$adapter = new \VATLib\Http\Adapter\Guzzle($guzzle);
$viesClient = new \VATLib\Vies\Client($adapter);
$checker = new \VATLib\Checker();
$checker->setViesClient($viesClient);
```

The same can be done using the cURL PHP extension:

```php
$adapter = new \VATLib\Http\Adapter\Curl([
    CURLOPT_PROXY => 'tcp://username:password@192.168.16.1:10',
]);
// ... same previous example
```

The same can be done using the `http` stream wrapper:

```php
$adapter = new \VATLib\Http\Adapter\Stream([
    'proxy' => 'tcp://username:password@192.168.16.1:10',
    'request_fulluri' => true,
]);
// ... same previous example
```


## Supported countries

The library supports these countries:

- all the European Union Countries:
  - Austria (`AT`)
  - Belgium (`BE`)
  - Bulgaria (`BG`)
  - Croatia (`HR`)
  - Cyprus (`CY`)
  - Czech (`CZ`)
  - Denmark (`DK`)
  - Estonia (`EE`)
  - Finland (`FI`)
  - France (`FR`)
  - Germany (`DE`)
  - Greece (`GR` country code, VAT numbers starting with `EL`)
  - Hungary (`HU`)
  - Ireland (`IE`)
  - Italy (`IT`)
  - Latvia (`LV`)
  - Lithuania (`LT`)
  - Luxembourg (`LU`)
  - Malta (`MT`)
  - Netherlands (`NL`)
  - Poland (`PL`)
  - Portugal (`PT`)
  - Romania (`RO`)
  - Slovakia (`SK`)
  - Slovenia (`SI`)
  - Spain (`ES`)
  - Sweden (`SE`)
- Switzerland (both `CHE-123.456.789` and `123.456.789` UID numbers)
- United Kingdom (`GB`), with the following extension:
  - for VAT numbers starting with `XI`, the VIES service will be queried accordingly to the Protocol on Ireland and Northern Ireland
