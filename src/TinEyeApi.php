<?php
/**
 * This implements the TinEye API PHP client
 * A thin wrapper class around GuzzleHTTP to make requests to TinEye API
 *
 * @link https://www.tineye.com
 *
 */

namespace tineye\api;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;

// Sandbox keys are used as defaults
const SANDBOX_PRIVATE_KEY = '6mm60lsCNIB,FwOWjJqA80QZHh9BMwc-ber4u=t^';
const SANDBOX_PUBLIC_KEY = 'LCkn,2K7osVwkX95K4Oy';
const API_BASE_URL = 'https://api.tineye.com/rest/';

/**
 * TinEye API wrapper for Guzzle HTTP to make authenticated requests to TinEye.
 * @link https://services.tineye.com/developers/tineyeapi/getting_started.html
 */
class TinEyeApi
{
    // Private/Public keys are defaulted to sand box keys
    private $api_private_key;
    private $api_public_key;
    private $api_url;
    private $client;

    /**
     * The public and private key are required to make a request and defaults to the TinEye sandbox keys
     * Guzzle client options will be passed through to the wrapped client
     * the api_url defaults to https://api.tineye.com/rest/, it can be set for debugging purposes
     *
     *
     * @param String $api_private_key API Secret
     * @param String $api_public_key API Key
     * @param Array $guzzle_client_options Provided for those that need to pass options to the client
     * @param String $api_url Provided for debugging, Defaults to https://api.tineye.com/rest/'
     *
     * @return TinEyeApi
     */
    public function __construct(
        $api_private_key = SANDBOX_PRIVATE_KEY,
        $api_public_key = SANDBOX_PUBLIC_KEY,
        $guzzle_client_options = null,
        $api_url = API_BASE_URL
    ) {

        $this->api_private_key = $api_private_key;
        $this->api_public_key = $api_public_key;
        $this->api_url = $api_url;

        // Add user defined client options
        if ($guzzle_client_options) {
            array_push($guzzle_client_options, [
                'base_uri' => $api_url,
            ]);
        } else {
            $guzzle_client_options = [
                'base_uri' => $api_url,
            ];
        }

        $this->client = new Client($guzzle_client_options);
    }

    /**
     * Returns the wrapped Guzzle client instance
     * @return GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     *Search for an image using a remote image specified by URL
     *
     * @param String $url Image URL to be downloaded and searched
     * @param Array $params
     *
     * @return Array Multidimensional Array of the returned JSON
     */
    public function searchUrl($url, $params = null)
    {
        //Push image url into request params
        if ($params === null) {
            $params = array('image_url' => $url);
        } else {
            $params['image_url'] = $url;
        }

        $options = $this->generateGetRequestParams('search', uniqid(), time(), $params);

        // // Call API
        $res = $this->client->request("GET", "search/", [
            'query' => $options,
        ]);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if ($parsed_json === null) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Search for an image using local image data
     * TinEye supports JPEG, PNG, WEBP, GIF, BMP, or TIFF image formats
     *
     * @param String $image_data fopen stream of an image
     * @param String $file_name Name of the file to be uploaded
     * @param Array $params
     *
     * @return Array Multidimensional Array of the returned JSON
     */
    public function searchData($image_data, $file_name, $params = null)
    {

        $guzzle_options = $this->generatePostRequestParams(
            'search',
            uniqid(),
            time(),
            $image_data,
            $file_name,
            $params
        );

        // Call API
        $res = $this->client->request("POST", "search/", $guzzle_options);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if ($parsed_json === null) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Returns the Number of searches left in the current bundle
     * @return Array Multidimensional Array of the returned JSON
     */
    public function remainingSearches()
    {
        $options = $this->generateGetRequestParams('remaining_searches', uniqid(), time());

        // // Call API
        $res = $this->client->request("GET", "remaining_searches/", [
            'query' => $options,
        ]);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if ($parsed_json === null) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Returns the Number of images currently indexed by TinEye
     * @return Array Multidimensional Array of the returned JSON
     */
    public function imageCount()
    {
        $options = $this->generateGetRequestParams('image_count', uniqid(), time());

        // // Call API
        $res = $this->client->request("GET", "image_count/", [
            'query' => $options,
        ]);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if ($parsed_json === null) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Generate the Guzzle request options of a GET, including the request signature
     * @return Array $params An array of params to be passed to the Guzzle Client instance
     */
    private function generateGetRequestParams($method, $nonce, $date, $params = null)
    {
        //Push image url into request params
        if ($params === null) {
            $params = array();
        }

        // Sort the options for the query string
        ksort($params);
        // Build the parameter string
        $query_options = http_build_query($params);
        // Force the parameter string to lowercase
        $signature_options = strtolower($query_options);
        // Build the request string
        $api_sig_raw = $this->api_private_key . "GET" . $date . $nonce . $this->api_url . $method . '/';
        $api_sig_raw .= $signature_options;

        // Hash the request string and add the query params
        $params['api_sig'] = hash_hmac("sha256", $api_sig_raw, $this->api_private_key);
        $params['api_key'] = $this->api_public_key;
        $params['nonce'] = $nonce;
        $params['date'] = $date;

        return $params;
    }

    /**
     * Generate the Guzzle request options of a POST, including the request signature and form data
     * @return Array $params An array of params to be passed to the Guzzle Client instance
     */
    private function generatePostRequestParams($method, $nonce, $date, $image_data, $file_name, $params = null)
    {
        if ($params === null) {
            $params = array();
        }

        // Sort the options for the query string
        ksort($params);
        // Build the parameter string
        $query_options = http_build_query($params);
        // Force the parameter string to lowercase
        $signature_options = strtolower($query_options);
        //Request boundary for multipart post requests
        $boundary = "---------------------" . md5(mt_rand() . microtime());
        $contenttype_header = "multipart/form-data; boundary=$boundary";

        //Lower case and urlencode file_name
        $file_name = strtolower(rawurlencode($file_name));

        //Compose api string to sign
        $api_sig_raw = $this->api_private_key . "POST" . $contenttype_header . $file_name . $date . $nonce;
        $api_sig_raw .= $this->api_url . $method . "/" . $signature_options;

        // Hash the request string and add the other params to the form
        $params['api_sig'] = hash_hmac("sha256", $api_sig_raw, $this->api_private_key);
        $params['api_key'] = $this->api_public_key;
        $params['date'] = $date;
        $params['nonce'] = $nonce;

        $multipart_form = [
            [
                'name' => 'image_upload',
                'contents' => $image_data,
                'filename' => $file_name,
                'headers' => [
                    'Content-Type' => 'application/octet-stream',
                ],
            ],
        ];

        //Add any extra options to the form
        foreach ($params as $key => $value) {
            array_push($multipart_form, [
                'name' => $key,
                'contents' => $value,
            ]);
        }

        // Guzzle options for multipart form
        $guzzle_options = [
            'headers' => [
                'Connection' => 'close',
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
            ],
            'body' => new MultipartStream($multipart_form, $boundary),
        ];

        return $guzzle_options;
    }
}
