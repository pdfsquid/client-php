# PDFsquid PHP client

This library simplifies usage of ZoneApi used for PDFsquid conversion mechanisms.

## Requirements and dependencies

The library requires PHP 5.3 and later. Additionally, make sure that the following PHP extensions are installed on your server:
- [`curl`](https://secure.php.net/manual/en/book.curl.php),
- [`json`](https://secure.php.net/manual/en/book.json.php)

You also need credentials for ZoneApi (api key, api secret) and zone name in order to use this client. If you do not have them you can:
- register at[Client Panel](https://panel.pdfsquid.com) if you do not have account yet
- add new api access

or
- ask for api access your administrator who is registered

## Install by composer

This is preferred install method. If you do not have composer already, install it first from
 [Composer](http://getcomposer.org/). Then run below command:

```bash
composer require pdfsquid/client-php
```

Then use Composer's [autoload](https://getcomposer.org/doc/00-intro.md#autoloading) mechanism:

```php
require_once('vendor/autoload.php');
```

## Manual Installation

There might be situation when you can not use composer to install this library (eg. it is not available on the system). In such cases you can download the [latest release](https://github.com/pdfsquid/zoneapi-php/releases) and then just include the `autoload.php` file.

```php
require_once('/path/to/client-php/autoload.php');
```

## Example usage

Download PDF file from URL/HTML synchronously:

```php
<?php

try {
    // initialize the library
    // pass your credentials, $zone_name is zone associated with api access eg. 'eu1'
    $client = new \PDFsquid\ZoneApi($api_key, $api_secret, $zone_name);
    
    // convert synchronously
    $file = $client->url2pdf('https://google.com');
    // $file = $client->html2pdf('<b>Hello!</b>');
    
    // download file as attachment, you can pass file name as option (default is conversionId value)
    $file->downloadAsAttachment();
    // you can also download file to show directly in browser
    // $file->downloadInline();
    // see more methods on file in class ResponseFile
}
catch(\PDFsquid\Exceptions\PDFsquidException $e) {
    // Jump here on conversion error, authentication error or API is not available
    // get HTTP code
    $code = $e->getHttpCode();
    // get errors
    $errors = $e->getErrors();
    /*
     * $errors can be null or array e.g.:
     *   ["field"]=>
     *    string(9) "x-api-key"
     *    ["message"]=>
     *    string(18) "value is not valid"
     *    ["value"]=>
     *    string(35) "value passed to API"
     */
}
```

Order PDF conversion from URL/HTML asynchronously (e.g. in massive conversions scenario):

```php
<?php

try {
    // initialize the library
    // pass your credentials, $zone_name is zone associated with api access eg. 'eu1'
    $client = new \PDFsquid\ZoneApi($api_key, $api_secret, $zone_name);
    
    // convert asynchronously
    $result = $client->url2pdfAsync('https://google.com');
    // $result = $client->html2pdf('<b>Hello!</b>', false);
    
    // now $result is object with conversionId
    // PDFsquid will call Client's handler defined in Client Panel when conversion ends (ping)
    $conversionId = $result->conversionId;
}
catch(\PDFsquid\Exceptions\PDFsquidException $e) {
    // Jump here on conversion error, authentication error or API is not available
    // get HTTP code
    $code = $e->getHttpCode();
    // get errors
    $errors = $e->getErrors();
    /*
     * $errors can be null or array e.g.:
     *   ["field"]=>
     *    string(9) "x-api-key"
     *    ["message"]=>
     *    string(18) "value is not valid"
     *    ["value"]=>
     *    string(35) "value passed to API"
     */
}
```

Get file from asynchronous conversion:
Following code should be present in webhook specified in Client Panel.
There are available GET parameters:
`conversionId`, `status` (success/error), `errorCode` (error code, present on error)

```php
<?php

try {
    $conversionId = $_GET['conversionId'];
    
    // initialize the library
    // pass your credentials, $zone_name is zone associated with api access eg. 'eu1'
    $client = new \PDFsquid\ZoneApi($api_key, $api_secret, $zone_name);
    
    // convert asynchronously
    $file = $client->getFile($conversionId);
    
    // save file on server
    $file->saveFile('/var/www/files/', 'custom_file_name');
    // see more methods on file in class ResponseFile
}
catch(\PDFsquid\Exceptions\PDFsquidException $e) {
    // Jump here on conversion error, authentication error or API is not available
    // get HTTP code
    $code = $e->getHttpCode();
    // get errors
    $errors = $e->getErrors();
    /*
     * $errors can be null or array e.g.:
     *   ["field"]=>
     *    string(9) "x-api-key"
     *    ["message"]=>
     *    string(18) "value is not valid"
     *    ["value"]=>
     *    string(35) "value passed to API"
     */
}
```

## Documentation

This readme describes working with PHP client library.
Full ZoneApi documentation is available at https://docs.pdfsquid.com/.

## Issues

If you find any problems with this library please raise an issue via this project page. Include as many information as you can with problem description, system environment and steps to reproduce. We will respond as soon as possible.


