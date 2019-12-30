<?php
    $url = "https://api.coinbase.com/v2/prices/spot?currency=USD";
    $fileGet = file_get_contents($url);
    $json = json_decode($fileGet, TRUE);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>BTC <-> USD Converter</title>
        <link href="https://fonts.googleapis.com/css?family=Ubuntu:700italic" rel="stylesheet" type="text/css">
        <style>
            #container
            {
                font-size: 40px;
                font-family: 'Ubuntu', sans-serif;
                text-align: center; 
            }
            #btc, #usd
            {
                width: 150px;
                height: 40px;
                border: 2px solid #333;
                border-radius: 5px;
                font-size: 22px;
                font-family: 'Ubuntu', sans-serif;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <img src="./img/bitcoin.png" alt="bitcoin"><br>
            <input type="text" id="btc" placeholder="btc" onchange="btcConvert()" onkeyup="btcConvert()">
            <input type="text" id="usd" placeholder="usd" onchange="usdConvert()" onkeyup="usdConvert()">
        </div>

        <script>
            var btc = '<?php echo $json["data"]["amount"]; ?>';

            function btcConvert()
            {
                var amount = document.getElementById("btc").value;
                var btcCalc = amount * btc;
                var btcCalc = btcCalc.toFixed(2);
                document.getElementById("usd").value = btcCalc;
            }

            function usdConvert()
            {
                var usd = document.getElementById("usd");
                var usdCalc = usd / btc;
                var usdCalc = usdCalc.toFixed(8);
                document.getElementById("btc").value = usdCalc;
            }
        </script>
    </body>
</html>