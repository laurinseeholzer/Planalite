<?php
function parseRepeat($contextNode, $repeatData, DOMDocument $dom) {
    $parent = $contextNode->parentNode;
    
    foreach ($repeatData as $itemData) {
        $newNode = $contextNode->cloneNode(true);
        
        $classes = explode(' ', $newNode->getAttribute('class'));
        $filtered = array_diff($classes, ['cms-repeat']);
        
        if (empty($filtered)) {
            $newNode->removeAttribute('class');
        } else {
            $newNode->setAttribute('class', implode(' ', $filtered));
        }

        parseElement($newNode, $itemData, $dom);
        
        $parent->insertBefore($newNode, $contextNode);
    }
    
    $parent->removeChild($contextNode);
}

?>