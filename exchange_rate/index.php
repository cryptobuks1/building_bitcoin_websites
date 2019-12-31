<?php
    $url = "https://api.coinbase.com/v2/prices/spot?currency=USD";
    $fileGet = file_get_contents($url);
    $json = json_decode($fileGet, TRUE);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>BTC Price</title>
        <link href="https://fonts.googleapis.com/css?family=Ubuntu:700italic" rel="stylesheet" type="text/css">
        <style>
            #container 
            {
                font-size: 40px;
                font-family: 'Ubuntu', sans-serif;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <img src="./img/bitcoin.png" alt="Bitcoin"><br>
            $<?php echo $json['data']['amount']; ?>
        </div>
    </body>
</html>