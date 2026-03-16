<?php

function buildSchema(DOMElement $node): mixed {
    $classes = $node->hasAttribute('class')
        ? explode(' ', $node->getAttribute('class'))
        : [];

    $isRepeat = in_array('cms-repeat', $classes);
    $isInner  = in_array('cms-inner', $classes);
    $isHref   = in_array('cms-href', $classes);
    $isSrc    = in_array('cms-src', $classes);

    if ($isRepeat) {
        $itemSchema = buildChildSchema($node);

        if ($isInner && !isset($itemSchema['inner'])) $itemSchema['inner'] = '';
        if ($isHref  && !isset($itemSchema['href']))  $itemSchema['href']  = '';
        if ($isSrc   && !isset($itemSchema['src']))   $itemSchema['src']   = '';

        if (empty($itemSchema)) $itemSchema = ['inner' => ''];

        return [$itemSchema];
    }

    $schema = [];
    if ($isInner) {
        $schema['inner'] = '';
    }
    if ($isHref) {
        $schema['href'] = '';
    }
    if ($isSrc) {
        $schema['src'] = '';
    }

    $childSchema = buildChildSchema($node);
    $schema = array_merge($schema, $childSchema);

    if (empty($schema) && !$isHref && !$isSrc) {
        return ['inner' => ''];
    }

    return $schema;
}

function buildChildSchema(DOMElement $node): array {
    $schema = [];
    foreach ($node->childNodes as $child) {
        if (!($child instanceof DOMElement)) continue;

        if ($child->hasAttribute('cms')) {
            $key = $child->getAttribute('cms');
            $schema[$key] = buildSchema($child);
        } else {
            $deeper = buildChildSchema($child);
            $schema = array_merge($schema, $deeper);
        }
    }
    return $schema;
}

function deepMergeKeepExisting(array $existing, array $new): array {
    $result = [];

    foreach ($new as $key => $schemaValue) {
        if (!array_key_exists($key, $existing)) {
            $result[$key] = $schemaValue;
        } elseif (is_array($schemaValue) && is_array($existing[$key])) {
            $existingIsList = array_is_list($existing[$key]);
            $newIsList      = array_is_list($schemaValue);

            if ($existingIsList && $newIsList && !empty($schemaValue)) {
                $itemSchema = $schemaValue[0];
                $mapped = array_map(function($item) use ($itemSchema) {
                    return is_array($item) && (!array_is_list($item) || empty($item))
                        ? deepMergeKeepExisting($item, $itemSchema)
                        : $item;
                }, $existing[$key]);
                $result[$key] = empty($mapped) ? [$itemSchema] : $mapped;
            } elseif (!$existingIsList && !$newIsList) {
                $result[$key] = deepMergeKeepExisting($existing[$key], $schemaValue);
            } else {
                $result[$key] = $existing[$key];
            }
        } else {
            $result[$key] = $existing[$key];
        }
    }

    if (array_key_exists('slug', $existing) && !array_key_exists('slug', $result)) {
        $result['slug'] = $existing['slug'];
    }

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$target = $_POST['target'] ?? '';
$isCollection = isset($_POST['is_collection']) && $_POST['is_collection'] == '1';
$slug   = $_POST['slug'] ?? '';

$templateFile = null;
$dataFileName = null;

if ($isCollection) {
    foreach (glob('templates/*.html') ?: [] as $candidate) {
        $html = file_get_contents($candidate);
        if (preg_match('/<meta\s+name="cms-collection"\s+content="([^"]+)"/i', $html, $m) && $m[1] === $target) {
            $templateFile = $candidate;
            break;
        }
    }
    $dataFileName = $target;
} else {
    $base = 'templates/' . basename($target);
    $templateFile = file_exists($base) ? $base : (file_exists($base . '.html') ? $base . '.html' : null);
    $dataFileName = basename($target, '.html');
}

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
