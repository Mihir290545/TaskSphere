<?php
$ch = curl_init('http://127.0.0.1:8080/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
if ($res === false) {
    echo "CURL ERROR: " . curl_error($ch) . "\n";
} else {
    echo "HTTP SUCCESS! Response length: " . strlen($res) . "\n";
    echo substr($res, 0, 200) . "\n";
}
curl_close($ch);
