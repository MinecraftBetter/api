<?php
header('Content-Type: application/json');
$data = [ "documentation" => "https://api.minecraftbetter.fr/swagger/" ];
echo json_encode($data);