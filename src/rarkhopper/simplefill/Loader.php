<?php

declare(strict_types = 1);

namespace rark\simple_fill;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use rark\simple_fill\command\SimpleFillCommand;
use rark\simple_fill\command\SimpleUndoCommand;
use rark\simple_fill\effect\Errors;
use rark\simple_fill\handler\EventListener;
use rark\simple_fill\item\AirFill;
use rark\simple_fill\item\SwitchMode;
use rark\simple_fill\libs\cortexpe\commando\PacketHooker;
use rark\simple_fill\obj\Container;
use rark\simple_fill\obj\Logger;
use rark\simple_fill\task\BlockPlaceTask;
use rark\simple_fill\task\RunningTasks;

class Loader extends PluginBase {
    const CONF_NAME = 'config.yml';
    const MAX_FILL_SIZE = 'MaxFillSize';
    const PLACE_SPEED = 'PlaceSpeed';
    const FILL_SIZE = 'FillSize';
    const SAVE_LOG_SIZE = 'SaveLogSize';
    protected static TaskScheduler $task_scheduler;

    protected function onEnable() : void {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        self::$task_scheduler = $this->getScheduler();
        $this->initItems();
        $this->applyConfData();
        EventListener::init();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->registerAll(
            $this->getName(),
            [
                new SimpleFillCommand($this),
                new SimpleUndoCommand($this)
            ]
        );
    }

    protected function onDisable() : void {
        RunningTasks::allStop();//todo rollback
    }

    public static function getTaskScheduler() : TaskScheduler {
        return self::$task_scheduler;
    }

    protected function initItems() : void {
        SwitchMode::init();
        AirFill::init();
    }

    protected function applyConfData() : void {
        $this->saveResource(self::CONF_NAME);
        $conf = $this->getConfig();
        RunningTasks::init((int) $conf->get(self::PLACE_SPEED, null) ?? throw new \RuntimeException(Errors::NOT_FOUND_PLACE_SPEED));
        BlockPlaceTask::init((int) $conf->get(self::FILL_SIZE, null) ?? throw new \RuntimeException(Errors::NOT_FOUND_FILL_SIZE));
        Logger::init((int) $conf->get(self::SAVE_LOG_SIZE, null) ?? throw new \RuntimeException(Errors::NOT_FOUND_SAVE_LOG_SIZE));
        Container::init((int) $conf->get(self::MAX_FILL_SIZE, null) ?? throw new \RuntimeException(Errors::NOT_FOUND_MAX_FILL_SIZE));
    }
}
