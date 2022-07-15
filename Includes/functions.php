<?php
    function debuguear($parametro){
        echo '<pre>';
        var_dump($parametro);
        echo '</pre>';
        exit;
    }

    
    function auth(){
        if(!$_SESSION['login']){
            header('location: /api');
        }
    }
    
?>