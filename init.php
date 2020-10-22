<?php
    // TO DO: default, paid, earned module settings modification

    session_start();

    $GLOBALS['config'] = array(

        'mysql' => array(
            'host' => '127.0.0.1',
            'username' => 'schimpy.cz',
            'password' => 'Alejdete0416',
            'db' => 'schimpycz2'
        ),

        'remember' => array(
            'cookie_name' => 'hash',
            'cookie_expiration' => 604800
        ),

        'sessions' => array(
            'session_name' => 'user',
            'token_name' => 'token'
        ),

        'domain' => array(
            'name' => 'English Quest',
            'url' => 'http://schimpy.cz/eq/'
        ),

        'avatar' => array(
            'path' => 'img/avatar/',
            'default' => 'default.png',
            'extension' => 'png'
        ),

        'defaultModule' => array(
            'width' => '100%',
            'height' => '150px'
        )
    );

    spl_autoload_register(function($class) {
        require_once 'classes/' . $class . '.php';
    });

    require_once 'functions/sanitize.php';
