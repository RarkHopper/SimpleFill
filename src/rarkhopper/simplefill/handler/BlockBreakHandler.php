<?php
declare(strict_types = 1);

namespace rark\simple_fill\handler;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use rark\simple_fill\item\AirFill;

class BlockBreakHandler implements BaseHandler{
	public static function getTarget():string{
		return BlockBreakEvent::class;
	}

	public static function handleEvent(Event $ev):void{
		if(!$ev instanceof BlockBreakEvent) return;
		AirFill::useOnBlock($ev->getPlayer(), $ev->getItem(), $ev->getBlock());
	}
