<?php

// ----- CONST ----- //

$url = "https://api.minecraftbetter.fr/storage";
$idKey = "path";
$rootPath = "/web/storage/";


// ----- PROG ----- //

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

$id = isset($_GET[$idKey]) ? trim($_GET[$idKey], "/\\") : "";
if (str_contains($id, "..") || str_contains($id, "./")) {
    http_response_code(400);
    echo 'Illegal char in ' . $idKey;
    exit();
}


//send local file
$filename = $rootPath . $id;
if (!file_exists($filename)) {
    header("Content-Type: application/json");
    echo json_encode(["code" => 404, "message" => "Not found", "details" => "File not found at " . $filename]);
    exit();
}
if (!is_file($filename)) {
    header("Content-Type: application/json");
    $data = ["code" => 200, "message" => "Folder content", "details" => $filename];

    $parent = trim(dirname($id), "/\\");
    if ($parent == ".") $data["result"]["parent"] = $url;
    else if ($parent != "") $data["result"]["parent"] = $url."?".$idKey."=" . $parent;

    $files = array_filter(scandir($filename), fn($f) => $f != '.' && $f != '..');
    $data["result"]["content"] = array_map(fn($f) => $url."?".$idKey."=" . $id . "/" . $f, $files);

    echo json_encode($data);
    exit();
}
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: Binary");
header("Content-Length:" . filesize($filename));
header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
readfile($filename);
exit();