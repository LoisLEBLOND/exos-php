<?php

function calcMoy(){
    $tableau = [12, 5, 6, 78, 1, 45];
    $r = array_sum($tableau);
    $moy = $r/count($tableau);
    echo $moy;
}

calcMoy();