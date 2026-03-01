<?php
/**
 * Admin Action: Save Content
 * 
 * Handles the POST request to save raw JSON data back to a singleton or collection.
 * 
 * Invoked by: admin.php
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST payload
    $saveTarget = $_POST['target'] ?? '';
    $isCollection = isset($_POST['is_collection']) && $_POST['is_collection'] == '1';
    $originalSlug = $_POST['original_slug'] ?? '';
    $rawJson = $_POST['raw_json'] ?? '';
    
    // Determine the correct JSON file path in the data/ folder
    $saveTargetName = basename($saveTarget, '.html');
    $saveTargetFile = "data/{$saveTargetName}.json";
    
    // Attempt to parse the user-submitted JSON string
    $decodedNewData = json_decode($rawJson, true);
    
    // Error Handling: If JSON is invalid, redirect back with an error flag
    if ($decodedNewData === null) {
        $redirectUrl = "admin.php?action=edit&target=" . urlencode($saveTarget);
        $redirectUrl .= $isCollection ? "&slug=" . urlencode($originalSlug) : "";
        header("Location: $redirectUrl&error=invalid_json");
        exit;
    }

    if ($isCollection) {
        // Logic for Collections (Arrays of items)
        $existingData = file_exists($saveTargetFile) ? json_decode(file_get_contents($saveTargetFile), true) : [];
        $updated = false;
        
        // Find and replace the specific item in the collection array using its slug
        if (is_array($existingData)) {
            foreach ($existingData as $index => $item) {
                if (isset($item['slug']) && $item['slug'] === $originalSlug) {
                    $existingData[$index] = $decodedNewData;
                    $updated = true;
                    break;
                }
            }
        }
        
        // If it's a new item (slug not found), append it to the array
        if (!$updated) {
            $existingData[] = $decodedNewData;
        }
        
        // Save the updated array back to disk
        file_put_contents($saveTargetFile, json_encode($existingData, JSON_PRETTY_PRINT));
        
        // Redirect back to the collection list view
        header("Location: admin.php?action=list&target=" . urlencode($saveTarget));
        exit;
    } else {
        // Logic for Singletons (Single page object)
        // Overwrite the entire file with the newly submitted object
        file_put_contents($saveTargetFile, json_encode($decodedNewData, JSON_PRETTY_PRINT));
        
        // Redirect back to the dashboard with a success message
        header("Location: admin.php?action=dashboard&msg=saved");
        exit;
    }
}
