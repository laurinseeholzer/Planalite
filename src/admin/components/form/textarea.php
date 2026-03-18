<div class="space-y-1 sm:col-span-2">
    <label class="block text-sm/6 font-medium text-gray-900 dark:text-white"><?= $label ?></label>
    <div class="mt-2">
        <textarea name="<?= htmlspecialchars($name) ?>" rows="4" placeholder="<?= htmlspecialchars($placeholder ?? 'Enter content...') ?>" class="block w-full rounded-md bg-gray-100 px-3.5 py-2 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"><?= htmlspecialchars($value) ?></textarea>
    </div>
</div>
