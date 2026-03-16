<?php

require_once 'src/core/templateScanner.php';

$templates = scanCMSStructure(__DIR__ . '/templates');
$singletons = $templates['singletons'] ?? [];
$collections = $templates['collections'] ?? [];

$action = $_GET['action'] ?? 'dashboard';
$target = $_GET['target'] ?? '';

$adminContentTemplate = '';
$pageTitle = 'Dashboard';

switch ($action) {
    
    case 'save':
        require_once __DIR__ . '/src/admin/actions/save.php';
        break;

    case 'dashboard':
        $adminContentTemplate = __DIR__ . '/src/admin/views/dashboard.php';
        break;

    case 'create':
        $pageTitle = 'Create New: ' . htmlspecialchars($target);
        $adminContentTemplate = __DIR__ . '/src/admin/views/edit.php';
        break;

    case 'list':
        $pageTitle = 'Collection: ' . htmlspecialchars($target);
        $adminContentTemplate = __DIR__ . '/src/admin/views/list.php';
        break;

    case 'edit':
        $itemDisplay = $target;
        foreach ($singletons as $s) {
            if ($s['file'] === $target) {
                $itemDisplay = $s['name'];
                break;
            }
        }
        $pageTitle = 'Edit: ' . htmlspecialchars($itemDisplay);
        $adminContentTemplate = __DIR__ . '/src/admin/views/edit.php';
        break;

    case 'scaffold':
        require_once __DIR__ . '/src/admin/actions/scaffold.php';
        break;

    default:
        $adminContentTemplate = __DIR__ . '/src/admin/views/dashboard.php';
        break;
}

require_once __DIR__ . '/src/admin/components/layout.php';

?>
