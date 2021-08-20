<?php

/**
 * This class provides a custom parsing exception
 *
 * @link https://tineye.com
 */

namespace tineye\api;

use JsonException;

final class TinEyeJsonParseException extends JsonException
{
    public function __construct($message)
    {
        parent::__construct('Failed to parse: ' . $message);
    }
}
