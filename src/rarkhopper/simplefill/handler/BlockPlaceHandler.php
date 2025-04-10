<?php

declare(strict_types = 1);

namespace rark\simple_fill\handler;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Event;
use pocketmine\scheduler\ClosureTask;
use rark\simple_fill\effect\Messages;
use rark\simple_fill\effect\Sounds;
use rark\simple_fill\Loader;
use rark\simple_fill\obj\ContainerPool;
use rark\simple_fill\obj\FillStatusWrapper;

class BlockPlaceHandler implements BaseHandler {
    public static function getTarget() : string {
        return BlockPlaceEvent::class;
    }

    public static function handleEvent(Event $ev) : void {
        if (!$ev instanceof BlockPlaceEvent) {
            return;
        }
        $player = $ev->getPlayer();
        $block = $ev->getBlock();

        if (!FillStatusWrapper::isFillMode($player)) {
            return;
        }
        $v = $block->getPosition()->asVector3();
        $world = $player->getPosition()->getWorld();
        $pre_container = ContainerPool::getPreContainerNonNull($player);
        $pre_container->push($v);

        if (!$pre_container->isComplete()) {
            Messages::sendMessage($player, Messages::SET_POS1);
            Sounds::blockPlaceSound($player, $block);
            $ev->cancel();
            return;
        }
        $old_block = $ev->getBlockReplaced();
        Messages::sendMessage($player, Messages::SET_POS2);
        Loader::getTaskScheduler()->scheduleDelayedTask(
            new ClosureTask(
                function() use ($player, $pre_container, $v, $world, $old_block) : void {
                    $block = $world->getBlock($v);
                    $world->setBlock($v, $old_block);
                    $container = $pre_container->parse();

                    if ($container === null) {
                        Messages::sendMessage($player, Messages::ERR_CONTAINER);
                        return;
                    }
                    $container->fill($block, $player->getPosition()->getWorld());
                    $container->place($player);
                }
            ),
            1
        );
        ContainerPool::clearContainer($player);
    }
}
