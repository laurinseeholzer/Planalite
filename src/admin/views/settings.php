<?php ?>
<div>
    <h2 class="text-2xl font-bold dark:text-white">Settings</h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your CMS configuration and updates.</p>

    <div class="mt-8 bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Update</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Check for updates or reinstall core files. This will download the latest version from GitHub and extract it, overwriting system files but preserving your `data` and `template` folders.
        </p>
        <div class="mt-6">
            <a href="install.php" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Update System
            </a>
        </div>
    </div>
</div>
