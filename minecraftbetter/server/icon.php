<?php

require __DIR__ . '/php-minecraft-query/MinecraftPing.php';
require __DIR__ . '/php-minecraft-query/MinecraftPingException.php';

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

$Ping = null;
try {
    $Ping = new MinecraftPing('minecraftbetter.com', 25565);
    $data = $Ping->Query();
    $strImage = $data["favicon"];
    $strImage = str_replace('data:image/png;base64,', '', $strImage);
    $strImage = str_replace(' ', '+', $strImage);
    header('Content-Type: image/png');
    echo base64_decode($strImage);
} catch (MinecraftPingException $e) {
    http_response_code(502);
    echo $e->getMessage();
} finally {
    if($Ping != null) $Ping->Close();
}