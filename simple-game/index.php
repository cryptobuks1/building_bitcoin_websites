<?php
    session_start();

    if(isset($_SESSION['nuid']) && $_SESSION['nuid'] != -1)
    {
        header('Location: game.php');
    }
    elseif(isset($_POST['register']) || isset($_POST['login']) || isset($_POST['leave']))
    {
        switch ($_POST)
        {
            case isset($_POST['register']):
                header('Location: registration.php');
            break;
            case isset($_POST['login']):
                header('Location: login.php');
            break;
            case isset($_POST['leave']):
                header('Location: https://bitcoincore.org/');
            break;
        }
    }
    else
    {
?>
<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>
        <h4>Welcome.</h4><br>
        <strong>RULES:</strong> The object of the game is to correctly guess if the computer will pick a number greater or less than 50.<br>
        Every guess will cost you 100 sats. If you guess correctly you will win 99 satoshis in return, if you guess wrong you recieve nothing.<br>
        <strong>BONUS:</strong> if you guess correctly with the EXACTLY 50 option you will win 9900 sats.<br>
        <hr>
        <h4>Make your choice!</h4>
        <form method="post" action="index.php">
            <input type="submit" name="register" value="Register"/> || 
            <input type="submit" name="login" value="Login"/> || 
            <input type="submit" name="leave" value="Leave"/><br>
        </form>
    </body>
</html>
<?php
    }
?>