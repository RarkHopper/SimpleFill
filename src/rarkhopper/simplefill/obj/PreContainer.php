<?php

declare(strict_types = 1);

namespace rark\simple_fill\obj;

use pocketmine\math\Vector3;

class PreContainer {
    protected ?Vector3 $v1 = null;
    protected ?Vector3 $v2 = null;

    public function push(Vector3 $v) : void {
        if (!$this->isSetV1()) {
            $this->pushV1($v);
        } elseif (!$this->isSetV2()) {
            $this->pushV2($v);
        }
    }

    protected function pushV1(Vector3 $v1) : void {
        $this->v1 = $v1;
    }

    protected function pushV2(Vector3 $v2) : void {
        $this->v2 = $v2;
    }

    public function isComplete() : bool {
        return $this->isSetV1() && $this->isSetV2();
    }

    protected function isSetV1() : bool {
        return $this->v1 !== null;
    }

    protected function isSetV2() : bool {
        return $this->v2 !== null;
    }

    public function parse() : ?Container {
        if (!$this->isComplete()) {
            return null;
        }
        return new Container($this->v1, $this->v2);
    }
}
