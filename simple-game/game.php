<?php
    require('./config.inc.php');
    require('./easybitcoin.php');
    session_start();

    if(isset($_POST['logout']) || !isset($_SESSION['nuid']) || $_SESSION['nuid'] == -1)
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
        session_destroy();
        header("Location: index.php");
    }

    $userData = $conn->prepare("SELECT * FROM game_users WHERE userid = :userid");
    $userData->bindParam(':userid', $userid);

    $userData->execute();

    $fetchUserData = $userData->fetchAll(PDO::FETCH_ASSOC);

    $username = $fetchUserData[0]['username'];
    $balance = $fetchUserData[0]['balance'];
    $deposit_address = $fetchUserData[0]['deposit_address'];

    // Game Code
    $winningNumber = mt_rand(1, 100);

    if(isset($_POST['greater']) || isset($_POST['exactly']) || isset($_POST['less']))
    {
        if($balance > 100)
        {
            switch ($_POST)
            {
                case isset($_POST['greater']):
                    $guess = 1;
                    if($winningNumber > 50)
                    {
                        $message = 'You WIN! +99 sats';
                        $message .= '<br>The computer picked '.$winningNumber;

                        $winlose = 1;

                        updateGame($_SESSION['nuid'], $winningNumber, $guess, $winlose, $conn);
                        updateBalance(($balance + 99), $_SESSION['nuid'], $conn);
                    }
                    else
                    {
                        $message = 'You LOSE! -100 sats';
                        $message .= '<br>The computer picked '.$winningNumber;

                        $winlose = 0;

                        updateGame($_SESSION['nuid'], $winningNumber, $guess, $winlose, $conn);
                        updateBalance(($balance -100), $_SESSION['nuid'], $conn);
                    }
                break;
                case isset($_POST['exacly']):
                    $guess = 0;
                    if($winningNumber == 50)
                    {
                        $message = 'You WIN! +9900 sats';
                        $message .= '<br>The computer picked '.$winningNumber;

                        $winlose = 1;

                        updateGame($_SESSION['nuid'], $winningNumber, $guess, $winlose, $conn);
                        updateBalance(($balance + 9900), $_SESSION['nuid'], $conn);
                    }
                    else
                    {
                        $message = 'You LOSE! -100 sats';
                        $message .= '<br>The computer picked '.$winningNumber;

                        $winlose = 0;

                        updateGame($_SESSION['nuid'], $winningNumber, $guess, $winlose, $conn);
                        updateBalance(($balance -100), $_SESSION['nuid'], $conn);
                    }
                break;
                case isset($_POST['less']):
                    $guess = -1;
                    if($winningNumber < 50)
                    {
                        $message = 'You WIN! +99 sats';
                        $message .= '<br>The computer picked '.$winningNumber;

                        $winlose = 1;

                        updateGame($_SESSION['nuid'], $winningNumber, $guess, $winlose, $conn);
                        updateBalance(($balance + 99), $_SESSION['nuid'], $conn);
                    }
                    else
                    {
                        $message = 'You LOSE! -100 sats';
                        $message .= '<br>The computer picked '.$winningNumber;

                        $winlose = 0;

                        updateGame($_SESSION['nuid'], $winningNumber, $guess, $winlose, $conn);
                        updateBalance(($balance -100), $_SESSION['nuid'], $conn);
                    }
                break;
            }

            // update the balance for the new game
            $balance = getBalance($_SESSION['nuid'], $conn);
        }
        else
        {
            $message = 'You need atleast 100 sats to play.<br>';
        }
    }
    
    function updateGame($player, $target, $guess, $winlose, $conn)
    {
        $insertGame = $conn->prepare("INSERT INTO game_table (player, target, guess, winlose) 
            VALUES(:player, :target, :guess, :winlose)");
        $insertGame->bindParam(':player', $player);
        $insertGame->bindParam(':target', $target);
        $insertGame->bindParam(':guess', $guess);
        $insertGame->bindParam(':winlose', $winlose);

        $insertGame->execute();
    }

    function updateBalance($newBalance, $userid, $conn)
    {
        $updateBalance = $conn->prepare("UPDATE game_users SET balance = :newBalance WHERE userid = :userid");
        $updateBalance->bindParam(':newBalance', $newBalance);
        $updateBalance->bindParam(':userid', $userid);

        $updateBalance->execute();
    }

    function getBalance($userid, $conn)
    {
        $getBalance = $conn->prepare("SELECT * FROM game_users WHERE userid = :userid");
        $getBalance->bindParam(':userid', $userid);

        $getBalance->execute();

        $fetchBalance = $getBalance->fetchAll(PDO::FETCH_ASSOC);
        $balance = $fetchBalance[0]['balance'];

        return $balance;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>
        <h4>Welcome, <?php echo $username; ?>.</h4><br>
        Your Balance: <?php echo $balance; ?> sats. <?php if($balance >= 547){ echo '<a href="withdraw.php">Withdraw balance.</a>'; }else{ echo 'Min. balance to withdraw 547 sats.'; } ?><br>
        Deposit Address: <?php echo $deposit_address; ?><br>
        <img src="https://chart.googleapis.com/chart?cht=qr&chs=125x125&chl=<?php echo $deposit_address; ?>">
        <hr>
        <?php if(isset($message)){ echo $message.'<hr>'; }?>
        <strong>RULES:</strong> The object of the game is to correctly guess if the computer will pick a number greater or less than 50.<br>
        Every guess will cost you 100 sats. If you guess correctly you will win 99 satoshis in return, if you guess wrong you recieve nothing.<br>
        <strong>BONUS:</strong> if you guess correctly with the EXACTLY 50 option you will win 9900 sats.<br>
        <hr>
        <?php if($balance <= 100){ echo '<h4>Make your guess!</h4>'; }else{ echo '<h4>Balance too low to play!</h4>'; } ?>
        <form method="post" action="?">
            <input type="submit" name="greater" value="Over 50" <?php if($balance < 100){ echo 'disabled '; } ?>/> || 
            <input type="submit" name="exactly" value="Exactly 50" <?php if($balance < 100){ echo 'disabled '; } ?>/> ||
            <input type="submit" name="less" value="Under 50" <?php if($balance < 100){ echo 'disabled '; } ?>/><br><br><br><br>
            <input type="submit" name="logout" value="Logout"/><br>
        </form>
    </body>
</html>