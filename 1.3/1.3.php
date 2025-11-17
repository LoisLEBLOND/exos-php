<?php

function repNombres($nombre){
        for ($i = 1; $i <= $nombre; $i++){
            for ($j = 1; $j <= $i; $j++){
                echo $i;
            }
            echo "<br>";
        }

}

repNombres(5);