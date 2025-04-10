<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\item;

use pocketmine\block\Block;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use rarkhopper\simplefill\effect\Messages;
use rarkhopper\simplefill\effect\Sounds;
use rarkhopper\simplefill\obj\ContainerPool;
use rarkhopper\simplefill\obj\FillStatusWrapper;

class SwitchMode implements SFTool {
    const BASE_NAME = 'Switch Mode' . TextFormat::RESET;
    const ON_NAME = TextFormat::GREEN . self::BASE_NAME;
    const OFF_NAME = TextFormat::GRAY . self::BASE_NAME;
    protected static Bow $on;
    protected static Bow $off;

    public static function init() : void {
        self::$on = VanillaItems::BOW();
        self::$off = VanillaItems::BOW();
        self::$on = self::$on->setCustomName(self::ON_NAME)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
        self::$off = self::$off->setCustomName(self::OFF_NAME);
    }

    public static function get(Player $player) : Item {
        return FillStatusWrapper::isFillMode($player)? self::$on: self::$off;
    }

    public static function equals(Item $item) : bool {
        return $item->getCustomName() === self::ON_NAME || $item->getCustomName() === self::OFF_NAME;
    }

    public static function use(Player $player, Item $item) : void {
        self::onUse($player, $item);
    }

    public static function useOnBlock(Player $player, Item $item, Block $block) : void {
        self::onUse($player, $item);
    }

    protected static function onUse(Player $player, Item $item) : void {
        if (!self::equals($item)) {
            return;
        }
        $custom_name = $item->getCustomName();

        if ($custom_name === self::ON_NAME) {
            self::onReceiveOn($player);
        } elseif ($custom_name === self::OFF_NAME) {
            self::onReceiveOff($player);
        }
        Sounds::noteSound($player);
        $player->getInventory()->setItemInHand(self::get($player));
    }

    public static function onReceiveOn(Player $player) : void {
        FillStatusWrapper::offFillMode($player);
        ContainerPool::clearContainer($player);
        Messages::sendMessage($player, Messages::TURN_OFF);
    }

    public static function onReceiveOff(Player $player) : void {
        FillStatusWrapper::setFillMode($player);
        ContainerPool::prepare($player);
        Messages::sendMessage($player, Messages::TURN_ON);
    }
}
