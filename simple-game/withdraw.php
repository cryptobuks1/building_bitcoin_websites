<?php
    // For obvious security reasons a change of name for this file to something completely obscure is advisable.

    require('./config.inc.php');
    require('./easybitcoin.php');
    require('./tools.php');             // sats-btc and btc-sats conversion functions
    session_start();

    if(!isset($_SESSION['nuid']) || $_SESSION['nuid'] == -1)
    {
        session_destroy();
        header('Location: index.php');
    }

    // Check if the user is actually logged in
    $userid = $_SESSION['nuid'];
    $trueLogin = $conn->prepare("SELECT count(*) FROM game_users WHERE userid = :userid");
    $trueLogin->bindParam(':userid', $userid);

    $trueLogin->execute();
    $numRows = $trueLogin->fetchColumn();

    if($numRows != 1)
    {
        header("Location: index.php");
    }
    else
    {
        $bitcoin = new Bitcoin($rpcUsername, $rpcPassword, $nodeIP);

        $getUserData = $conn->prepare("SELECT * FROM game_users WHERE userid = :userid");
        $getUserData->bindParam(':userid', $userid);

        $getUserData->execute();

        $fetchUserData = $getUserData->fetchAll(PDO::FETCH_ASSOC);

        $balance = $fetchUserData[0]['balance'];
        $username = $fetchUserData[0]['username'];

        if(isset($_POST['withdraw']))
        {
            $address = $_POST['address'];

            $checkValid = $bitcoin->validateaddress($address);
            $isValid = $checkValid['isvalid'];

            if($balance >= 547)
            {
                // Check valid address
                if(!$isValid)
                {
                    // Invalid address
                    $error_address = 'Invalid bitcoin address.';
                }
                else
                {
                    // Process withdrawal
                    // Fee calcs
                    // Fees payed by the user prevents attacks draining any surplus funds in the wallet
                    $smartFee = $bitcoin->estimatesmartfee(6);
                    $fee = toSats($smartFee['feerate']);

                    $sats = $balance - (0.25 * $fee); // Segwit transactions are roubghly 25% cheaper
                    $btc = toBTC($sats);
                    $withdrawBTC = number_format($btc, 8);

                    // Process withdrawl
                    $doWithdrawal = $bitcoin->sendtoaddress($address, $withdrawBTC);

                    if($doWithdrawal)
                    {
                        $message = 'Transaction: <a href="https://www.smartbit.com.au/tx/'.$doWithdrawal.'" target="_blank">'.$doWithdrawal.'</a>';

                        $updateBalace = $conn->prepare("UPDATE game_users SET balance = 0 WHERE userid = :userid");
                        $updateBalace->bindParam(':userid', $_SESSION['nuid']);

                        $updateBalace->execute();

                        $balance = 0;
                    }
                    else
                    {
                        $message = 'Transaction error';
                        echo 'TX Error: <pre>'; print_r($doWithdrawal); echo '</pre><br>';
                    }
                }
            }
            else
            {
                $message = 'Balance is less than minimum sats required (547 sats) for the transaction to not be considered dust.';
            }
        }
        
        // Display withdrawal form
?>

<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>
        <h4>Welcome, <?php echo $username; ?>.</h4><br>
        Your Balance: <?php echo $balance; ?> sats.<br>
        <hr>
        <?php if(isset($message)){ echo $message.'<hr>'; }?>
        <h4>Withdraw balance:</h4>
        <form method="post" action="?">
            Wirhdraw to this Address: <input type="text" name="address" size="60"/> <?php echo $error_address; ?><br>
            <input type="submit" name="withdraw" value="Withdraw"/><br>
        </form>
    </body>
</html>

<?php
    }
?>