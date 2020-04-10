<?php
    require('./config.inc.php');
    require('./easybitcoin.php');
    session_start();
    $_SESSION['nuid'] = -1;

    $error_username = '';
    $error_password = '';
    $error_password2 = '';

    if($_GET['run'] == "register")
    {
        $username = $_POST['username'];
        $password = $_POST['pw'];
        $password2 = $_POST['pw2'];

        mysqli_set_charset($conn, 'utf8');
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);
        $password2 = mysqli_real_escape_string($conn, $password2);

        $dupName = mysqli_query($conn, "SETECT username FROM game_users WHERE username = '$username'");
        $rowName = mysqli_num_rows($dupName) or die(mysqli_error($conn));

        if(trim($username) == '')
        {
            // no username
            $error_username = 'You must enter a username.';
        }
        elseif($rowName !=0)
        {
            // username in use
            $error_username = 'Username in use, select another.';
        }

        if(trim($password) == '')
        {
            // no password
            $error_password = 'You must enter a password.';
        }        
        
        if(trim($password2) == '' || $password != $password)
        {
            // no password 2 or don't match
            $error_password2 = 'Passwords do not match.';
        }
    
        if($error_username != '' || $error_password != '' || $error_password2 != '')
        {
            output($error_username, $error_password, $error_password2);
            die();
        }

        // Input checks passed, continue registration
        $userid = uniqid();
        $encPass = password_hash($password, PASSWORD_DEFAULT);
        $bitcoin = new Bitcoin($rpcUsername, $rpcPassword, $nodeIP);
        $address = $bitcoin->getnewaddress();
        $newUser = "INSERT INTO game_users (userid, username, userpass, deposit_address, balance) VALUES('$userid', $username', '$encPass', '$address', 0";
        $doNewUser = mysqli_query($conn, $newUser) or die(mysqli_error($conn));
        $_SESSION['nuid'] = $userid;

        header('Location: game.php');
    }
    else
    {
        output($error_username, $error_password, $error_password2);
        die();
    }

    function output($error_username, $error_password, $error_password2)
    {
?>

<!DOCTYPE html>
<html lang="en">
    <head></head>
    <body>
        <form method="post" action="?run=register">
            Username:<br><input type="text" id="username" maxlength="20"> <?php echo $error_username; ?><br>
            Password:<br><input type="password" id="pw" name="pw"> <?php echo $error_password; ?><br>
            Retype Password:<br><input type="password" id="pw2" name="pw2"> <?php echo $error_password2; ?><br>
            <input type="submit" name="submit" value="Register"/>
        </form>
    </body>
</html>

<?php
    }
?>