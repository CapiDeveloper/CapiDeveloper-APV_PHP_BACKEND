<?php

    require __DIR__.'/../vendor/autoload.php';

    // Para variables de entorno
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    require 'bd.php';
    require 'functions.php';

    //Conexcion a bd
    use Model\ActiveRecord;
    ActiveRecord::conectarDB($bd);
    
?>