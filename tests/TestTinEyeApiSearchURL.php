<?php

declare (strict_types = 1);

namespace tineye\api;

use \GuzzleHttp\Exception\ClientException;

/**
 * @coversDefaultClass \tineye\api\TinEyeApi
 */
class TestTinEyeApiSearchUrl extends \PHPUnit\Framework\TestCase
{
    const MELON_CAT_URL = 'https://tineye.com/images/meloncat.jpg';

    /**
     * Test that an image can be searched with data
     */
    public function testSearchUrl()
    {
        $tineyeapi = new TinEyeApi();
        $search_result = $tineyeapi->searchUrl(self::MELON_CAT_URL);
        $this->assertTrue($search_result['code'] === 200);
        $this->assertTrue(sizeof($search_result['results']['matches']) > 1);
    }

    /**
     * Test that an error is thrown when a request is made to a non existent key
     */
    public function testClientExceptionOnInvalidKeys()
    {
        $tineyeapi = new TinEyeApi('Not a Key', 'Also Not a Key');
        try {
            $tineyeapi->searchUrl(self::MELON_CAT_URL);
        } catch (ClientException $e) {
            $this->assertNotNull($e);
        }
    }
}
