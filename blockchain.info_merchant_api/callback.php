<?php
    /************************************
     * Modified examples from the book  *
     * Building Bitcoin Websites        *
     * by Kyle Honeycutt @coinableS     *
     * ISBN 153494544X                  *
     ************************************/

    require_once('config.inc.php');
    $confirmations = $_GET['confirmations'];

    if($_GET['secret'] != $secret)
    {
        die('Stop doing that!');
    }
    else
    {
        if ($confirmations <= $setting_confirmations) 
        {
            //Insert into pending payments
            //Don't print *ok* so the notification resent again on next confirmation

            // Update db with confirmation status of payment
            $queryUpdate = "UPDATE orders 
                            SET paid = $confirmations
                            WHERE orderID = '$orderNum'";

            mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
        }
        else
        {
            // Automatically generated $_GET[] - invoice, value, confirmations, address, transaction_hash
            $orderNum = $_GET['invoice'];
            $amount = $_GET['value'];
            $amountCalc = $amount / 100000000;  

            $queryUpdate = "UPDATE orders 
                            SET paid = $confirmations, recd = $amount
                            WHERE orderID = '$orderNum'";
            $doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
            if($doUpdate)
            {
                echo '*ok*';
            }
        }
    }
?>