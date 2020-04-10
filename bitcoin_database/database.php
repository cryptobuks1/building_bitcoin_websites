<?php
    require('./config.inc.php');

    if(mysqli_connect_errno())
    {
        die("Connection to Database Failed" . mysqli_connect_error($conn));
    }

    // Create and add a new user
    $uid = uniqid();
    $username = "coinableS";
    $address = "1NPrfWgJfkANmd1jt88A141PjhiarT8d9U";

    $addUser = "INSERT INTO siteusers (userid, username, address) VALUES('$uid', '$username', '$address')";
    $doAddUser = mysqli_query($conn, $addUser) or die(mysqli_error($conn));

    // Select user data from the database
    $select = "SELECT * FROM siteusers WHERE username = 'coinableS'";
    $doSelect = mysqli_query($conn, $select) or die(mysqli_error($conn));
    $fetchSelect = mysqli_fetch_assoc($doSelect);
    $getAddy = $fetchSelect['address'];

    echo $getAddy;

    // Update user data in the database
    $newAddress = "1J9ikqFuwrzPbczsDkquA9uVYeq6dEehsj";
    
    $updateAddress = "UPDATE siteusers SET address = '$newAddress' WHERE username = 'coinableS'";
    $doUpdate = mysqli_query($conn, $updateAddress) or die(mysqli_error($conn));

    // SQL Injection preventions in procedural PHP
    mysqli_set_charset($conn, 'utf8');
    $username = $_POST['uname'];
    $username = mysqli_real_escape_string($conn, $username);
    $insert = "INSERT INTO table (username) VALEUS ('$username')";

?>