<?php
    /************************************
     * Modified examples from the book  *
     * Building Bitcoin Websites        *
     * by Kyle Honeycutt @coinableS     *
     * ISBN 153494544X                  *
     ************************************/

    $api_key = "";
    $xpub = "xpub6BhRh2ku39Z37WgXirSh6iRXAgXa94wWPLbD2pPfFThZF8VqWvSP8GB6QUeyTrfSV79hfm2UzfrV61XUM9HPsAaeX9EmKJbGmhFibBZkyKa"; // Enjin BTC wallet
    $secret = hash("tiger192,4", "one, two, three o'clock, four o'clock rock!");
    $rootURL = "http://localhost/building_bitcoin_websites/blockchain.info_merchant_api/";
    $orderID = uniqid();

    $callback_url = $rootURL."callback.php?invoice=".$orderID."&secret=".$secret;
    $recieve_url = "https://api.blockchain.info/v2/recieve?key=".$api_key."&xpub=".$xpub."&callback=".urlencode($callback_url);

    // cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // verify ssl cert of blockchain.info
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return curl contenst as string
    curl_setopt($ch, CURLOPT_URL, $recieve_url); // tell curl where to go
    $ccc = curl_exec($ch);

    // returned json response
    $json = json_decode($ccc, true);
    $payTo = $json['address'];

    echo 'Send payment to: '.$payTo;
?>
