<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use rarkhopper\simplefill\command\SimpleFillCommand;
use rarkhopper\simplefill\command\SimpleUndoCommand;
use rarkhopper\simplefill\effect\Errors;
use rarkhopper\simplefill\handler\BlockBreakHandler;
use rarkhopper\simplefill\handler\BlockPlaceHandler;
use rarkhopper\simplefill\handler\ItemUseHandler;
use rarkhopper\simplefill\item\AirFill;
use rarkhopper\simplefill\item\SwitchMode;
use rarkhopper\simplefill\obj\Container;
use rarkhopper\simplefill\obj\Logger;
use rarkhopper\simplefill\task\BlockPlaceTask;
use rarkhopper\simplefill\task\RunningTasks;

class Loader extends PluginBase {
    const CONF_NAME = 'config.yml';
    const MAX_FILL_SIZE = 'MaxFillSize';
    const PLACE_SPEED = 'PlaceSpeed';
    const FILL_SIZE = 'FillSize';
    const SAVE_LOG_SIZE = 'SaveLogSize';
    protected static TaskScheduler $task_scheduler;

    protected function onEnable() : void {
        self::$task_scheduler = $this->getScheduler();
        $this->initItems();
        $this->applyConfData();
        $this->getServer()->getPluginManager()->registerEvents(new BlockBreakHandler(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BlockPlaceHandler(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ItemUseHandler(), $this);
        $this->getServer()->getCommandMap()->registerAll(
            $this->getName(),
            [
                new SimpleFillCommand(),
                new SimpleUndoCommand()
            ]
        );
    }

    protected function onDisable() : void {
        RunningTasks::allStop(); //todo rollback
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
