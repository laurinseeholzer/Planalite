<?php ?>
<ul role="list" class="flex flex-1 flex-col gap-y-7 mt-4">
    <li>
        <ul role="list" class="-mx-2 space-y-1">
            <li>
                <a href="admin.php" class="group flex gap-x-3 rounded-md <?= $action === 'dashboard' ? 'bg-gray-50 text-indigo-600 dark:bg-white/5 dark:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' ?> p-2 text-sm/6 font-semibold">
                    <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>
            </li>
        </ul>
    </li>

    <?php if (!empty($singletons)): ?>
    <li>
        <div class="text-xs/6 font-semibold text-gray-400 uppercase tracking-wider">Pages</div>
        <ul role="list" class="-mx-2 mt-2 space-y-1">
            <?php foreach($singletons as $page): ?>
            <li>
                <a href="admin.php?action=edit&target=<?= urlencode($page['file']) ?>" class="group flex gap-x-3 rounded-md <?= ($action === 'edit' && $target === $page['file']) ? 'bg-gray-50 text-indigo-600 dark:bg-white/5 dark:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' ?> p-2 text-sm/6 font-semibold">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.625rem] font-medium text-gray-400"><?= strtoupper(substr($page['name'], 0, 1)) ?></span>
                    <span class="truncate"><?= htmlspecialchars($page['name']) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endif; ?>

    <?php if (!empty($collections)): ?>
    <li>
        <div class="text-xs/6 font-semibold text-gray-400 uppercase tracking-wider">Collections</div>
        <ul role="list" class="-mx-2 mt-2 space-y-1">
            <?php foreach($collections as $col): ?>
            <li>
                <a href="admin.php?action=list&target=<?= urlencode($col['collection']) ?>" class="group flex gap-x-3 rounded-md <?= ($action === 'list' && $target === $col['collection']) ? 'bg-gray-50 text-indigo-600 dark:bg-white/5 dark:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' ?> p-2 text-sm/6 font-semibold">
                    <span class="flex size-6 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.625rem] font-medium text-gray-400"><?= strtoupper(substr($col['name'], 0, 1)) ?></span>
                    <span class="truncate"><?= htmlspecialchars($col['name']) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endif; ?>

    <li>
        <ul role="list" class="-mx-2 space-y-1">
            <li>
                <a href="admin.php?action=settings" class="group flex gap-x-3 rounded-md <?= $action === 'settings' ? 'bg-gray-50 text-indigo-600 dark:bg-white/5 dark:text-white' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-white' ?> p-2 text-sm/6 font-semibold">
                    <svg class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </li>
        </ul>
    </li>

    <li class="-mx-6 mt-auto">
        <a href="/" target="_blank" class="flex items-center gap-x-4 px-6 py-3 text-sm/6 font-semibold text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-white/5">
            <svg class="size-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
            <span aria-hidden="true">View Live Site</span>
        </a>
    </li>
</ul>
