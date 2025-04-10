<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\task;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use rarkhopper\simplefill\effect\Errors;
use rarkhopper\simplefill\effect\Messages;
use rarkhopper\simplefill\effect\Sounds;
use rarkhopper\simplefill\obj\Container;
use rarkhopper\simplefill\obj\Logger;
use function array_key_first;
use function array_shift;
use function count;

class BlockPlaceTask extends Task {
    protected static int $fill_size;
    protected ?Player $player;
    /** @var Block[] */
    protected ?array $blocks = [];
    protected ?Container $backup = null;
    protected bool $is_killed = false;
    protected int $id;

    public function __construct(Container $container, ?Player $player, int $id) {
        $this->blocks = $container->getBlocks();
        $this->player = $player;
        $this->id = $id;
        $key = array_key_first($this->blocks);

        if ($key === null || !isset($this->blocks[$key])) {
            return;
        }
        $backup = clone $container;

        try {
            $backup->loadBlocks($this->blocks[$key]->getPosition()->getWorld());
        } catch(\Exception) {
            $this->is_killed = true;

            if ($this->player === null) {
                return;
            }
            Messages::sendMessage($player, Messages::ERR_SIZE);
            return;
        }
        $this->backup = $backup;

        if ($this->player === null) {
            return;
        }
        Logger::push($player, $backup);
        Messages::sendMessage($player, Messages::START_FILL);
    }

    public static function init(int $fill_size) : void {
        if ($fill_size < 1) {
            throw new \InvalidArgumentException(Errors::INVALID_FILL_SIZE);
        }
        self::$fill_size = $fill_size;
    }

    public function getId() : int {
        return $this->id;
    }

    public function onRun() : void {
        if ($this->is_killed) {
            $this->stop();
            return;
        }
        for ($i = self::$fill_size; $i > 0; --$i) {
            if (count($this->blocks) < 1) {
                $this->stop();
                break;
            }
            $block = array_shift($this->blocks);
            $p = $block->getPosition();
            $p->getWorld()->setBlock($p->asVector3(), $block);
        }

        if ($this->player === null) {
            return;
        }
        if ($block === null) {
            return;
        }
        Sounds::blockPlaceSound($this->player, $block->isSameState(VanillaBlocks::AIR())? VanillaBlocks::OAK_WOOD(): $block);
    }

    public function stop() : void {
        $this->is_killed = true;
        $this->player = null;
        $this->blocks = null;
        RunningTasks::stop($this->getId());
    }

    public function rollback() : void {
        $this->backup?->forcePlace();
    }
}
