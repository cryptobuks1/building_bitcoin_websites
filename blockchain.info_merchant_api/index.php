<?php
    /************************************
     * Modified examples from the book  *
     * Building Bitcoin Websites        *
     * by Kyle Honeycutt @coinableS     *
     * ISBN 153494544X                  *
     ************************************/

    require_once('config.inc.php');
    $orderID = uniqid();

    $callback_url = $rootURL."callback.php?invoice=".$orderID."&secret=".$secret;
    $recieve_url = "https://api.blockchain.info/v2/recieve?key=".$api_key."&xpub=".$xpub."&callback=".urlencode($callback_url);

    // cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verify ssl cert of blockchain.info
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return curl contenst as string
    curl_setopt($ch, CURLOPT_URL, $recieve_url); // tell curl where to go
    $ccc = curl_exec($ch);

    echo '<p>$ch:<br><pre>'.var_dump($ch).'</pre></p>';
    echo '<p>$ccc:<br<pre>'.var_dump($ccc).'</pre></p>';

    // returned json response
    $json = json_decode($ccc, true);
    $payTo = $json['address'];

    echo 'Send payment to: '.$payTo;
?>
