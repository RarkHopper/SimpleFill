<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\obj;

use pocketmine\player\Player;

abstract class FillStatusWrapper extends Status {
    public static function isFillMode(Player $player) : bool {
        return (bool) (self::getStatus($player) & self::FILL);
    }

    public static function setFillMode(Player $player) : void {
        if (!isset(self::$player_status[$player->getName()])) {
            self::prepare($player);
        }
        self::$player_status[$player->getName()] |= self::FILL;
    }

    public static function offFillMode(Player $player) : void {
        if (!isset(self::$player_status[$player->getName()])) {
            self::prepare($player);
        }
        self::$player_status[$player->getName()] &= ~self::FILL;
    }
}
