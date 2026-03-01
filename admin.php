<?php
/**
 * Main Admin Controller (POC)
 * 
 * This file acts as the single entry point for the Admin Panel.
 * It uses the 'action' query parameter to determine which view to load
 * and handles form submissions for saving content.
 */

// 1. Include the template scanner to discover available pages and collections
require_once 'src/core/templateScanner.php';

// 2. Scan the 'templates' directory to build the CMS structure
$templates = scanCMSStructure(__DIR__ . '/templates');
$singletons = $templates['singletons'] ?? [];
$collections = $templates['collections'] ?? [];

// 3. Determine the current routing action and target from the URL
$action = $_GET['action'] ?? 'dashboard';
$target = $_GET['target'] ?? '';

// Variables that will be passed down to the layout and view templates
$adminContentTemplate = '';
$pageTitle = 'Dashboard';

// 4. Handle the requested action
switch ($action) {
    
    // --- POST ACTION: SAVE CONTENT ---
    case 'save':
        require_once __DIR__ . '/src/admin/actions/save.php';
        break;

    // --- GET ACTION: DASHBOARD ---
    case 'dashboard':
        $adminContentTemplate = __DIR__ . '/src/admin/views/dashboard.php';
        break;

    // --- GET ACTION: CREATE NEW ITEM ---
    case 'create':
        $pageTitle = 'Create New: ' . htmlspecialchars($target);
        $adminContentTemplate = __DIR__ . '/src/admin/views/edit.php';
        break;

    // --- GET ACTION: LIST COLLECTION ITEMS ---
    case 'list':
        $pageTitle = 'Collection: ' . htmlspecialchars($target);
        $adminContentTemplate = __DIR__ . '/src/admin/views/list.php';
        break;

    // --- GET ACTION: EDIT ITEM / PAGE ---
    case 'edit':
        $itemDisplay = $target;
        // Search through singletons to find a user-friendly name for the header title
        foreach ($singletons as $s) {
            if ($s['file'] === $target) {
                $itemDisplay = $s['name'];
                break;
            }
        }
        $pageTitle = 'Edit: ' . htmlspecialchars($itemDisplay);
        $adminContentTemplate = __DIR__ . '/src/admin/views/edit.php';
        break;

    // --- DEFAULT FALLBACK ---
    default:
        $adminContentTemplate = __DIR__ . '/src/admin/views/dashboard.php';
        break;
}

// 5. Load the master visual layout which will safely include $adminContentTemplate inside <main>
require_once __DIR__ . '/src/admin/components/layout.php';

?>
