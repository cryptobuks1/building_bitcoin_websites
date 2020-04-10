<?php
    // For obvious security reasons a change of name for this file to something completely obscure is advisable.

    require('./config.inc.php');
    require('./easybitcoin.php');
    require('./tools.php');             // sats-btc and btc-sats conversion functions

    $bitcoin = new Bitcoin($rpcUsername, $rpcPassword, $nodeIP);

    $tx = $_GET['tx'];

    //$tx = mysqli_real_escape_string($conn, $tx); // unsure how to sanitise tx here without mysqli, needs obvsuration of file name min as a result!
    $getTransaction = $bitcoin->gettransaction($tx);
    
    $confirmations = $getTransaction['confirmations']; // has the tx been confirmed yet?
    if($confirmations < 1)
    {
        die();
    }
    else
    {
        // TX confirmed
        $countDetails = count($getTransaction['details']);
        for($i=0; $i < $countDetails; $i++)
        {
            $getAddress = $getTransaction['details'][$i]['address'];
            $getReceive = $getTransaction['details'][$i]['category'];

            if($getReceive == "receive") // ensure its a deposit tx
            {
                $checkAddress = $conn->prepare("SELECT count(*) FROM game_users WHERE deposit_address = :getAddress");
                $checkAddress->bindParam(':getAddress', $getAddress);
                $checkAddress->execute();

                $doCheckAddress = $checkAddress->fetchColumn();

                if($doCheckAddress == 1)
                {
                    $getAddress = $getTransaction['details'][$i]['address'];
                    $amount = $getTransaction['details'][$i]['amount'];
                    $amount = toSats($amount);

                    $updateBalance = $conn->prepare("UPDATE game_users SET balance = balance + :amount WHERE deposit_address = :address");
                    $updateBalance->bindParam(':amount', $amount);
                    $updateBalance->bindParam(':address', $getAddress);

                    $updateBalance->execute();
                }
            }
        }
    }

?>