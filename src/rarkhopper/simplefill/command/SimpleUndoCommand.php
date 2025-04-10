<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use rarkhopper\simplefill\effect\Messages;
use rarkhopper\simplefill\obj\Logger;
use function count;
use function filter_var;
use const FILTER_VALIDATE_INT;

final class SimpleUndoCommand extends Command {
    public const PERMISSION = 'simple_fill.command.op';
    public const COMMAND_NAME = 'simpleundo';
    public const DESCRIPTION = 'SimpleFillのUndoを行います。引数にUndoする回数を指定することで、Undoの回数を指定できます。';
    public const USAGE = '/su <undo_count>';
    public const ALIAS = 'su';

    public function __construct() {
        parent::__construct(
            self::COMMAND_NAME,
            self::DESCRIPTION,
            self::USAGE,
            [self::ALIAS],
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
