<?php

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
        
        $type = 'singleton';
        $name = basename($file, '.html');
        $collection = '';
        
        if (preg_match('/<meta\s+name="cms-type"\s+content="([^"]+)"\s*\/?>/i', $html, $matches)) {
            $type = strtolower($matches[1]);
        }
        
        if (preg_match('/<meta\s+name="cms-name"\s+content="([^"]+)"\s*\/?>/i', $html, $matches)) {
            $name = $matches[1];
        }
        
        if ($type === 'collection') {
            if (preg_match('/<meta\s+name="cms-collection"\s+content="([^"]+)"\s*\/?>/i', $html, $matches)) {
                $collection = $matches[1];
            }
            
            $structure['collections'][] = [
                'file' => basename($file),
                'name' => $name,
                'collection' => $collection
            ];
        } else {
            $structure['singletons'][] = [
                'file' => basename($file),
                'name' => $name
            ];
        }
    }
    
    return $structure;
}
