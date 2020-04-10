<?php
    // For obvious security reasons a change of name for this file to something completely obscure is advisable.

    require('./config.inc.php');
    require('./easybitcoin.php');
    require('./tools.php');             // sats-btc and btc-sats conversion functions

    if(!isset($_SESSION['nuid']) || $_SESSION['nuid'] == -1)
    {
        session_destroy();
        header('Location: index.php');
    }

    // Check if the user is actually logged in
    $userid = $_SESSION['nuid'];
    $trueLogin = "SELECT * FROM game_users WHERE userid = '$userid'";
    $doTrueLogin = mysqli_query($conn, $trueLogin);
    $numRows = mysqli_num_rows($doTrueLogin) or die(mysqli_error($conn));

    if($numRows != 1)
    {
        header("Location: index.php");
    }
    else
    {
        $bitcoin = new Bitcoin($rpcUsername, $rpcPassword, $nodeIP);

        $fetchUserData = mysqli_fetch_assoc($doTrueLogin);
        $balance = $fetchUserData['balance'];
        $username = $fetchUserData['username'];

        if(isset($_POST['withdraw']))
        {
            $address = $bitcoin->validateaddress($address);
            $isValid = $address['isvalid'];
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
                    $btc = toBTC($balance);
                    $btc = number_format($btc, 8);
                    $doWithdrawal = $bitcoin->sendtoaddress($address, $btc);
                    $message = 'Transaction: <a href="https://www.smartbit.com.au/tx/">'.$doWithdrawal.'</a>';

                    $updateBalace = "UPDATE game_users SET balance = 0 WHERE userid = '".$_SESSION['nuid']."'";
                    $doUpdateBalance = mysqli_query($conn, $updateBalace);
                    $balance = 0;
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
        Your Balance: <?php echo $balance; ?> sats. <?php if($balance >= 547){ echo '<a href="withdraw.php">Withdraw balance.</a>'; }else{ echo 'Min balance to withdraw 547 sats.'; } ?><br>
        <hr>
        <?php if(isset($message)){ echo $message.'<hr>'; }?>
        <h4>Withdrraw balance:</h4>
        <form method="post" action="?">
            Wirhdraw to this Address: <input type="text" name="address" size="60"/> <?php echo $error_address; ?><br>
            <input type="submit" name="withdraw" value="Withdraw"/><br>
        </form>
    </body>
</html>

<?php
    }
?>