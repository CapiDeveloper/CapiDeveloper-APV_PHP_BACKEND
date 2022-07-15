<?php

    require __DIR__.'/../vendor/autoload.php';

    // Para variables de entorno
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    require_once __DIR__.'./bd.php';
    require_once __DIR__.'./functions.php';

    //Conexcion a bd
    use Model\ActiveRecord;
    ActiveRecord::conectarDB($bd);
?>