<?php
    function toSats($btc)
    {
        $satoshis = $btc * 100000000;
        
        return $satoshis;
    }

    function toBTC($satoshis)
    {
        $btc = $satoshis / 100000000;

        return $btc;
    }

?>