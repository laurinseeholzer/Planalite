<div class="space-y-1">
    <label class="block text-sm/6 font-medium text-gray-900 dark:text-white"><?= $label ?></label>
    <div class="mt-2">
        <input type="text" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>" placeholder="<?= htmlspecialchars($placeholder ?? 'https://...') ?>" class="block w-full rounded-md bg-gray-50 px-3 py-1.5 text-base text-gray-900 border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm/6 dark:bg-gray-900 dark:text-white dark:border-white/10 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500" />
    </div>
</div>
