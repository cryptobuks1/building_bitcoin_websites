<?php
    // For obvious security reasons a change of name for this file to something completely obscure is advisable.

    require('./config.inc.php');
    require('./easybitcoin.php');
    require('./tools.php');             // sats-btc and btc-sats conversion functions

    $bitcoin = new Bitcoin($rpcUsername, $rpcPassword, $nodeIP);

    $tx = $_GET['tx'];
    $tx = mysqli_real_escape_string($conn, $tx);
    $getTransaction = $bitcoin->gettransaction($tx);
    
    $confirmations = $getTransaction['confirmations'];
    if($confirmations < 1)
    {
        die();
    }
    else
    {
        $countDetails = count($getTransaction['details']);
        for($i=0; $i < $countDetails; $i++)
        {
            $getAddress = $getTransaction['details'][$i]['address'];
            $getReceive = $getTransaction['details'][$i]['category'];
            if($getReceive == "receive")
            {
                $checkAddress = mysqli_query($conn, "SELECT deposit_address FROM users WHERE deposit_address = '$getAddress'");
                $doCheckAddress = mysqli_num_rows($checkAddress) or die(mysqli_error($conn));
                if($doCheckAddress == 1)
                {
                    $amount = $getTransaction['details'][$i]['amount'];
                    $amount = toSats($amount);

                    $updateBalance = "UPDATE game_users SET balance = balance + '$amount' WHERE deposit_address = '$getAddress'";
                    $doUpdateBalance = mysqli_query($conn, $updateBalance) or die(mysqli_error($conn));
                }
            }
        }
    }

?>