<?php
$secret_chain="gtrrKIUF45JUGGtddszzz";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,site_url()."index.php/cron/get_alertes/".$secret_chain);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);
?>