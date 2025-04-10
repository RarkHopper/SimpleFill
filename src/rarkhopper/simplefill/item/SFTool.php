<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\item;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\player\Player;

interface SFTool {
    public static function init() : void;
    public static function useOnBlock(Player $player, Item $item, Block $block) : void;
    public static function use(Player $player, Item $item) : void;
    public static function equals(Item $item) : bool;
}
