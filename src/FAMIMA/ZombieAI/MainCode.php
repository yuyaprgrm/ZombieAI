<?php

namespace FAMIMA\ZombieAI;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\Player;

class MainCode extends PluginBase {

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {

        if(!$sender instanceof Player)return false;
        switch($cmd->getName()) {

            case "spawnmob":
            // var_dump($sender->x, $sender->z);
            $exp = new RootExplorer($sender->level, [256, 256], [floor($sender->x), floor($sender->z)], floor($sender->y));
            $root = $exp->exploration();

            if($root !== null) {
                $task = new UpdateTask($this, $sender->level, $root, 256, 256, floor($sender->y));
                $this->getServer()->getScheduler()->scheduleRepeatingTask($task, 1);
            }
        }
    }
}