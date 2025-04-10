<?php

declare(strict_types = 1);

namespace rark\simple_fill\handler;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use function get_class;

class EventListener implements Listener {
    /** @var BaseHandler[] */
    protected static array $handlers = [];

    public static function init() : void {
        self::registerHandler(new BlockBreakHandler());
        self::registerHandler(new BlockPlaceHandler());
        self::registerHandler(new ItemUseHandler());
    }

    public static function registerHandler(BaseHandler $handler) : void {
        self::$handlers[$handler->getTarget()] = $handler;
    }

    protected static function onListen(Event $ev) : void {
        if (!isset(self::$handlers[($class = get_class($ev))])) {
            return;
        }
        self::$handlers[$class]->handleEvent($ev);
    }

    public function blockBreak(BlockBreakEvent $ev) : void {
        self::onListen($ev);
    }

    public function blockPlace(BlockPlaceEvent $ev) : void {
        self::onListen($ev);
    }

    public function itemUse(PlayerItemUseEvent $ev) : void {
        self::onListen($ev);
    }
}
