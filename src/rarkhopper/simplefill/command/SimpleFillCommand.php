<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use rarkhopper\simplefill\effect\Messages;
use rarkhopper\simplefill\item\AirFill;
use rarkhopper\simplefill\item\SwitchMode;
use function count;

final class SimpleFillCommand extends Command {
    public const PERMISSION = 'simple_fill.command.op';
    public const COMMAND_NAME = 'simple_fill';
    public const DESCRIPTION = 'SimpleFillを利用するためのアイテムを付与します。引数にon/offを指定することで、SimpleFillのON/OFFを切り替えます。';
    public const USAGE = '/sf <on|off>';
    public const ALIAS = 'sf';

    public function __construct() {
        parent::__construct(
            self::COMMAND_NAME,
            self::DESCRIPTION,
            self::USAGE,
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

        if (isset($args[0])) {
            $mode = $args[0] === 'on' || $args[0] === 'true' || $args[0] === '1';

            if ($mode) {
                Messages::sendMessage($sender, Messages::TURN_ON);
                SwitchMode::onReceiveOff($sender);
            } else {
                Messages::sendMessage($sender, Messages::TURN_OFF);
                SwitchMode::onReceiveOn($sender);
            }
            return true;
        }

        $sender->getInventory()->addItem(SwitchMode::get($sender), AirFill::get());
        Messages::sendMessage($sender, Messages::ADDED_ITEMS);
        return true;
    }
}
