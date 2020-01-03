<?php
    /************************************
     * Modified examples from the book  *
     * Building Bitcoin Websites        *
     * by Kyle Honeycutt @coinableS     *
     * ISBN 153494544X                  *
     ************************************/
    
    require_once('./config.inc.php');
    require_once('./easybitcoin.php');

    // JSON RPC
    // Bitcoin(username, password, ipaddress(optional), port(optional))
    $bitcoin = new Bitcoin($rpcUsername, $rpcPassword, $nodeIP);

    $tip = $bitcoin->getblockcount();
    $blockhash = $bitcoin->getblockhash($tip);
    $freshtxinfo = $bitcoin->getmempoolinfo();
    $smartfee = $bitcoin->estimatesmartfee(1);

    // BTCUSD Price
    $url = "https://www.bitstamp.net/api/ticker/";
    $fileGet = file_get_contents($url);
    $json = json_decode($fileGet, TRUE);
    $lastPrice = number_format($json['last'], 2);

    // taken from php.net
    function formatBytes($bytes, $precision = 2) 
    { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        $bytes /= (1 << (10 * $pow)); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 
?>

<!DOCTYPE html>
<html>
    <head></head>
    <body>
        <div>
            <?php if(!$bitcoin->error){ $feerate = number_format($smartfee['feerate'],8); ?>
            <p>Transaction waiting since block # <?php print_r($tip); ?> : <?php print_r($freshtxinfo['size']); ?> txs (<?php print_r(formatBytes($freshtxinfo['usage'])); ?>)</p>
            <p>Next block fee estimate: <?php print_r($feerate); ?> sats/byte | $<?php print_r($feerate* $lastPrice); ?> /byte</p>
            <?php }else{ ?>
            <p>Error: <?php print_r($bitcoin->error); ?></p>
            <p>Status: <?php print_r($bitcoin->status); ?></p>
            <?php } ?>
        </div>
    </body>
</html>