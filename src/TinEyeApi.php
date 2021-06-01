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
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\MultipartStream;

// Sandbox keys are used as defaults
const SANDBOX_PRIVATE_KEY = '6mm60lsCNIB,FwOWjJqA80QZHh9BMwc-ber4u=t^';
const SANDBOX_PUBLIC_KEY  = 'LCkn,2K7osVwkX95K4Oy';
const API_BASE_URL        = 'https://api.tineye.com/rest/';

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
     * the api_url defaults to https://api.tineye.com/rest/, it can be set for debugging purposes.
     *
     *
     * @param string      $api_private_key       API Secret
     * @param string      $api_public_key        API Key
     * @param array|null  $guzzle_client_options Provided for those that need to pass options to the client
     * @param string      $api_url               Provided for debugging, Defaults to https://api.tineye.com/rest/
     *
     * @return void
     */
    public function __construct(
        string $api_private_key = SANDBOX_PRIVATE_KEY,
        string $api_public_key = SANDBOX_PUBLIC_KEY,
        ?array $guzzle_client_options = [],
        string $api_url = API_BASE_URL
    ) {

        $this->api_private_key = $api_private_key;
        $this->api_public_key  = $api_public_key;
        $this->api_url         = $api_url;

        // Keeps backward compatibility
        if (null === $guzzle_client_options) {
            $guzzle_client_options = [];
        }

        // Add user defined client options
        $guzzle_client_options = array_merge($guzzle_client_options, [
            'base_uri' => $api_url,
        ]);

        $this->client = new Client($guzzle_client_options);
    }

    /**
     * Returns the wrapped Guzzle client instance.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Search for an image using a remote image specified by URL.
     *
     * @param string     $url    Image URL to be downloaded and searched
     * @param array|null $params
     *
     * @return array Multidimensional Array of the returned JSON
     *
     * @throws GuzzleException
     * @throws TinEyeJsonParseException
     */
    public function searchUrl(string $url, ?array $params = []): array
    {
        // Keeps backward compatibility
        if (null === $params) {
            $params = [];
        }

        //Push image url into request params
        $params = array_merge($params, ['image_url' => $url]);

        $options = $this->generateGetRequestParams('search', uniqid('', true), time(), $params);

        // Call API
        $res = $this->client->request("GET", "search/", [
            'query' => $options,
        ]);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Search for an image using local image data.
     * TinEye supports JPEG, PNG, WEBP, GIF, BMP, or TIFF image formats.
     *
     * @param string|resource|false $image_data fopen stream of an image
     * @param string                $file_name  Name of the file to be uploaded
     * @param array|null            $params
     *
     * @return array Multidimensional Array of the returned JSON
     *
     * @throws GuzzleException
     * @throws TinEyeJsonParseException
     */
    public function searchData($image_data, string $file_name, ?array $params = []): array
    {
        // Keeps backward compatibility
        if (null === $params) {
            $params = [];
        }

        $guzzle_options = $this->generatePostRequestParams(
            'search',
            uniqid('', true),
            time(),
            $image_data,
            $file_name,
            $params
        );

        // Call API
        $res = $this->client->request("POST", "search/", $guzzle_options);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Returns the Number of searches left in the current bundle.
     *
     * @return array Multidimensional Array of the returned JSON
     *
     * @throws GuzzleException
     * @throws TinEyeJsonParseException
     */
    public function remainingSearches(): array
    {
        $options = $this->generateGetRequestParams('remaining_searches', uniqid('', true), time());

        // Call API
        $res = $this->client->request("GET", "remaining_searches/", [
            'query' => $options,
        ]);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Returns the Number of images currently indexed by TinEye.
     *
     * @return array Multidimensional Array of the returned JSON
     *
     * @throws GuzzleException
     * @throws TinEyeJsonParseException
     */
    public function imageCount(): array
    {
        $options = $this->generateGetRequestParams('image_count', uniqid('', true), time());

        // Call API
        $res = $this->client->request("GET", "image_count/", [
            'query' => $options,
        ]);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Generate the Guzzle request options of a GET, including the request signature.
     *
     * @return array $params An array of params to be passed to the Guzzle Client instance
     */
    private function generateGetRequestParams(
        string $method,
        string $nonce,
        int $date,
        ?array $params = []
    ): array {
        // Keeps backward compatibility
        if (null === $params) {
            $params = [];
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
     * Generate the Guzzle request options of a POST, including the request signature and form data.
     *
     * @return array $params An array of params to be passed to the Guzzle Client instance
     */
    private function generatePostRequestParams(
        string $method,
        string $nonce,
        int $date,
        $image_data,
        string $file_name,
        ?array $params = []
    ): array {
        // Keeps backward compatibility
        if (null === $params) {
            $params = [];
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
        $api_sig_raw  = $this->api_private_key . "POST" . $contenttype_header . $file_name . $date . $nonce;
        $api_sig_raw .= $this->api_url . $method . "/" . $signature_options;

        // Hash the request string and add the other params to the form
        $params['api_sig'] = hash_hmac("sha256", $api_sig_raw, $this->api_private_key);
        $params['api_key'] = $this->api_public_key;
        $params['date']    = $date;
        $params['nonce']   = $nonce;

        $multipart_form = [
            [
                'name'     => 'image_upload',
                'contents' => $image_data,
                'filename' => $file_name,
                'headers'  => [
                    'Content-Type' => 'application/octet-stream',
                ],
            ],
        ];

        //Add any extra options to the form
        foreach ($params as $key => $value) {
            $multipart_form[] = [
                'name'     => $key,
                'contents' => $value,
            ];
        }

        // Guzzle options for multipart form
        $guzzle_options = [
            'headers' => [
                'Connection'   => 'close',
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
            ],
            'body' => new MultipartStream($multipart_form, $boundary),
        ];

        return $guzzle_options;
    }
}
