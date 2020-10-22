<?php

    // Define DB access
    define('DBHOST', '127.0.0.1');
    define('DBNAME', 'schimpycz2');
    define('DBUSER', 'schimpy.cz');
    define('DBPASS', 'Alejdete0416');

    // Set the corect timezone
    date_default_timezone_set("Europe/Prague");

    // Connect
    try {
        $db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo '<p class="err">'.$e->getMessage().'</p>';
    }

