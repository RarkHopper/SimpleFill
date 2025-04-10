<?php

declare(strict_types = 1);

namespace rark\simple_fill\effect;

use rark\simple_fill\Loader;

abstract class Errors {
    final private function __construct() {/** NOOP */
    }
    const WORLD_IS_NULL = 'world is null';
    const INVALID_PLACE_SPEED = 'place speed must be more then 1';
    const INVALID_FILL_SIZE = 'fill size must be more then 1';
    const INVALID_SAVE_LOG_SIZE = 'save log size must be more then 1';
    const INVALID_MAX_FILL_SIZE = 'max fill size must be more then 1';
    const INVALID_LENGTH = 'len must be more then 1';
    const INVALID_CONTAINER_SIZE = 'container size is too large';
    const FAILED_LOAD_CONFIG = 'failed to load config file';
    const KEY_NOT_FOUND = 'key is not exists';
    const NOT_FOUND_PLACE_SPEED = '"' . Loader::PLACE_SPEED . '"' . self::KEY_NOT_FOUND;
    const NOT_FOUND_FILL_SIZE = '"' . Loader::FILL_SIZE . '"' . self::KEY_NOT_FOUND;
    const NOT_FOUND_SAVE_LOG_SIZE = '"' . Loader::SAVE_LOG_SIZE . '"' . self::KEY_NOT_FOUND;
    const NOT_FOUND_MAX_FILL_SIZE = '"' . Loader::MAX_FILL_SIZE . '"' . self::KEY_NOT_FOUND;
}
