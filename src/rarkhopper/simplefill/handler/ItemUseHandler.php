<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\handler;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use rarkhopper\simplefill\item\SwitchMode;

final class ItemUseHandler implements Listener {
    public static function handleEvent(PlayerItemUseEvent $ev) : void {
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
