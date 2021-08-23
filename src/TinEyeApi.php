<?php
/**
 * TinEye API PHP client
 * A thin wrapper class around GuzzleHTTP to make requests to TinEye API
 *
 * @link https://tineye.com
 *
 */

namespace tineye\api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

// Sandbox keys are used as defaults
const SANDBOX_API_KEY = '6mm60lsCNIB,FwOWjJqA80QZHh9BMwc-ber4u=t^';
const API_BASE_URL = 'https://api.tineye.com/rest/';

/**
 * TinEye API wrapper for Guzzle HTTP to make authenticated requests to TinEye.
 * @link https://services.tineye.com/developers/tineyeapi/what_is_tineyeapi
 */
class TinEyeApi
{
    private $client;

    /**
     * The API key is required to make a request and defaults to the TinEye sandbox key
     * Guzzle client options will be passed through to the wrapped client
     * the api_url defaults to https://api.tineye.com/rest/, it can be set for debugging purposes.
     *
     *
     * @param string      $api_key              API Key
     * @param array|null  $guzzle_client_options Provided for those that need to pass options to the client
     * @param string      $api_url               Provided for debugging, Defaults to https://api.tineye.com/rest/
     *
     * @return void
     */
    public function __construct(
        string $api_key = SANDBOX_API_KEY,
        ?array $guzzle_client_options = [],
        string $api_url = API_BASE_URL
    ) {

        $this->api_key = $api_key;
        $this->api_url = $api_url;

        // Keeps backward compatibility
        if (null === $guzzle_client_options) {
            $guzzle_client_options = [];
        }

        // Add user-defined client options
        $guzzle_client_options = array_merge($guzzle_client_options, [
            'base_uri' => $api_url,
        ]);

        // Append API key header if there were some user-defined headers
        if (array_key_exists('headers', $guzzle_client_options)) {
            $guzzle_client_options['headers']['x-api-key'] = $api_key;
        } else {
            $guzzle_client_options['headers'] = ['x-api-key' => $api_key];
        }

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
     * Search for an image using a URL.
     *
     * @param string     $url    Image URL to be downloaded and searched
     * @param array|null $params
     *
     * @return array Array of the returned JSON
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

        // Push image url into request params
        $params = array_merge($params, ['image_url' => $url]);

        // Call API
        $res = $this->client->request("GET", "search/", [
            'query' => $params,
        ]);

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Search for an image using image data.
     * TinEye supports JPEG, PNG, WEBP, GIF, BMP, or TIFF image formats.
     *
     * @param string|resource|false $image_data fopen stream of an image
     * @param string                $file_name  Name of the file to be uploaded
     * @param array|null            $params
     *
     * @return array Array of the returned JSON
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

        // Add extra options to the form
        foreach ($params as $key => $value) {
            $multipart_form[] = [
                'name' => $key,
                'contents' => $value,
            ];
        }

        // Call API
        $res = $this->client->request("POST", "search/",
            ["multipart" => $multipart_form]
        );

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Returns the number of searches left in the current bundle.
     *
     * @return array Array of the returned JSON
     *
     * @throws GuzzleException
     * @throws TinEyeJsonParseException
     */
    public function remainingSearches(): array
    {
        // Call API
        $res = $this->client->request("GET", "remaining_searches/");

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

    /**
     * Returns the number of images in TinEye.
     *
     * @return array Array of the returned JSON
     *
     * @throws GuzzleException
     * @throws TinEyeJsonParseException
     */
    public function imageCount(): array
    {
        // Call API
        $res = $this->client->request("GET", "image_count/");

        // Decode Response
        $parsed_json = json_decode($res->getBody(), true);
        if (null === $parsed_json) {
            throw new TinEyeJsonParseException($res->getBody());
        }

        return $parsed_json;
    }

}
