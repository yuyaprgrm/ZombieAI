<?php

namespace FAMIMA\ZombieAI;

class Root {

    private $root;

    private $nowxz;

    public function __construct($x, $z) {
        $this->root = [];
        $this->nowxz = [$x, $z];
    }

    public function addRoot(array $root, $xz){
        $this->root[] = $root;
        $this->nowxz = [$xz[0], $xz[1]];
    }

    public function getNowXZ(){
        return $this->nowxz;
    }

    public function getRoot($n) {
        return isset($this->root[$n]) ? $this->root[$n] : null;
    }
}