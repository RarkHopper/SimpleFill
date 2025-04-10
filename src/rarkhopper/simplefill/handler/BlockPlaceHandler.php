<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\handler;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\scheduler\ClosureTask;
use rarkhopper\simplefill\effect\Messages;
use rarkhopper\simplefill\effect\Sounds;
use rarkhopper\simplefill\Loader;
use rarkhopper\simplefill\obj\ContainerPool;
use rarkhopper\simplefill\obj\FillStatusWrapper;

final class BlockPlaceHandler implements Listener {
    public static function handleEvent(BlockPlaceEvent $ev) : void {
        $player = $ev->getPlayer();
        $transaction = $ev->getTransaction();
        $placedBlock = $ev->getItem()->getBlock();

        if (!FillStatusWrapper::isFillMode($player)) {
            return;
        }
        $v = $placedBlock->getPosition()->asVector3();
        $world = $player->getPosition()->getWorld();
        $pre_container = ContainerPool::getPreContainerNonNull($player);
        $pre_container->push($v);

        if (!$pre_container->isComplete()) {
            Messages::sendMessage($player, Messages::SET_POS1);
            Sounds::blockPlaceSound($player, $placedBlock);
            $ev->cancel();
            return;
        }

        Messages::sendMessage($player, Messages::SET_POS2);
        Loader::getTaskScheduler()->scheduleDelayedTask(
            new ClosureTask(
                function() use ($player, $pre_container, $v, $world, $transaction) : void {
                    $block = $world->getBlock($v);

                    foreach ($transaction->getBlocks() as [$x, $y, $z, $replacedBlock]) {
                        $world->setBlockAt($x, $y, $z, $replacedBlock);
                    }

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
