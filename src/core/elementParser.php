<?php

function parseElement($contextNode, $data, DOMDocument $dom) {
    if (!$contextNode instanceof DOMElement) return;

    $collectionName = extractClassValue($contextNode, 'cms-collection-');
    if ($collectionName) {
        $collectionFile = "data/$collectionName.json";
        
        if (file_exists($collectionFile)) {
            $data = json_decode(file_get_contents($collectionFile), true) ?: [];
            
            foreach ($data as &$item) {
                if (is_array($item)) {
                    $item['_collection'] = $collectionName;
                }
            }

            $limit = extractClassValue($contextNode, 'cms-limit-');
            if ($limit !== null) {
                $limitInt = (int) $limit;
                $data = array_slice($data, 0, $limitInt);
            }
        } else {
            $data = [];
        }
        
        removeClassPrefix($contextNode, ['cms-collection-', 'cms-limit-']);
        
        if (hasClass($contextNode, 'cms-repeat')) {
            parseRepeat($contextNode, $data, $dom);
            return;
        }
    }

    if (hasClass($contextNode, 'cms-item-link') && isset($data['_collection']) && isset($data['slug'])) {
        $url = "/" . $data['_collection'] . "/" . $data['slug'];
        $contextNode->setAttribute('href', $url);
        removeClassPrefix($contextNode, ['cms-item-link']);
    }

    $cmsKey = null;
    if ($contextNode->hasAttribute('cms')) {
        $cmsKey = $contextNode->getAttribute('cms');
    }
    $nodeData = ($cmsKey && isset($data[$cmsKey])) ? $data[$cmsKey] : $data;

    if (is_array($nodeData)) {
        if (hasClass($contextNode, 'cms-inner')) {
            $contextNode->nodeValue = ''; 
            $htmlContent = $nodeData['inner'] ?? '';
            if ($htmlContent !== '') {
                $tempDom = new DOMDocument();
                @$tempDom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $htmlContent . '</body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                $bodyTags = $tempDom->getElementsByTagName('body');
                if ($bodyTags->length > 0) {
                    $bodyNode = $bodyTags->item(0);
                    $children = [];
                    foreach ($bodyNode->childNodes as $child) {
                        $children[] = $child;
                    }
                    foreach ($children as $child) {
                        $importedNode = $dom->importNode($child, true);
                        $contextNode->appendChild($importedNode);
                    }
                }
            }
        }
        if (hasClass($contextNode, 'cms-href') && isset($nodeData['href'])) {
            $contextNode->setAttribute('href', $nodeData['href']);
        }
        if (hasClass($contextNode, 'cms-src') && isset($nodeData['src'])) {
            $contextNode->setAttribute('src', $nodeData['src']);
        }
    }

    $children = iterator_to_array($contextNode->childNodes);

    foreach ($children as $childNode) {
        if ($childNode->nodeType !== XML_ELEMENT_NODE) continue;

        if ($childNode->hasAttribute('cms')) {
            $childCmsKey = $childNode->getAttribute('cms');

            if (isset($nodeData[$childCmsKey]) && hasClass($childNode, 'cms-repeat')) {
                parseRepeat($childNode, $nodeData[$childCmsKey], $dom);
            } else {
                parseElement($childNode, $nodeData, $dom);
            }
        } else {
            parseElement($childNode, $nodeData, $dom);
        }
    }

    $contextNode->removeAttribute('cms');
}

?>