<?php

use CodeIgniter\Pager\PagerRenderer;

/** @var PagerRenderer $pager */
$pager->setSurroundCount(2);
?>
<nav aria-label="Equipment catalog pages">
    <ul class="pagination catalog-pagination mb-0">
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <?php if ($pager->hasPrevious()): ?>
                <a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Previous page"><i class="bi bi-chevron-left" aria-hidden="true"></i></a>
            <?php else: ?>
                <span class="page-link" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
            <?php endif; ?>
        </li>
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>" <?= $link['active'] ? 'aria-current="page"' : '' ?>><?= esc((string) $link['title']) ?></a>
            </li>
        <?php endforeach; ?>
        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <?php if ($pager->hasNext()): ?>
                <a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Next page"><i class="bi bi-chevron-right" aria-hidden="true"></i></a>
            <?php else: ?>
                <span class="page-link" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
            <?php endif; ?>
        </li>
    </ul>
</nav>