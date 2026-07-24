<?php
/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(0);
?>

<div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3">
    <span class="text-muted small">Halaman <?= $pager->getCurrentPageNumber() ?> dari <?= $pager->getPageCount() ?></span>
    <div class="btn-group">
        <a href="<?= $pager->hasPreviousPage() ? $pager->getPreviousPage() : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pager->hasPreviousPage() ? '' : 'disabled' ?>">Sebelumnya</a>
        <a href="<?= $pager->hasNextPage() ? $pager->getNextPage() : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pager->hasNextPage() ? '' : 'disabled' ?>">Berikutnya</a>
    </div>
</div>
