<?php
    $bd = mysqli_connect($_ENV['DB_HOST'],$_ENV['DB_USER'],$_ENV['DB_PASS'],$_ENV['DB_BD']);
    // mysqli_set_charset($bd, 'utf8'); // Para formto en json
    if (!$bd) {
        echo 'No se conecto a la base de datos';
    }  
?>