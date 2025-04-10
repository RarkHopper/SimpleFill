<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\obj;

use pocketmine\player\Player;
use function array_filter;

abstract class Status {
    final private function __construct() {/** NOOP */
    }
    const FLAT = 0;
    const FILL = 0b1;

    /** @var int[] */
    protected static array $player_status = [];

    protected static function prepare(Player $player) : void {
        self::$player_status[$player->getName()] = self::FLAT;
    }

    public static function getStatus(Player $player) : int {
        if (!isset(self::$player_status[$player->getName()])) {
            self::prepare($player);
        }
        return self::$player_status[$player->getName()];
    }

    public static function is(Player $player, int $flag) : bool {
        return (bool) (self::getStatus($player) & $flag);
    }

    public static function set(Player $player, int $flag) : void {
        if (!isset(self::$player_status[$player->getName()])) {
            self::prepare($player);
        }
        self::$player_status[$player->getName()] |= $flag;
    }

    public static function clearAll(Player $player) : void {
        unset(self::$player_status[$player->getName()]);
        self::$player_status = array_filter(self::$player_status);
    }

    public static function clear(Player $player, int $flag) : void {
        if (!isset(self::$player_status[$player->getName()])) {
            self::prepare($player);
        }
        self::$player_status[$player->getName()] &= ~$flag;
    }
}
