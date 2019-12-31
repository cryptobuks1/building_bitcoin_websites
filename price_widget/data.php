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
    $openPrice = $json['open']; 
    
    // Calculate 24h price difference
    if($openPrice < $lastPrice)
    {
        $operator = "+";
        $change = $lastPrice - $openPrice;
        $percent = $change / $openPrice;
        $percent = $percent * 100;
        $percentChange = $operator.number_format($percent, 2);
        $color = 'green';
    }

    if($openPrice > $lastPrice)
    {
        $operator = "-";
        $change = $openPrice - $lastPrice;
        $percent = 1 - ($change / $openPrice);
        $percent = $percent * 100;
        $percentChange = $operator.number_format($percent, 2);
        $color = 'red';
    }

    // Data output to #container
    $table = <<<EOT
            <table width="100%">
                <tr>
                    <td rowspan="4" width="60%" id="lastPrice">$ $lastPrice</td>
                    <td align="right" style="color: $color;">24hr $percentChange %</td>
                </tr>
                <td align="right">H $ $highPrice</td>
                <tr>
                    <td align="right">L $ $lowPrice</td>
                </tr>
                <tr>
                    <td align="right" colspan="2" id="dateTime">API data from <a href="https://bitstamp.net/" target="_blank">Bitstamp.net</a><br>$date</td>
                </tr>
            </table>
            EOT;
    
    echo $table;
?>