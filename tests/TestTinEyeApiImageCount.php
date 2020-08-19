<?php

declare (strict_types = 1);
namespace tineye\api;

/**
 * @coversDefaultClass \tineye\api\TinEyeApi
 */
class TestTinEyeApiImageCount extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that the Default params can preform a sandbox search
     */
    public function testImagCountMethod()
    {
        $tineyeapi = new TinEyeApi();
        $search_result = $tineyeapi->imageCount();
        $this->assertTrue($search_result['code'] === 200);
        $this->assertTrue($search_result['results'] > 33884049056);
    }
}
