<?php
require_once 'src/core/helper.php';
require_once 'src/core/elementParser.php';
require_once 'src/core/repeatParser.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts = explode('/', $path);
$collectionKey = $parts[0] ?: 'index';
$slug = $parts[1] ?? null;

$dataFile = "data/$collectionKey.json";
$pageData = null;
$template = $collectionKey;

if (file_exists($dataFile)) {
    $entry = json_decode(file_get_contents($dataFile), true);

    if (is_array($entry) && array_is_list($entry)) {
        if ($slug) {
            foreach ($entry as $item) {
                if (isset($item['slug']) && $item['slug'] === $slug) {
                    $pageData = $item;
                    $pageData['_collection'] = $collectionKey;
                    break;
                }
            }
        } else {
             $pageData = ['_collection' => $collectionKey, 'items' => $entry];
        }
    } else {
        $pageData = $entry;
    }
}

if (!$pageData) {
    http_response_code(404);
    die("404 - Page or Entry Not Found");
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$templateFile = "templates/$template.html";

if (!file_exists($templateFile)) {
    die("Template error: $templateFile not found.");
}

$dom->loadHTMLFile($templateFile, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
libxml_clear_errors();

parseElement($dom->documentElement, $pageData, $dom);
echo $dom->saveHTML();

?>