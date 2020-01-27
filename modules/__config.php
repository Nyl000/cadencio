<?php

$files = scandir(__DIR__);
foreach($files as $file) {
    $pathFile = __DIR__.'/'.$file;
    if($file!== '.' && $file !== '..' && is_dir($pathFile)) {
        $pathFileMain = $pathFile.'/back/main.php';
        if (file_exists($pathFileMain)) {
            require_once $pathFileMain;
        }
    }
}