<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\item;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use rarkhopper\simplefill\effect\Messages;
use rarkhopper\simplefill\obj\Container;
use rarkhopper\simplefill\obj\ContainerPool;
use rarkhopper\simplefill\obj\FillStatusWrapper;
use rarkhopper\simplefill\obj\Logger;
use rarkhopper\simplefill\obj\PreContainer;

class AirFill implements SFTool {
    const BASE_NAME = TextFormat::AQUA . 'Air Fill' . TextFormat::RESET;
    protected static Item $item;

    public static function init() : void {
        self::$item = VanillaItems::IRON_PICKAXE();
        self::$item->setCustomName(self::BASE_NAME);
        self::$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
    }

    public static function get() : Item {
        return clone self::$item;
    }

    public static function equals(Item $item) : bool {
        return $item->getCustomName() === self::BASE_NAME;
    }

    /**
     * @return \Generator<Item>
     */
    protected static function getItems() : \Generator {
        yield self::$item;
    }

    public static function use(Player $player, Item $item) : void {
        //NOOP
    }

    public static function useOnBlock(Player $player, Item $item, Block $block) : void {
        if (!self::equals($item)) {
            return;
        }
        if ($block === null) {
            return;
        }
        if (!FillStatusWrapper::isFillMode($player)) {
            return;
        }
        self::onBlockBreak($player, $block, ContainerPool::getPreContainerNonNull($player));
    }

    protected static function onBlockBreak(Player $player, Block $block, PreContainer $pre_container) : void {
        $pre_container->push($block->getPosition()->asVector3());

        if (!$pre_container->isComplete()) {
            Messages::sendMessage($player, Messages::SET_POS1);
            return;
        }
        Messages::sendMessage($player, Messages::SET_POS2);
        $container = $pre_container->parse();

        if ($container === null) {
            Messages::sendMessage($player, Messages::ERR_CONTAINER);
            return;
        }
        $container->fill(VanillaBlocks::AIR(), $player->getPosition()->getWorld());
        $container->place($player);
        ContainerPool::clearContainer($player);
    }

    protected static function saveLog(Player $player, Container $container) : void {
        $container->loadBlocks($player->getPosition()->getWorld());
        Logger::push($player, $container);
    }
}
