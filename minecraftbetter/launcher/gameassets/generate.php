<?php
/*
 * How to use this:
 * 1. Create a 0.json file in $cachePath, in this file, specify all the directories and files that needs to be deleted from the precedent server.
 * 2. Launch this program, it will generate the file list
 * 3. If an update is needed, replace the content of the old file by the files that needs to be deleted (like in 0.) then launch this program
 */

include $_SERVER['DOCUMENT_ROOT'] . "/config.php";
assert(isset($STORAGE_PATH) && isset($API_URL));
header("Content-Type: application/json");

// ----- CONST ----- //

$folder = "gameassets"; // No trailing slash
$noOverride = ["config/", "./options.txt"]; // List of relative paths
$cache_life = 7 * 24 * 3600; // 7j (1h = 3600s)


// ----- PROG ----- //

$path = $STORAGE_PATH . $folder . "/data/";

$cachePath = $STORAGE_PATH . $folder . "/.cache/";
$cache = $cachePath . time() . ".json";

if (isset($_GET["update"])) {
    $result = [];
    print("<pre>");
    print ("cd \"" . $path . "\" && git pull");
    exec("cd \"" . $path . "\" && git pull", $result);
    foreach ($result as $line) {
        print($line . "\n");
    }
    print("</pre>");
}

if(!file_exists($path)) {
    echo json_encode([
        "message" => "error, the directory doesn't exists"
    ]);
    exit;
}
$o_dir = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
$o_iter = new RecursiveIteratorIterator($o_dir);

$results = [];
foreach ($o_iter as $o_name) {
    if (!$o_name->isFile()) continue;
    $fullPath = $o_name->getPathname();
    if (preg_match('/(^|\/)\.\w+/i', $fullPath)) continue; // Hidden dir

    $localFilePath = substr($fullPath, strlen($STORAGE_PATH));
    $filePath = substr($localFilePath, strlen($folder . "/data/"));

    $results[$filePath] = [
        "hash" => sha1_file($fullPath),
        "size" => $o_name->getSize(),
        "url" => $API_URL . "/storage?path=" . $localFilePath,
        "path" => $filePath,
        "override" => override($filePath)
    ];
}

$finalJson = json_encode($results);
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