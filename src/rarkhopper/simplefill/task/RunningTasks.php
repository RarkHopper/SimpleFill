<?php

declare(strict_types = 1);

namespace rarkhopper\simplefill\task;

use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use rarkhopper\simplefill\effect\Errors;
use rarkhopper\simplefill\Loader;
use rarkhopper\simplefill\obj\Container;
use function array_filter;

abstract class RunningTasks {
    final public function __construct() {/** NOOP */
    }
    /** @var BlockPlaceTask[] */
    protected static array $tasks = [];
    /** @var TaskHandler[] */
    protected static array $handlers = [];
    protected static int $ids = 0;
    protected static int $place_speed;

    public static function init(int $place_speed) : void {
        if ($place_speed < 1) {
            throw new \InvalidArgumentException(Errors::INVALID_PLACE_SPEED);
        }
        self::$place_speed = $place_speed;
    }

    public static function stop(int $id) : void {
        if (!isset(self::$tasks[$id]) || !isset(self::$handlers[$id])) {
            return;
        }
        self::$handlers[$id]->cancel();
        unset(self::$tasks[$id]);
        unset(self::$handlers[$id]);
        self::$tasks = array_filter(self::$tasks);
        self::$handlers = array_filter(self::$handlers);
    }

    public static function run(Container $container, ?Player $holder = null) : void {
        $task = new BlockPlaceTask($container, $holder, ++self::$ids);
        self::$handlers[$task->getId()] = Loader::getTaskScheduler()->scheduleRepeatingTask($task, self::$place_speed);
        self::$tasks[$task->getId()] = $task;
    }

    public static function allStop() : void {
        foreach (self::$tasks as $task) {
            $task->rollback();
        }
        foreach (self::$handlers as $handler) {
            $handler->cancel();
        }
        self::$tasks = [];
    }
}
