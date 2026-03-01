<?php
/**
 * Admin Collection List View
 * 
 * Renders a list of all items inside a specific Collection (e.g. all Features).
 * It reads the JSON array for the collection and loops through it to display each item.
 */

// 1. Identify which collection to load based on the URL parameter (e.g. ?target=features)
$collectionName = htmlspecialchars($target);
$dataFile = "data/{$target}.json";
$items = [];

// 2. Fetch the collection data from the JSON file
if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);
    
    // Ensure the data is actually a sequential array (a list of items) before assigning it
    if (is_array($data) && array_is_list($data)) {
        $items = $data;
    }
}
?>

<div>
    <!-- Header Block -->
    <div>
        <nav aria-label="Back" class="sm:hidden">
            <a href="admin.php" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="mr-1 -ml-1 size-5 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>
                Dashboard
            </a>
        </nav>
        <nav aria-label="Breadcrumb" class="hidden sm:flex mb-4">
            <ol role="list" class="flex items-center space-x-4">
                <li>
                    <div class="flex">
                        <a href="admin.php" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Admin</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="size-5 shrink-0 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-700 dark:text-gray-200" aria-current="page"><?= htmlspecialchars(ucfirst($target)) ?> Collection</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between mb-8 border-b border-gray-200 pb-5 dark:border-white/10">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight dark:text-white">
                <?= htmlspecialchars(ucfirst($target)) ?>
            </h2>
        </div>
        <div class="mt-4 flex shrink-0 md:mt-0 md:ml-4">
            <a href="admin.php?action=create&target=<?= urlencode($target) ?>" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:hover:bg-indigo-400 dark:focus-visible:outline-indigo-500">
                + Create New
            </a>
        </div>
    </div>
</div>

<?php if (empty($items)): ?>
    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
        <svg class="mx-auto size-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No items found</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new entry in this collection.</p>
        <div class="mt-6">
            <a href="admin.php?action=create&target=<?= urlencode($target) ?>" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg class="-ml-0.5 mr-1.5 size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                New Item
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg dark:bg-gray-800 dark:ring-white/10">
        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
            <!-- 3. Loop through each item in the collection array and render a row -->
            <?php foreach ($items as $item): ?>
            <li class="relative flex justify-between gap-x-6 px-4 py-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 sm:px-6 transition-colors duration-200">
                <div class="flex min-w-0 gap-x-4">
                    <div class="min-w-0 flex-auto">
                        <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                            <!-- Link to the Edit View for this specific item, passing both the target collection and item slug -->
                            <a href="admin.php?action=edit&target=<?= urlencode($target) ?>&slug=<?= urlencode($item['slug'] ?? '') ?>">
                                <span class="absolute inset-x-0 -top-px bottom-0"></span>
                                <!-- Fallback chain to find a displayable title for the item -->
                                <?= htmlspecialchars($item['title'] ?? $item['name'] ?? $item['slug'] ?? 'Untitled Item') ?>
                            </a>
                        </p>
                        <p class="mt-1 flex text-xs leading-5 text-gray-500 dark:text-gray-400">
                            Slug: <?= htmlspecialchars($item['slug'] ?? 'none') ?>
                        </p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-x-4">
                    <div class="hidden sm:flex sm:flex-col sm:items-end">
                        <p class="text-sm leading-6 text-gray-900 dark:text-gray-300">Published</p>
                    </div>
                    <svg class="size-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
