<?php

declare (strict_types = 1);

namespace tineye\api;

use GuzzleHttp\Exception\ConnectException;

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
        $tineyeapi = new TinEyeApi('Not a Key', 'Also Not a Key', [], 'Https://thisisnotcorrect.tineye');
        try {
            $search_result = $tineyeapi->remainingSearches();
        } catch (ConnectException $e) {
            $this->assertNotNull($e);
        }
    }
}
