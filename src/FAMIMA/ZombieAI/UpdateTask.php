<?php

namespace FAMIMA\ZombieAI;

use pocketmine\scheduler\Task;
use pocketmine\entity\Entity;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\MoveEntityPacket;


class UpdateTask extends Task {

    private $eid;

    private $count = 0;
    
    public function __construct($main, $level, $root, $sx, $sz, $y, $player) {
        $this->main = $main;
        $this->player = $player;

        $this->root = $root;
        $this->xz = [$sx, $sz];
        $this->y = $y;
        $this->level = $level;
        $count = 0;
        $eid = 20001 + mt_rand(0, 2000);
        $this->eid = $eid;

        $pk = new AddEntityPacket();
        $pk->eid = $eid;
        $pk->type = 32;
        $pk->x = $sx;
        $pk->y = $y;
        $pk->z = $sz;

        $pk->speedX =
        $pk->speedY =
        $pk->speedZ = 0;

        $pk->yaw = 0;
        $pk->pitch = 0;

        $flags = 0;
		@$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
		@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
		@$flags |= 1 << Entity::DATA_FLAG_IMMOBILE;

		$pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
	    	Entity::DATA_AIR => [Entity::DATA_TYPE_SHORT, 400],
		    Entity::DATA_MAX_AIR => [Entity::DATA_TYPE_SHORT, 400],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, "AI-Zombie"],
			Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG,-1]
		];

        foreach($level->getPlayers() as $player) {
            $player->dataPacket($pk);
        }
        $this->mtask = true;
        $this->mcount = 0;
    }

    public function onRun($tick) {
        if(!$this->mtask) {
            $this->count++;
            $this->mtask = true;
        }
        $vxz = $this->root->getRoot($this->count);
        // var_dump($vxz);
        
        if($vxz === null){
            $exp = new RootExplorer($this->player->level, [$this->xz[0], $this->xz[1]], [floor($this->player->x), floor($this->player->z)], $this->y);
            $this->root = $exp->exploration();

            if($this->root === null){
                $id = $this->getTaskId();
                $this->main->getServer()->getScheduler()->cancelTask($id);
            }

            return 0;
        }

        if($this->count > 10) {
            $this->count = 0;
            $exp = new RootExplorer($this->player->level, [$this->xz[0], $this->xz[1]], [floor($this->player->x), floor($this->player->z)], $this->y);
            $this->root = ($r = $exp->exploration()) === null ? $this->root : $r;
            return 0;
        }

        $x = $this->xz[0] + $vxz[0]*$this->mcount*0.2;
        $z = $this->xz[1] + $vxz[1]*$this->mcount*0.2;

        $pk = new MoveEntityPacket();
        $pk->eid = $this->eid;
        $pk->x = $x+0.5;
        $pk->z = $z+0.5;
        $pk->y = $this->y;
        if($vxz[0] === 0){
            if($vxz[1] === 1){
                $yaw = 0;
            }else{
                $yaw = 180;
            }
        }else{
            if($vxz[0] === -1){
                $yaw = 90;
            }else{
                $yaw = 270;
            }
        }
        $pk->yaw = $yaw;
        $pk->headYaw = $yaw;
        $pk->pitch = 0;
        $this->mcount += 1;

        if($this->mcount > 5){
            $this->mtask = false;
            $this->mcount = 0;
            $this->xz[0] += $vxz[0];
            $this->xz[1] += $vxz[1];
        }
        foreach($this->level->getPlayers() as $player) {
            $player->dataPacket($pk);
        }

    }


}