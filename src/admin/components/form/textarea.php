<div class="space-y-1 sm:col-span-2">
    <label class="block text-sm/6 font-medium text-gray-900 dark:text-white"><?= $label ?></label>
    <div class="mt-2">
        <textarea name="<?= htmlspecialchars($name) ?>" rows="4" placeholder="<?= htmlspecialchars($placeholder ?? 'Enter content...') ?>" class="block w-full rounded-md bg-gray-50 px-3 py-1.5 text-base text-gray-900 border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm/6 dark:bg-gray-900 dark:text-white dark:border-white/10 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500"><?= htmlspecialchars($value) ?></textarea>
    </div>
</div>
