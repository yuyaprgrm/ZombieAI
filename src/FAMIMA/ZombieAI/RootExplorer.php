<?php

namespace FAMIMA\ZombieAI;

class RootExplorer {

    /** @var Level */
    private $level;

    public function __construct($level, $start, $end, $y) {
        $this->level = $level;
        $this->start = $start;
        $this->end = $end;
        $this->y = $y;
    }

    public function exploration() {

        $start = $this->start;
        $end = $this->end;
        
        $roots[] = new Root($start[0], $start[1]);

        // var_dump($roots, $roots[0]->getNowXZ());
        $sx = min($start[0], $end[0])-60;
        $sz = min($start[1], $end[1])-60;

        $ex = max($start[0], $end[0])+60;
        $ez = max($start[1], $end[1])+60;

        for($x = $sx; $x <= $ex; $x++) {
            
            for($z = $sz; $z <= $ez; $z++) {
                $this->flag[$x][$z] = ($this->level->getBlockIdAt($x, $this->y, $z) === 0 && $this->level->getBlockIdAt($x, $this->y-1, $z) !== 0);
            }
        }

        // var_dump($this->flag);

        while(true) {

            $nRoots = [];

            foreach($roots as $root) {

                for($i = 0; $i < 4; $i++) {

                    if($this->canGoTo($root, $i)) {
                        // var_dump($root);
                        $newroot = clone $root;
                        $vector = $this->getVector($i);
                        $xz = $newroot->getNowXZ();
                        $x = $xz[0] + $vector[0];
                        $z = $xz[1] + $vector[1];
                        $newroot->addRoot($vector, [$x, $z]);
                        $nRoots[] = $newroot;
                        //var_dump([$x, $z]);
                        $this->flag[$x][$z] = false;
                        // print("1");
                        
                        if($x == $this->end[0] && $z == $this->end[1])return $newroot;
                    }
                }
                // var_dump($nRoots);
            }
            // var_dump(count($nRoots));
            if(!count($nRoots) > 0)
                return null;
            
            // var_dump($nRoots);

            $roots = $nRoots; // 更新

            // var_dump($roots);
                
        }
    }

    public function canGoTo($root, $number){
        $xz = $root->getNowXZ();
        $axz = $this->getVector($number);

        $x = $xz[0] + $axz[0];

        $z = $xz[1] + $axz[1];

        // var_dump($x, $z);

        return isset($this->flag[$x][$z]) ? $this->flag[$x][$z] : false;
    }

    public function getVector($number) {
        switch($number) {

            case 0:  $vector = [1,0]; break;
            case 1:  $vector = [0,1]; break;
            case 2:  $vector = [-1,0]; break;
            case 3:  $vector = [0,-1]; break;
            default: $vector = [0,0]; break;
        }

        return $vector;
    }
}