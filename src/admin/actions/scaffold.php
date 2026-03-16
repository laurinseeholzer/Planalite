<?php

require_once __DIR__ . '/../../core/schema.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$target = $_POST['target'] ?? '';
$isCollection = isset($_POST['is_collection']) && $_POST['is_collection'] == '1';
$slug   = $_POST['slug'] ?? '';

$templateFile = null;
$dataFileName = null;

$templateFile = getTemplateFile($target, $isCollection);
$dataFileName = $isCollection ? $target : basename($target, '.html');

$dataFile = "data/{$dataFileName}.json";

if (!$templateFile) {
    $errorRedirect = $isCollection
        ? "admin.php?action=list&target=" . urlencode($target) . "&scaffold_error=no_template"
        : "admin.php?action=edit&target=" . urlencode($target) . "&scaffold_error=no_template";
    header("Location: $errorRedirect");
    exit;
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTMLFile($templateFile);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

$cmsNodes = $xpath->query('//*[@cms][not(ancestor-or-self::head)]');

$schemaMap = [];
foreach ($cmsNodes as $node) {
    $key = $node->getAttribute('cms');

    $ancestor = $node->parentNode;
    $skipNode = false;
    while ($ancestor && $ancestor instanceof DOMElement) {
        if ($ancestor->hasAttribute('cms')) {
            $skipNode = true;
            break;
        }
        $classList = preg_split('/\s+/', $ancestor->getAttribute('class'));
        foreach ($classList as $cls) {
            if (str_starts_with($cls, 'cms-collection-')) {
                $skipNode = true;
                break 2;
            }
        }
        $ancestor = $ancestor->parentNode;
    }
    if ($skipNode) continue;

    $schemaMap[$key] = buildSchema($node);
}

if ($isCollection) {
    $existing = file_exists($dataFile)
        ? (json_decode(file_get_contents($dataFile), true) ?: [])
        : [];

    if (!is_array($existing) || !array_is_list($existing)) {
        $existing = [];
    }

    $existing = array_map(function($item) use ($schemaMap) {
        return is_array($item) ? deepMergeKeepExisting($item, $schemaMap) : $item;
    }, $existing);

    file_put_contents($dataFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    header("Location: admin.php?action=list&target=" . urlencode($target) . "&scaffold_ok=1");
} else {
    $existing = file_exists($dataFile)
        ? (json_decode(file_get_contents($dataFile), true) ?: [])
        : [];

    if (!is_array($existing) || array_is_list($existing)) {
        $existing = [];
    }

    $merged = deepMergeKeepExisting($existing, $schemaMap);

    file_put_contents($dataFile, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    header("Location: admin.php?action=edit&target=" . urlencode($target) . "&scaffold_ok=1");
}
exit;
