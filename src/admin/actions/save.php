<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saveTarget = $_POST['target'] ?? '';
    $isCollection = isset($_POST['is_collection']) && $_POST['is_collection'] == '1';
    $originalSlug = $_POST['original_slug'] ?? '';
    $rawJson = $_POST['raw_json'] ?? '';
    
    $saveTargetName = basename($saveTarget, '.html');
    $saveTargetFile = "data/{$saveTargetName}.json";
    
    $decodedNewData = json_decode($rawJson, true);
    
    if ($decodedNewData === null) {
        $redirectUrl = "admin.php?action=edit&target=" . urlencode($saveTarget);
        $redirectUrl .= $isCollection ? "&slug=" . urlencode($originalSlug) : "";
        header("Location: $redirectUrl&error=invalid_json");
        exit;
    }

    if ($isCollection) {
        $existingData = file_exists($saveTargetFile) ? json_decode(file_get_contents($saveTargetFile), true) : [];
        $updated = false;
        
        if (is_array($existingData)) {
            foreach ($existingData as $index => $item) {
                if (isset($item['slug']) && $item['slug'] === $originalSlug) {
                    $existingData[$index] = $decodedNewData;
                    $updated = true;
                    break;
                }
            }
        }
        
        if (!$updated) {
            $existingData[] = $decodedNewData;
        }
        
        file_put_contents($saveTargetFile, json_encode($existingData, JSON_PRETTY_PRINT));
        
        header("Location: admin.php?action=list&target=" . urlencode($saveTarget));
        exit;
    } else {
        file_put_contents($saveTargetFile, json_encode($decodedNewData, JSON_PRETTY_PRINT));
        
        header("Location: admin.php?action=dashboard&msg=saved");
        exit;
    }
}
