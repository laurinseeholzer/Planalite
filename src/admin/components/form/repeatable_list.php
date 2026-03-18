<div class="cms-repeatable-list space-y-5" data-key="<?= htmlspecialchars($key) ?>" data-prefix="<?= htmlspecialchars($namePrefix) ?>">
  <div class="cms-items-container space-y-5">
    <?= $itemsHtml ?>
  </div>

  <div class="mt-4">
    <button type="button" class="cms-add-item inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
      <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
      Add New <?= htmlspecialchars(ucfirst($key)) ?>
    </button>
  </div>

  <template class="cms-item-template">
    <div class="cms-repeatable-item">
      <?= $templateFormHtml ?>
    </div>
  </template>
</div>
