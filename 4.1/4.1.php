<?php

function calcMoy(){
    $eleves = [
 ["nom" => "Alice", "notes" => [15, 14, 16]],
 ["nom" => "Bob", "notes" => [12, 10, 11]],
 ["nom" => "Claire", "notes" => [18, 17, 16]]
];
    foreach($eleves as $eleve){
    $r = array_sum($eleve["notes"]);
    $moy = $r/count($eleve["notes"]);
    echo "Nom: " . $eleve["nom"] . ", Moyenne: " . $moy . "<br>";
    }
}

calcMoy();