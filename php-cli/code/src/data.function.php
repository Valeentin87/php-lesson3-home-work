<?php
/* пока пустой
*/

function validateName(string $name):bool {
  
    return ctype_alpha($name);
}


function validateDate(string $date): bool {
    $dateBlocks = explode("-", $date);



    if(count($dateBlocks) < 3){

        return false;
    }

    if(isset($dateBlocks[0]) && $dateBlocks[0] > 31) {
        return false;
    }

    if(isset($dateBlocks[1]) && $dateBlocks[1] > 12) {
        return false;
    }

    if((isset($dateBlocks[2]) && $dateBlocks[2] > date('Y')) || ($dateBlocks[2] < (date('Y') - 100))) {
        return false;
    }

    return true;
}
