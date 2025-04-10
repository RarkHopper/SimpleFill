<?php

declare(strict_types = 1);

namespace rark\simple_fill\handler;

use pocketmine\event\Event;

interface BaseHandler {
    public static function getTarget() : string;
    public static function handleEvent(Event $ev) : void;
}
