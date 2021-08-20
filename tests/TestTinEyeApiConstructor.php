<?php

declare (strict_types = 1);
namespace tineye\api;

/**
 * @coversDefaultClass \tineye\api\TinEyeApi
 */
class TestTinEyeApiConstructor extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that the Default params can preform a sandbox search
     */
    public function testSandBoxKeysUsedAsDefault()
    {
        $tineyeapi = new TinEyeApi();
        $search_result = $tineyeapi->searchUrl('https://tineye.com/images/meloncat.jpg');
        $this->assertTrue($search_result['code'] === 200);
    }

    /**
     * Test that an optional param param (limit works)
     */
    public function testLimitParam()
    {
        $tineyeapi = new TinEyeApi();
        $search_result = $tineyeapi->searchUrl(
            'https://tineye.com/images/meloncat.jpg',
            ['limit' => 2]
        );
        $this->assertTrue(sizeof($search_result['results']['matches']) === 2);
    }

    /**
     * Test that Guzzle Options are passed through the constructor by setting a
     * header and then checking if the client has that header set
     */
    public function testClientOptionsPassthrough()
    {
        $tineyeapi = new TinEyeApi(
            SANDBOX_API_KEY,
            [
                'headers' => [
                    'User-Agent' => 'TEST_USER_AGENT',
                ],
            ]
        );
        // Get the Client from TinEyeApi
        $guzzle_client = $tineyeapi->getClient();
        // Check client exists
        $this->assertNotNull($guzzle_client);
        // Check header is correctly set
        $this->assertTrue($guzzle_client->getConfig('headers')['User-Agent'] === 'TEST_USER_AGENT');
    }
}
