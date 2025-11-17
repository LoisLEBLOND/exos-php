<?php

function my_strrev($n){
    $mot = "";
    for ($i = strlen($n); $i >= 0; $i--){
        $mot .= substr($n, $i, 1);
    }
    return $mot;
}

echo my_strrev("Hello World!");