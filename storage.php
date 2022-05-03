<?php

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

$idKey = "url";
if(!isset($_GET[$idKey])) { http_response_code(400); echo 'Bad Request'; exit(); }
$id = $_GET[$idKey];

if(str_contains($id,"..") || str_contains($id,"./")) { http_response_code(400); echo 'Illegal char in '.$idKey; exit(); }


//send local file
$filename = "/web/storage/".trim($id, "/");
if(!file_exists($filename)) {
    header("Content-Type: application/json");
    echo json_encode(["code" => 404, "message" => "Not found", "details" => "File not found at ".$filename]);
    exit();
}
if(!is_file($filename)){
    header("Content-Type: application/json");
    echo json_encode(["code" => 200, "message" => "Folder", "details" => scandir($filename)]);
    exit();
}
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: Binary");
header("Content-Length:".filesize($filename));
header("Content-Disposition: attachment; filename=\"".basename($filename)."\"");
readfile($filename);
exit();