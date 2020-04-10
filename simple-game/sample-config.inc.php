<?php
    // Database
    $DB_host = "localhost";
    $DB_user = "user";
    $DB_pass = "pass";
    $DB_name = "database";

    try
    {
        $conn = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }

    // Bitcoin RPC
    $rpcUsername = "user";
    $rpcPassword = "pass";
    $nodeIP = "ip"; // optional
    //$nodePort = ""; // optional
?>