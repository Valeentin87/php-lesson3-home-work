<?php

// function readAllFunction(string $address) : string 
function readAllFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");
        
        $contents = ''; 
    
        while (!feof($file)) {
            $contents .= fread($file, 100);
        }
        
        fclose($file);
        return $contents;
    }
    else {
        return handleError("Файл не существует");
    }
}

// function addFunction(string $address) : string 
function addFunction(array $config) : string {
    $address = $config['storage']['address'];

    $name = readline("Введите имя: ");
    $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");
    $data = $name . ", " . $date . "\r\n";

    
    if((validateDate($date)) && validateName($name)) {
        $fileHandler = fopen($address, 'a');

        if(fwrite($fileHandler, $data)){
        return "Запись $data добавлена в файл $address"; 
        }
        else {
        return handleError("Произошла ошибка записи. Данные не сохранены");
        }

        fclose($fileHandler);
    }
    else {
        return handleError("Введены не валидные данные");
    }
    
}

// function clearFunction(string $address) : string 
function clearFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "w");
        
        fwrite($file, '');
        
        fclose($file);
        return "Файл очищен";
    }
    else {
        return handleError("Файл не существует");
    }
}

function helpFunction() {
    return handleHelp();
}

function readConfig(string $configAddress): array|false{
    return parse_ini_file($configAddress, true);
}

function readProfilesDirectory(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!is_dir($profilesDirectoryAddress)){
        mkdir($profilesDirectoryAddress);
    }

    $files = scandir($profilesDirectoryAddress);

    $result = "";

    if(count($files) > 2){
        foreach($files as $file){
            if(in_array($file, ['.', '..']))
                continue;
            
            $result .= $file . "\r\n";
        }
    }
    else {
        $result .= "Директория пуста \r\n";
    }

    return $result;
}

function readProfile(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!isset($_SERVER['argv'][2])){
        return handleError("Не указан файл профиля");
    }

    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";

    if(!file_exists($profileFileName)){
        return handleError("Файл $profileFileName не существует");
    }

    $contentJson = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);

    $info = "Имя: " . $contentArray['name'] . "\r\n";
    $info .= "Фамилия: " . $contentArray['lastname'] . "\r\n";

    return $info;
}

// функция getBirthdayToday проверяет наличие именинников на текущую дату

function getBirthdayToday(array $config) {

    $address = $config['storage']['address'];

    $today = date("d-m-Y");  

    print_r($today);

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");
        
        $current_line = '';
        $checkArray = [];
        $resultArray = []; 
    
        while (!feof($file)) {
            $current_line = fgets($file);
            $checkArray = explode(",", $current_line);
            if (trim($checkArray[1]) == $today) {
                array_push($resultArray, $checkArray[0]);
                
            }


        }
        
        fclose($file);
        $resultArray = "Сегодня необходимо поздравить: \r\n". implode(';',$resultArray) .PHP_EOL;
        return $resultArray;
    }
    else {
        return handleError("Файл не существует");
    }

}

// позволяет удалять данные из файла по введенному имени пользователя
function deleteUserName (array $config) {

    $address = $config['storage']['address'];  


    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "r+");

        $delName = readline('Введите имя пользователя, которого необходимо удалить:'.PHP_EOL);
        $resultInfo = '';
        $current_line = '';
        $checkArray = [];
        $newData = '';
        $flag = 0; 
    
        while (!feof($file)) {
            $current_line = fgets($file);
            $checkArray = explode(",", $current_line);
            if (trim($checkArray[0]) == $delName) {
                echo "пользователь с именем $delName найден и будет удален " .PHP_EOL;
                $flag++;
                continue;
            }
            $newData .= $current_line;
        }

        if ($flag) {
            file_put_contents($address, $newData);
            return "Удаление произошло успешно, файл перезаписан";
        }
        else {
            return "Пользователи с именем $delName в файле отсутствуют";
        }
        
        fclose($file);

    }
    else {
        return handleError("Файл не существует");
    }
    
}