# Smart SMS Solutions ![CI status](https://img.shields.io/badge/build-passing-brightgreen.svg)

Smart SMS Solutions is a PHP library for sending messages with the [https://smartsmssolutions.com](https://smartsmssolutions.com) API

## Installation

### Requirements
* [PHP](https://php.net) 5.4+
* [HHVM](https://hhvm.com) 3.3+
* [Composer](https://getcomposer.org)

To get the latest version of this library, simply require it

``` 
$ composer require gbxnga/smart-sms-solutions 
```

Or you could add this to your `composer.json` file

```json
"gbxnga/smart-sms-solutions": "1.0.*"
```
After which you will run `composer install` or `composer update` to download it and update the autoloader

## Usage

- Sending a message to a single recipient
```php
<?php
require '../vendor/autoload.php';

use Gbxnga\SmartSMSSolutions\SmartSMSSolutions;
 

$sms = new SmartSMSSolutions("<EMAIL>","<PASSWORD>");

$sender = "Sender Name here";
$recipient = "11 Digit Nigerian phone number here";
$message = "Your Message here"; 
 
echo $sms->getBalance();

echo $sms->sendMessage($sender,$recipient,$message);
```
- Sending message to multiple recipients
```php
<?php
require '../vendor/autoload.php';

use Gbxnga\SmartSMSSolutions\SmartSMSSolutions;
 

$sms = new SmartSMSSolutions("<EMAIL>","<PASSWORD>");

$sender = "Sender Name here";
$recipients = [
              "XXXXXXXXXXX",
              "XXXXXXXXXXX"
             ];
$message = "Your Message here";  

echo $sms->sendMessage($sender,$recipients,$message);
```


## Running the tests
To run the tests: include your [smartsmssolutions.com](http://smartsmssolutions.com) email and password in the `class SmartSMSSolutionsTest` constants.
Then run `vendor/bin/phpunit`. Make sure there is more than zero SMS units on the account.
```
<?php
class SmartSMSSolutionsTest extends TestCase
{
    const SMART_SMS_SOLUTIONS_USERNAME = "<EMAIL>";

    const SMART_SMS_SOLUTIONS_PASSWORD = "PASSWORD";

    .
    .
    .
}
```


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
The [MIT](https://choosealicense.com/licenses/mit/) LICNESE. Please see [License File](https://github.com/gbxnga/smart-sms-solutions/blob/master/LICENSE.md) form more information.
