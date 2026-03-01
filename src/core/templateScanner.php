<?php

/**
 * Scans a directory of HTML templates and extracts CMS meta tags to determine 
 * if a template is a singleton or a collection blueprint.
 * 
 * @param string $templatesDir The path to the templates directory (e.g., 'templates/')
 * @return array An array containing 'singletons' and 'collections' categorizing the templates
 */
function scanCMSStructure($templatesDir) {
    $structure = [
        'singletons' => [],
        'collections' => []
    ];

    if (!is_dir($templatesDir)) {
        return $structure;
    }

    $files = glob($templatesDir . '/*.html');
    
    foreach ($files as $file) {
        $html = file_get_contents($file);
        
        $type = 'singleton'; // Default type
        $name = basename($file, '.html');
        $collection = '';
        
        // Parse the CMS type meta tag
        if (preg_match('/<meta\s+name="cms-type"\s+content="([^"]+)"\s*\/?>/i', $html, $matches)) {
            $type = strtolower($matches[1]);
        }
        
        // Parse the CMS name meta tag (for display purposes)
        if (preg_match('/<meta\s+name="cms-name"\s+content="([^"]+)"\s*\/?>/i', $html, $matches)) {
            $name = $matches[1];
        }
        
        if ($type === 'collection') {
            // Parse the collection key
            if (preg_match('/<meta\s+name="cms-collection"\s+content="([^"]+)"\s*\/?>/i', $html, $matches)) {
                $collection = $matches[1];
            }
            
            $structure['collections'][] = [
                'file' => basename($file),
                'name' => $name,
                'collection' => $collection
            ];
        } else {
            // Treat as singleton
            $structure['singletons'][] = [
                'file' => basename($file),
                'name' => $name
            ];
        }
    }
    
    return $structure;
}
