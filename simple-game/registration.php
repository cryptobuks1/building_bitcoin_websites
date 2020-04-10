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

        $dupName = $conn->prepare("SELECT count(*) FROM game_users WHERE username = :username");
        $dupName->bindParam(':username', $username);
        $dupName->execute();
        
        $rowName = $dupName->fetchColumn();

        if($rowName != 0)
        {
            // username in use
            $error_username = 'Username in use, select another.';
        }
        elseif(trim($username) == '')
        {
            // no username
            $error_username = 'You must enter a username.';
        }
        
        if(trim($password) == '')
        {
            // no password
            $error_password = 'You must enter a password.';
        }
        
        if(trim($password2) == '')
        {
            // no password 2 or don't match
            $error_password2 = 'You must enter a matching password.';
        }
        elseif($password != $password2)
        {
            // no password 2 or don't match
            $error_password2 = 'Passwords do not match.';
        }
        
        if($error_username != '' || $error_password != '' || $error_password2 != '')
        {
            output($error_username, $error_password, $error_password2);
            die();
        }
        else
        {
            // Input checks passed, continue registration
            $balance = 0;
            $userid = uniqid();
            $encPass = password_hash($password, PASSWORD_DEFAULT);

            $bitcoin = new Bitcoin($rpcUsername, $rpcPassword, $nodeIP);
            $address = $bitcoin->getnewaddress();

            $newUser = $conn->prepare("INSERT INTO game_users (userid, username, userpass, deposit_address, balance) 
                VALUES(:userid, :username, :encPass, :newaddress, :balance)");

            $newUser->bindParam(':userid', $userid);
            $newUser->bindParam(':username', $username);
            $newUser->bindParam(':encPass', $encPass);
            $newUser->bindParam(':newaddress', $address);
            $newUser->bindParam(':balance', $balance);

            $newUser->execute();
            $_SESSION['nuid'] = $userid;

            header('Location: game.php');
        }
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
            Username:<br><input type="text" id="username" name="username" maxlength="20"> <?php echo $error_username; ?><br>
            Password:<br><input type="password" id="pw" name="pw"> <?php echo $error_password; ?><br>
            Retype Password:<br><input type="password" id="pw2" name="pw2"> <?php echo $error_password2; ?><br>
            <input type="submit" name="submit" value="Register"/>
        </form>
    </body>
</html>

<?php
    }
?>