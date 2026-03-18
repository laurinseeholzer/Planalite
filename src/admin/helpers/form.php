<?php

function renderComponent(string $path, array $vars): void {
    extract($vars);
    include $path;
}

function renderFormSchema(array $schema, mixed $data, string $prefix = 'data', bool $isInCard = false): void {
    foreach ($schema as $key => $subSchema) {
        $namePrefix = $prefix . '[' . $key . ']';
        $currentValue = $data[$key] ?? null;

        if ($key === 'slug') continue;

        $isTopLevel = ($prefix === 'data');
        $title = htmlspecialchars(ucfirst(str_replace('_', ' ', $key)));

        ob_start();

        if (is_array($subSchema) && array_is_list($subSchema)) {
            renderRepeatableField($key, $subSchema[0] ?? [], is_array($currentValue) ? $currentValue : [], $namePrefix, $title);
        } else if (is_array($subSchema)) {
            renderDictionaryField($subSchema, $currentValue, $namePrefix, $isTopLevel, $isInCard);
        }

        $contentHtml = ob_get_clean();

        // Always wrap items in a card
        if (!empty(trim($contentHtml))) {
            renderComponent(__DIR__ . '/../components/form/card.php', ['title' => $title, 'content' => $contentHtml]);
        }
    }
}

function renderRepeatableField(string $key, array $itemSchema, array $items, string $namePrefix, string $title): void {
    ob_start();
    foreach ($items as $index => $itemData) {
        $itemTitle = $title;
        
        ob_start();
        renderFormSchema($itemSchema, $itemData, $namePrefix . '[' . $index . ']', true);
        $itemContent = ob_get_clean();
        
        ob_start();
        renderComponent(__DIR__ . '/../components/form/delete_button.php', []);
        $deleteButton = ob_get_clean();

        echo '<div class="cms-repeatable-item">';
        renderComponent(__DIR__ . '/../components/form/card.php', ['title' => $itemTitle, 'header_actions' => $deleteButton, 'content' => $itemContent]);
        echo '</div>';
    }
    $itemsHtml = ob_get_clean();

    ob_start();
    renderFormSchema($itemSchema, [], $namePrefix . '[__INDEX__]', true);
    $templateForm = ob_get_clean();
    
    ob_start();
    renderComponent(__DIR__ . '/../components/form/delete_button.php', []);
    $templateDeleteButton = ob_get_clean();

    ob_start();
    renderComponent(__DIR__ . '/../components/form/card.php', ['title' => $title . ' (New)', 'header_actions' => $templateDeleteButton, 'content' => $templateForm]);
    $templateFormHtml = ob_get_clean();

    renderComponent(__DIR__ . '/../components/form/repeatable_list.php', [
        'key' => $key,
        'namePrefix' => $namePrefix,
        'itemsHtml' => $itemsHtml,
        'templateFormHtml' => $templateFormHtml
    ]);
}

function renderDictionaryField(array $subSchema, mixed $currentValue, string $namePrefix, bool $isTopLevel, bool $isInCard): void {
    $knownKeys = ['inner', 'href', 'src'];
    $hasKnownKeys = false;
    foreach ($knownKeys as $k) { if (isset($subSchema[$k])) $hasKnownKeys = true; }

    if ($hasKnownKeys) {
        $gridClass = ($isTopLevel || $isInCard) ? 'grid grid-cols-1 gap-4 sm:grid-cols-2' : '';
        
        ob_start();
        foreach ($knownKeys as $k) {
            if (isset($subSchema[$k])) {
                $val = $currentValue[$k] ?? '';
                $labelName = ($k === 'href') ? 'Link URL' : (($k === 'src') ? 'Image URL' : 'Content');
                $inputVars = ['name' => $namePrefix . '[' . $k . ']', 'value' => $val, 'label' => $labelName, 'placeholder' => ($k === 'href') ? 'https://...' : 'Enter content...'];

                if ($k === 'inner') {
                    renderComponent(__DIR__ . '/../components/form/textarea.php', $inputVars);
                } else {
                    renderComponent(__DIR__ . '/../components/form/input.php', $inputVars);
                }
            }
        }
        $gridContent = ob_get_clean();

        renderComponent(__DIR__ . '/../components/form/dictionary_grid.php', [
            'gridClass' => $gridClass,
            'content' => $gridContent
        ]);
    }

    $nestedSchema = array_diff_key($subSchema, array_flip($knownKeys));
    if (!empty($nestedSchema)) {
        ob_start();
        renderFormSchema($nestedSchema, $currentValue ?: [], $namePrefix);
        $nestedContent = ob_get_clean();

        renderComponent(__DIR__ . '/../components/form/nested_wrapper.php', [
            'content' => $nestedContent
        ]);
    }
}
