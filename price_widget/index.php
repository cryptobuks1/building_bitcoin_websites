<?php
    // Store API result in database, script to query database.
    // This will prevent exceeding API call rate limits.
    date_default_timezone_set('Australia/Perth');
    $date = date("d/m/Y - h:i:sa");

    $url = "https://www.bitstamp.net/api/ticker/";
    $fileGet = file_get_contents($url);
    $json = json_decode($fileGet, TRUE);

    $lastPrice = number_format($json['last'], 2);
    $highPrice = number_format($json['high'], 2);
    $lowPrice = number_format($json['low'], 2);
    $openPrice = number_format($json['open'], 2); 
    
    if($openPrice < $lastPrice)
    {
        $operator = "+";
        $percent = 1 - ($lastPrice / $openPrice);
        $percentChange = $operator.number_format($percent, 2);
        $color = 'green';
    }
/*
    if($openPrice = $lastPrice)
    {
        $percentChange = 0.0;
        $color = 'grey';
    }
*/
    if($openPrice > $lastPrice)
    {
        $operator = "-";
        $percent = 1 - ($openPrice / $lastPrice);
        $percentChange = $operator.number_format($percent, 2);
        $color = 'red';
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>₿itcoin Widget</title>
        <style>
            #container
            {
                width: 275px;
                height: 90px;
                overflow: hidden;
                background-color: #2f2f2f;
                border: 1px solid #000;
                border-radius: 5px;
                color: #fefdfb;
            }
            #lastPrice
            {
                font-size: 24px;
                font-weight: bold;
            }
            #dateTime
            {
                font-size: 9px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <table width="100%">
                <tr>
                    <td rowspan="4" width="60%" id="lastPrice">$ <?php echo $lastPrice; ?></td>
                    <td align="right" style="color: <?php echo $color; ?>;"><?php echo $percentChange; ?> %</td>
                </tr>
                <td align="right">H $ <?php echo $highPrice; ?></td>
                <tr>
                    <td align="right">L $ <?php echo $lowPrice; ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="2" id="dateTime"><?php echo $date; ?></td>
                </tr>
            </table>
        </div>
    </body>
</html>