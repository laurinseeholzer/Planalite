<div class="mb-3 overflow-hidden bg-white ring-1 ring-gray-200 sm:rounded-lg dark:bg-[#0f1523] dark:ring-white/10">
  <div class="flex items-center justify-between px-3 py-2 bg-gray-100/50 dark:bg-white/5 border-b border-gray-200 dark:border-gray-700/50">
    <h2 class="text-xs font-bold uppercase tracking-tight text-gray-600 dark:text-gray-400">
      <?= $title ?>
    </h2>
    <?php if (isset($header_actions)): ?>
      <div class="flex items-center gap-2">
        <?= $header_actions ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="p-3 sm:p-4">
    <?= $content ?>
  </div>
</div>