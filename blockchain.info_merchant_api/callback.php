<?php
    /************************************
     * Modified examples from the book  *
     * Building Bitcoin Websites        *
     * by Kyle Honeycutt @coinableS     *
     * ISBN 153494544X                  *
     ************************************/

    $secret = hash("tiger192,4", "one, two, three o'clock, four o'clock rock!");

    if($_GET['secret'] != $secret)
    {
        die('Stop doing that!');
    }
    else
    {
        // automatically generated $_GET[] - value, confirmations, address, transaction_hash
        $orderNum = $_GET['invoice'];
        $amount = $_GET['value'];
        $amountCalc = $amount / 100000000;  

        $queryUpdate = "UPDATE orders 
                        SET paid = 1, recd = $amount
                        WHERE orderID = '$orderNum'";
        $doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
        if($doUpdate)
        {
            echo '*ok*';
        }
    }
?>