<?php
function verifAge ($age){
    switch($age){
        case ($age <=3):
            echo "creche";
            break;
        case ($age >3 && $age <=6):
            echo "maternelle";
            break;
        case ($age >6 && $age <=11):
            echo "primaire";
            break;
        case ($age >11 && $age <=16):
            echo "college";
            break;
        case ($age >16 && $age <=18):
            echo "lycee";
            break;
    }
}

verifAge(18);