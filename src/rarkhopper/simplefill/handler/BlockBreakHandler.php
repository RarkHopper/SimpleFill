<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\handler;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use rarkhopper\simplefill\item\AirFill;

final class BlockBreakHandler implements Listener {
    public static function handleEvent(BlockBreakEvent $ev) : void {
        AirFill::useOnBlock($ev->getPlayer(), $ev->getItem(), $ev->getBlock());
    }
}
