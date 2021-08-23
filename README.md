# TinEye API PHP Library

**tineye-api** is a PHP library for the [TinEye API](https://api.tineye.com). The TinEye API
is a paid reverse image search solution for professional, commercial or high-volume users of TinEye.

# Contents
- [ Installation ](#installation)
- [ Migrating from previous versions ](#migrating-from-previous-versions)
- [ Getting started ](#getting-started)
- [ Methods ](#methods)
    - [ Common parameters ](#common-parameters)
    - [ Search using an image URL ](#search-using-an-image-url)
    - [ Search using image data ](#search-using-image-data)
    - [ Get remaining searches ](#get-remaining-searches)
    - [ Get number of indexed images ](#get-number-of-indexed-images)
    - [ Get the HTTP client ](#get-the-http-client)
- [ Support ](#support)
- [ Testing ](#testing)
- [ License ](#license)


# Installation

Install via [Composer](https://getcomposer.org/). If composer is installed, run the following from your shell:

```shell
$ composer require tineye/tineye-api
```

# Migrating from previous versions
​
If you were using any version of the TinEye API library before `2.0.0`, you will need
to make minor changes to your code.
​
The API object is now instantiated using a single key, `api_key`. The value
of this key is the same as your previous `private_key`. The public key is no 
longer used.
​
#### New ✅ 
```php
<?php
// Sandbox key
// Note that this is the same value as the old private_key
$api_key = '6mm60lsCNIB,FwOWjJqA80QZHh9BMwc-ber4u=t^';
$tineyeapi = new tineye\api\TinEyeApi($api_key);
```
​
#### Old ❌
```php
<?php
// Sandbox keys
$public_key = 'LCkn,2K7osVwkX95K4Oy';
$private_key = '6mm60lsCNIB,FwOWjJqA80QZHh9BMwc-ber4u=t^';
$tineyeapi = new tineye\api\TinEyeApi($private_key, $public_key);
```

# Getting started

Once you've installed the library, you can instantiate a `TinEyeApi` object with your API key:

```php
<?php
$tineyeapi = new tineye\api\TinEyeApi($api_key);
```

If you don't have an account yet, you can still test out the library using our [API sandbox](https://services.tineye.com/developers/tineyeapi/sandbox) by instantiating the `TinEyeApi` object
with no arguments:

```php
<?php
$tineyeapi = new tineye\api\TinEyeApi();
```

Note that the API sandbox will not return real results; all results will be the same image of a cat.

Once you've created your `TinEyeApi` object you can start searching. You can submit an image using either an
[image URL](#search-using-an-image-url) or by [submitting image data](#search-using-image-data)
by uploading an image file. You can also [check the number of remaining searches](#get-remaining-searches)
in your account or [check the number of images in the TinEye index](#get-number-of-indexed-images).

# Methods

## Common parameters

Each search method (`searchUrl` and `searchData`) takes an optional parameter `params` that takes an associative array with any of these options:

```php
<?php
$params = [
    'offset' => 0,
    'limit' => 10,
    'backlink_limit' => 100,
    'sort' => 'score',
    'order' => 'desc',
    'domain' => 'tineye.com',
];
```

For more information on possible settings please visit the [TinEye API documention](https://services.tineye.com/developers/tineyeapi/api_reference#search).

## Search using an image URL

Use this method to have TinEye download an image URL and search it against the TinEye index.

```php
<?php
/**
* Search for an image using an image URL
*
* @param String $url Image URL to be downloaded and searched
* @param Array $params Optional General Arguments
* @return Array Multidimensional Array of the returned JSON
*/

$tineyeapi = new tineye\api\TinEyeApi($api_key);
$search_result = $tineyeapi->searchUrl('https://tineye.com/images/meloncat.jpg');
```

## Search using image data

Use this method to upload an image to TinEye and search it against the TinEye index.

```php
<?php
/**
* Search for an image using local image data
* TinEye supports JPEG, PNG, WEBP, GIF, BMP, or TIFF image formats
*
* @param String $image_data fopen stream of an image
* @param String $file_name Name of the file to be uploaded
* @param Array $params Optional General Arguments
*
* @return Array Multidimensional Array of the returned JSON
*/
$tineyeapi = new tineye\api\TinEyeApi($api_key);
$search_result = $tineyeapi->searchData(
    fopen('./tests/meloncat.jpg', 'r'),
    'meloncat.jpg'
);
```

## Get remaining searches

Use this method to get the number and status of remaining search bundles.

```php
<?php
/**
* Returns information on search bundles 
* @return Array Multidimensional array of the returned JSON
*/
$tineyeapi = new tineye\api\TinEyeApi($api_key);
$search_bundles = $tineyeapi->remainingSearches();
```

## Get number of indexed images

Use this method to get the number and images currently indexed by TinEye

```php
<?php
/**
* Returns the count of images in the TinEye index 
* @return Array Multidimensional array of the returned JSON
*/
$tineyeapi = new tineye\api\TinEyeApi($api_key);
$search_bundles = $tineyeapi->imageCount();
```

## Get the HTTP client

This method allows access to the wrapped GuzzleHttp client. More information is available at [GuzzleHttp](https://github.com/guzzle/guzzle).

```php
<?php
/**
* Returns the wrapped Guzzle client instance
* @return GuzzleHttp\Client
*/
$tineyeapi = new tineye\api\TinEyeApi($api_key);
$guzzle_client = $tineyeapi->getClient();
```

# Support

Please send comments, recommendations, and bug reports to support@tineye.com.

# Testing

Tests are located in the `/tests` folder and use [PHPunit](https://phpunit.de/).

```bash
$ composer test
```

# License

Licensed under the MIT License (MIT). Please see [License File](LICENSE.md) for more information.
