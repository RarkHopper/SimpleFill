<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\obj;

use pocketmine\player\Player;
use function array_filter;

abstract class ContainerPool {
    final private function __construct() {/** NOOP */
    }
    /** @var PreContainer[] */
    protected static array $pre_containers = [];

    public static function prepare(Player $player) : void {
        self::$pre_containers[$player->getName()] = new PreContainer();
    }

    public static function getPreContainer(Player $player) : ?PreContainer {
        return self::hasPreContainer($player)? self::$pre_containers[$player->getName()]: null;
    }

    public static function getPreContainerNonNull(Player $player) : PreContainer {
        if (!self::hasPreContainer($player)) {
            self::prepare($player);
        }
        return self::$pre_containers[$player->getName()];
    }

    public static function clearContainer(Player $player) : void {
        if (!self::hasPreContainer($player)) {
            return;
        }
        unset(self::$pre_containers[$player->getName()]);
        self::$pre_containers = array_filter(self::$pre_containers);
    }

    public static function hasPreContainer(Player $player) : bool {
        return isset(self::$pre_containers[$player->getName()]);
    }

    public static function getContainer(Player $player) : ?Container {
        $pre_container = self::getPreContainer($player);
        return $pre_container === null? null: $pre_container->parse();
    }
}
