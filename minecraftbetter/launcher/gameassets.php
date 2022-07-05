<?php
include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL));
header("Content-Type: application/json");

// ----- CONST ----- //

$folder = "gameassets"; // No trailing slash
$noOverride = ["config/", "./options.txt"]; // List of relative paths
$cache_life = 7 * 24 * 3600; // 7j (1h = 3600s)


// ----- PROG ----- //

$path = $STORAGE_PATH . $folder;

$cache = $path. "/.cache.json";
$filemtime = filemtime($cache);

if (isset($_GET["update"])){
    $result = array();
    print("<pre>");
    print ("cd \"".$path."\" && git pull");
    exec("cd \"".$path."\" && git pull", $result);
    foreach ($result as $line) {
        print($line . "\n");
    }
    print("</pre>");
}

if (!isset($_GET["force"]) && !isset($_GET["update"]) && $filemtime && (time() - $filemtime <= $cache_life)) { echo file_get_contents($cache); return; }

$o_dir = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
$o_iter = new RecursiveIteratorIterator($o_dir);

$results = [];
foreach ($o_iter as $o_name) {
    if (!$o_name->isFile()) continue;
    $fullPath = $o_name->getPathname();
    if (preg_match('/(^|\/)\.\w+/i', $fullPath)) continue; // Hidden dir

    $localFilePath = substr($fullPath, strlen($STORAGE_PATH));
    $pathFolders = preg_split("/\//", $localFilePath);
    if (count($pathFolders) > 2) {
        $rootFolder = $pathFolders[1];
        $filePath = substr($localFilePath, strlen($folder . "/" . $rootFolder . "/"));
    }
    else {
        $rootFolder = ".";
        $filePath = substr($localFilePath, strlen($folder . "/"));
    }

    $results[$rootFolder][$filePath] = [
        "hash" => sha1_file($fullPath),
        "size" => $o_name->getSize(),
        "url" => $API_URL . "/storage?path=" . $localFilePath,
        "path" => $filePath,
        "override" => override($rootFolder."/".$filePath)
    ];
}

$finalJson = json_encode([
    "code" => 200,
    "date" => date("Y-m-d H:i:s", $filemtime ?? time()),
    "message" => "Success",
    "details" => "Custom assets for Minecraft Better",
    "results" => $results
]);
file_put_contents($cache, $finalJson);
echo $finalJson;

function override($fPath): bool
{
    global $noOverride;
    foreach ($noOverride as $item)
        if (strpos($fPath, $item) !== false)
            return false;
    return true;
}