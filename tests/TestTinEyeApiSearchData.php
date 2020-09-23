<?php

declare (strict_types = 1);

namespace tineye\api;

use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ConnectException;

/**
 * @coversDefaultClass \tineye\api\TinEyeApi
 */
class TestTinEyeApiSearchData extends \PHPUnit\Framework\TestCase
{
    const MELON_CAT_URL = 'https://tineye.com/images/meloncat.jpg';
    const MELON_CAT_LOCAL = 'tests/meloncat.jpg';

    /**
     * Test that an image can be searched with data
     * by searching a meloncat sample image
     */
    public function testSearchData()
    {
        $tineyeapi = new TinEyeApi();
        $search_result = $tineyeapi->searchData(
            fopen(self::MELON_CAT_LOCAL, 'r'),
            'meloncat.jpg'
        );
        $this->assertTrue($search_result['code'] === 200);
        $this->assertTrue(sizeof($search_result['results']['matches']) > 1);
    }

    /**
     * Test that the file name is url encoded and lower cased
     */
    public function testFileNameEncoded()
    {
        $tineyeapi = new TinEyeApi();
        $file_name = "MeL ONca-t1.jpg";
        $search_result = $tineyeapi->searchData(
            fopen(self::MELON_CAT_LOCAL, 'r'),
            $file_name
        );
        $this->assertTrue($search_result['code'] === 200);
        $this->assertTrue(sizeof($search_result['results']['matches']) > 1);
    }

    /**
     * Test that an error is thrown when a request is made to a non existant tld
     */
    public function testConnectionException()
    {
        $tineyeapi = new TinEyeApi('Not a Key', 'Also Not a Key', [], 'https://thisisnotcorrect.tineye');
        try {
            $tineyeapi->searchData(
                fopen('tests/meloncat.jpg', 'r'),
                self::MELON_CAT_LOCAL
            );
        } catch (ConnectException $e) {
            $this->assertNotNull($e);
        }
    }

    /**
     * Test that an error is thrown when a request is made to a non existant key
     */
    public function testClientExceptionOnInvalidKeys()
    {
        $tineyeapi = new TinEyeApi('Not a Key', 'Also Not a Key');
        try {
            $tineyeapi->searchUrl(self::MELON_CAT_URL);
            $this->fail();
        } catch (ClientException $e) {
            $this->assertNotNull($e);
        }
    }

    /**
     * Test that an error is thrown when a request is made to a non existant key
     */
    public function testJSONDecodeError()
    {
        $tineyeapi = new TinEyeApi('Not a Key', 'Also Not a Key', [], "https://tineye.com");
        try {
            $tineyeapi->searchUrl(self::MELON_CAT_URL);
            $this->fail();
        } catch (TinEyeJsonParseException $e) {
            $this->assertNotNull($e);
        }
    }
}
