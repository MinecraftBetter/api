<?php

require __DIR__ . '/php-minecraft-query/MinecraftPing.php';
require __DIR__ . '/php-minecraft-query/MinecraftPingException.php';

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

$Ping = new MinecraftPing('minecraftbetter.com', 25565);
try {
    $data = $Ping->Query();
    $strImage = $data["favicon"];
    $strImage = str_replace('data:image/png;base64,', '', $strImage);
    $strImage = str_replace(' ', '+', $strImage);
    header('Content-Type: image/png');
    echo base64_decode($strImage);
} catch (MinecraftPingException $e) {
    echo $e->getMessage();
} finally {
    $Ping->Close();
}