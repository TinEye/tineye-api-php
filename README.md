# TinEye API PHP Client

**tineye-api** is a PHP library for [TinEye API](https://api.tineye.com). The TinEye API
is a paid search alternative for professional, commercial or high-volume users of TinEye.

## Quick Start

Install via [Composer](https://getcomposer.org/).

```bash
$ composer require tineye/tineye-api
```

### Run a Search

To search an image with the TinEye API sandbox:

```php
$tineyeapi = new TinEyeApi();
$search_result = $tineyeapi->searchUrl('https://tineye.com/images/meloncat.jpg');
print_r($search_result);
```

The sandbox only returns a stock set of results. You can find more information about the TinEye sandbox [here](https://services.tineye.com/developers/tineyeapi/sandbox.html).

To search an image with tineye-api with an account:

```php
$tineyeapi = new TinEyeApi(<Private_API_Key>,<Public_API_Key>);
$search_result = $tineyeapi->searchUrl('https://tineye.com/images/meloncat.jpg');
print_r($search_result);
```

## Methods

Each search method(`searchUrl` and `searchData`) takes an optional parameter `params` that takes an associative array with any of these options:

```php
$params = [
    'offset' => 0,
    'limit' => 10,
    'backlink_limit' => 100,
    'sort' => 'score',
    'order' => 'desc',
    'domain' => 'tineye.com',
];
```

For more information on possible settings please visit the [TinEye API documention](https://services.tineye.com/developers/tineyeapi/overview.html#general-arguments).

### searchUrl

Use this method to have TinEye download an image URL and search it against the TinEye index.

```php
/**
* Search for an image using an image URL
*
* @param String $url Image URL to be downloaded and searched
* @param Array $params Optional General Arguments
* @return Array Multidimensional Array of the returned JSON
*/

$tineyeapi = new TinEyeApi($api_private_key, $api_public_key);
$search_result = $tineyeapi->searchUrl('https://tineye.com/images/meloncat.jpg');
```

### searchData

Use this method to upload an image to TinEye and search it against the TinEye index.

```php
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
$tineyeapi = new TinEyeApi($api_private_key, $api_public_key);
$search_result = $tineyeapi->searchData(
    fopen('./tests/meloncat.jpg', 'r'),
    'meloncat.jpg'
);
```

### remainingSearches

Use this method to get the number and status of remaining search bundles.

```php
/**
* Returns information on search bundles 
* @return Array Multidimensional array of the returned JSON
*/
$tineyeapi = new TinEyeApi($api_private_key, $api_public_key);
$search_bundles = $tineyeapi->remainingSearches();
```

### imageCount

Use this method to get the number and images currently indexed by TinEye

```php
/**
* Returns the count of images in the TinEye index 
* @return Array Multidimensional array of the returned JSON
*/
$tineyeapi = new TinEyeApi($api_private_key, $api_public_key);
$search_bundles = $tineyeapi->imageCount();
```

### getClient

This method allows access to the wrapped GuzzleHttp Client. More information is available at [GuzzleHttp](https://github.com/guzzle/guzzle)

```php
/**
* Returns the wrapped Guzzle client instance
* @return GuzzleHttp\Client
*/
$tineyeapi = new TinEyeApi($api_private_key, $api_public_key);
$guzzle_client = $tineyeapi->getClient();
```

## Support

Please send comments, recommendations, and bug reports to support@tineye.com.

## Testing

Tests are located in the `/tests` folder and use [PHPunit](https://phpunit.de/).

```bash
$ composer test
```

## License

Licensed under the MIT License (MIT). Please see [License File](LICENSE.md) for more information.
