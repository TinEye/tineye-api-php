<?php

declare (strict_types = 1);

namespace tineye\api;

use GuzzleHttp\Exception\ConnectException;

/**
 * @coversDefaultClass \tineye\api\TinEyeApi
 */
class TestTinEyeApiRemainingSearches extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that the Default params can preform a sandbox search
     */
    public function testRemainingSearchesMethod()
    {

        $tineyeapi = new TinEyeApi();
        $remaining_searches = $tineyeapi->remainingSearches();
        $this->assertTrue($remaining_searches['code'] === 200);
        $this->assertNotNull($remaining_searches['results']);
        $this->assertNotNull(sizeOf($remaining_searches['results']) > 1);
    }

    /**
     * Test that an error is thrown when a request is made to a non existant tld
     */
    public function testExceptionOnFailedRequest()
    {
        $tineyeapi = new TinEyeApi('Not a Key', [], 'https://thisisnotcorrect.tineye');
        try {
            $tineyeapi->remainingSearches();
        } catch (ConnectException $e) {
            $this->assertNotNull($e);
        }
    }
}
