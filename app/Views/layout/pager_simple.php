<?php $pager->setSurroundCount(0) ?>
<a href="<?= $pager->hasPrevious() ? $pager->getPrevious() : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pager->hasPrevious() ? '' : 'disabled' ?>">Sebelumnya</a>
<a href="<?= $pager->hasNext() ? $pager->getNext() : '#' ?>" class="btn btn-sm btn-outline-secondary <?= $pager->hasNext() ? '' : 'disabled' ?>">Berikutnya</a>
