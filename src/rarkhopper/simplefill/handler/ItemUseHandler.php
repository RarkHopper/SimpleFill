<?php

declare(strict_types = 1);

namespace rark\simple_fill\handler;

use pocketmine\event\Event;
use pocketmine\event\player\PlayerItemUseEvent;
use rark\simple_fill\item\SwitchMode;

class ItemUseHandler implements BaseHandler {
    public static function getTarget() : string {
        return PlayerItemUseEvent::class;
    }

    public static function handleEvent(Event $ev) : void {
        if (!$ev instanceof PlayerItemUseEvent) {
            return;
        }
        if (!SwitchMode::equals($ev->getItem())) {
            return;
        }
        SwitchMode::use($ev->getPlayer(), $ev->getItem());
        $ev->cancel();
    }
}
