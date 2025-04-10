<?php

declare(strict_types = 1);

namespace rark\simple_fill\obj;

use pocketmine\player\Player;
use rark\simple_fill\effect\Errors;
use rark\simple_fill\effect\Messages;
use function array_pop;
use function array_shift;
use function count;
use function is_array;

abstract class Logger {
    final private function __construct() {/** NOOP */
    }
    protected static int $save_log_size;
    /** @var Container[][] */
    protected static array $log = [];

    public static function init(int $save_log_size) : void {
        if ($save_log_size < 1) {
            throw new \InvalidArgumentException(Errors::INVALID_SAVE_LOG_SIZE);
        }
        self::$save_log_size = $save_log_size;
    }

    /**
     * @return Container[]
     */
    public static function getAllLog(Player $player) : array {
        return isset(self::$log[$player->getName()])? self::$log[$player->getName()]: [];
    }

    protected static function setLog(Player $player, array $log) : void {
        self::$log[$player->getName()] = $log;
    }

    public static function push(Player $player, Container $blocks) : void {
        $log = self::getAllLog($player);
        $log[] = $blocks;

        if (count($log) > self::$save_log_size) {
            array_shift($log);
        }
        self::setLog($player, $log);
    }

    /**
     * @return Container[]
     */
    public static function getLog(Player $player, int $len) : array {
        if ($len < 1) {
            throw new \InvalidArgumentException(Errors::INVALID_LENGTH);
        }
        $name = $player->getName();

        if (!isset(self::$log[$name]) || !is_array(self::$log[$name])) {
            return [];
        }
        $return_log = [];

        for (; $len > 0; --$len) {
            $container = array_pop(self::$log[$name]);

            if (!$container instanceof Container) {
                continue;
            }
            $return_log[] = $container;
        }
        return $return_log;
    }

    public static function undo(Player $player, int $len) : void {
        $count = 0;

        foreach (self::getLog($player, $len) as $container) {
            $container->place();
            ++$count;
        }
        Messages::sendMessage($player, Messages::getUndoMessage($count));
    }
}
