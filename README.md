# makesweat-php
Makesweat client library for PHP using OpenActive API standard

## Requirements

PHP 5.4.0 and later.

## Manual Installation

Download the [latest release](https://github.com/makesweat/makesweat-php/releases).

## Getting Started

Simple usage looks like:

```php
\Stripe\Stripe::setApiKey('sk_test_BQokikJOvBiI2HlWgH4olfQ2');
$charge = \Stripe\Charge::create(['amount' => 2000, 'currency' => 'usd', 'source' => 'tok_189fqt2eZvKYlo2CTGBeg6Uq']);
echo $charge;
```

```
require(dirname(__FILE__) . '/Makesweat.php');

\Makesweat\Makesweat::setClientToken("gfjreisgu458typo");

$myorder = [
  "uniqueOffer" => 6437,
  "givenName" => "John",
  "familyName" => "Smith",
  "emailAddress" => "johnsmithtest@makesweat.com"
];

\Makesweat\Makesweat::order($myorder);
\Makesweat\Makesweat::confirm();

```
