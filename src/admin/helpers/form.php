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

        if ($isTopLevel) {
            renderComponent(__DIR__ . '/../components/form/card.php', ['title' => $title, 'content' => $contentHtml]);
        } else {
            echo '<div class="mb-5"><label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">' . $title . '</label>' . $contentHtml . '</div>';
        }
    }
}

function renderRepeatableField(string $key, array $itemSchema, array $items, string $namePrefix, string $title): void {
    echo '<div class="cms-repeatable-list space-y-5" data-key="' . htmlspecialchars($key) . '" data-prefix="' . htmlspecialchars($namePrefix) . '">';
    echo '  <div class="cms-items-container space-y-5">';
    
    foreach ($items as $index => $itemData) {
        $itemTitle = $title;
        
        ob_start();
        renderFormSchema($itemSchema, $itemData, $namePrefix . '[' . $index . ']', true);
        $itemContent = ob_get_clean();
        
        $cardTitle = '<div class="flex items-center justify-between w-full"><span>' . $itemTitle . '</span><button type="button" class="cms-remove-item inline-flex items-center rounded-md bg-indigo-600 p-1.5 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button></div>';

        echo '<div class="cms-repeatable-item">';
        renderComponent(__DIR__ . '/../components/form/card.php', ['title' => $cardTitle, 'content' => $itemContent]);
        echo '</div>';
    }
    echo '  </div>';
    
    echo '  <div class="mt-4">';
    echo '    <button type="button" class="cms-add-item inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">';
    echo '      <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>';
    echo '      Add New ' . htmlspecialchars(ucfirst($key));
    echo '    </button>';
    echo '  </div>';
    
    ob_start();
    renderFormSchema($itemSchema, [], $namePrefix . '[__INDEX__]', true);
    $templateForm = ob_get_clean();
    $templateTitle = '<div class="flex items-center justify-between w-full"><span>' . $title . ' (New)</span><button type="button" class="cms-remove-item inline-flex items-center rounded-md bg-indigo-600 p-1.5 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button></div>';

    echo '  <template class="cms-item-template">';
    echo '    <div class="cms-repeatable-item">';
    renderComponent(__DIR__ . '/../components/form/card.php', ['title' => $templateTitle, 'content' => $templateForm]);
    echo '    </div>';
    echo '  </template>';
    echo '</div>';
}

function renderDictionaryField(array $subSchema, mixed $currentValue, string $namePrefix, bool $isTopLevel, bool $isInCard): void {
    $knownKeys = ['inner', 'href', 'src'];
    $hasKnownKeys = false;
    foreach ($knownKeys as $k) { if (isset($subSchema[$k])) $hasKnownKeys = true; }

    if ($hasKnownKeys) {
        $gridClass = ($isTopLevel || $isInCard) ? 'grid grid-cols-1 gap-4 sm:grid-cols-2' : '';
        echo '<div class="' . $gridClass . '">';
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
        echo '</div>';
    }

    $nestedSchema = array_diff_key($subSchema, array_flip($knownKeys));
    if (!empty($nestedSchema)) {
        echo '<div class="mt-4 space-y-4">';
        renderFormSchema($nestedSchema, $currentValue ?: [], $namePrefix);
        echo '</div>';
    }
}
