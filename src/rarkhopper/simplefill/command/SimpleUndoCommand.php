<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use rark\simple_fill\effect\Messages;
use rark\simple_fill\obj\Logger;
use function count;
use function filter_var;
use const FILTER_VALIDATE_INT;

final class SimpleUndoCommand extends Command {
    protected const PERMISSION = 'simple_fill.command.op';
    protected const COMMAND_NAME = 'simpleundo';
    protected const DESCRIPTION = 'Simple Undo';
    protected const ALIAS = 'su';

    public function __construct() {
        parent::__construct(
            self::COMMAND_NAME,
            self::DESCRIPTION,
            [self::ALIAS]
        );
        $this->setPermission(self::PERMISSION);
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$this->testPermission($sender)) {
            return false;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage(Messages::PLZ_EXEC_IN_GAME);
            return false;
        }

        if (count($args) > 1) {
            $sender->sendMessage(Messages::OVER_CMD_ARG);
            return false;
        }

        if (count($args) === 0) {
            Logger::undo($sender, 1);
            return true;
        }

        if (isset($args[0])) {
            if (filter_var($args[0], FILTER_VALIDATE_INT) === false) {
                $sender->sendMessage(Messages::ERR_COUNT);
                return false;
            }

            $undoCount = (int) $args[0];
        } else {
            $undoCount = 1;
        }

        if ($undoCount < 1) {
            Messages::sendMessage($sender, Messages::ERR_COUNT);
            return false;
        }

        Logger::undo($sender, $undoCount);
        return true;
    }
}
