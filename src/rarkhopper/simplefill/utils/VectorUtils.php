<?php

declare(strict_types = 1);

namespace rark\simple_fill\utils;

use ErrorException;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;
use rark\simple_fill\effect\Errors;
use function explode;
use function max;
use function min;
use function serialize;
use function unserialize;

abstract class VectorUtils {
    final private function __construct() {/** NOOP */
    }

    public static function getBB(Vector3 $v1, Vector3 $v2) : AxisAlignedBB {
        self::sortVector($v1, $v2);
        return new AxisAlignedBB(
            $v1->x,
            $v1->y,
            $v1->z,
            $v2->x,
            $v2->y,
            $v2->z
        );
    }

    public static function sortVector(Vector3 &$v1, Vector3 &$v2) : void {
        $v1 = $v1->asVector3();
        $v2 = $v2->asVector3();
        $dat = [
            [$v1->x, $v2->x],
            [$v1->y, $v2->y],
            [$v1->z, $v2->z]
        ];
        $v1->x = min($dat[0]);
        $v2->x = max($dat[0]);
        $v1->y = min($dat[1]);
        $v2->y = max($dat[1]);
        $v1->z = min($dat[2]);
        $v2->z = max($dat[2]);
    }

    public static function sortVector2(Vector2 &$v1, Vector2 &$v2) : void {
        $dat = [
            [$v1->x, $v2->x],
            [$v1->y, $v2->y],
        ];
        $v1->x = min($dat[0]);
        $v2->x = max($dat[0]);
        $v1->y = min($dat[1]);
        $v2->y = max($dat[1]);
    }

    public static function convertToVector2(Vector3 $v) : Vector2 {
        return new Vector2($v->x, $v->z);
    }

    public static function getDiff(Vector3 $v1, Vector3 $v2) : Vector3 {
        return new Vector3($v2->x - $v1->x, $v2->y - $v1->y, $v2->z - $v1->z,);
    }

    public static function vectorSerialize(Vector3 $v) : string {
        return serialize($v->asVector3());
    }

    public static function vectorUnserialize(string $str_v) : mixed {
        return unserialize($str_v);
    }

    public static function positionSerialize(Position $pos) : string {
        return self::vectorSerialize($pos) . "\t{$pos->getWorld()->getFolderName()}";
    }

    public static function positionUnserialize(string $str_pos) : ?Position {
        $dat = explode("\t", $str_pos);

        if (!isset($dat[1])) {
            return null;
        }
        $v = self::vectorUnserialize((string) $dat[0]);

        if (!$v instanceof Vector3) {
            return null;
        }
        return new Position(
            $v->x,
            $v->y,
            $v->z,
            Server::getInstance()->getWorldManager()->getWorldByName($dat[1])
        );
    }

    public static function arrayToPosition(array $array) : ?Position {
        try {
            $world = Server::getInstance()->getWorldManager()->getWorldByName($array['world']) ?? throw new ErrorException(Errors::WORLD_IS_NULL);
            return new Position((float) $array['x'], (float) $array['y'], (float) $array['z'], $world);
        } catch(\Exception) {
            return null;
        }
    }
}
