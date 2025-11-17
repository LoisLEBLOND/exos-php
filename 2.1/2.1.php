<?php

function calcMoy(){
    $tableau = [15, 17.7, 20];
    $r = array_sum($tableau);
    $moy = $r/count($tableau);
    echo $moy;
}

calcMoy();