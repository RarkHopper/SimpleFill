<?php

declare(strict_types = 1);

namespace rark\simple_fill\effect;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\world\sound\BlockBreakSound;
use pocketmine\world\sound\BlockPlaceSound;
use pocketmine\world\sound\NoteInstrument;
use pocketmine\world\sound\NoteSound;
use pocketmine\world\sound\PopSound;

abstract class Sounds {
    final private function __construct() {/** NOOP */
    }

    public static function popSound(Player $player) : void {
        $pos = $player->getPosition();
        $pos->getWorld()->addSound($pos->asVector3(), new PopSound(), [$player]);
    }

    public static function noteSound(Player $player) : void {
        $pos = $player->getPosition();
        $pos->getWorld()->addSound($pos->asVector3(), new NoteSound(NoteInstrument::PIANO(), 7), [$player]);
    }

    public static function blockPlaceSound(Player $player, Block $block) : void {
        $pos = $player->getPosition();
        $pos->getWorld()->addSound($pos->asVector3(), new BlockPlaceSound($block), [$player]);
    }

    public static function blockBreakSound(Player $player) : void {
        $pos = $player->getPosition();
        $pos->getWorld()->addSound($pos->asVector3(), new BlockBreakSound(VanillaBlocks::OAK_WOOD()), [$player]);
    }
}
