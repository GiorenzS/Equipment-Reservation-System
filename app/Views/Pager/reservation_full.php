<?php $pager->setSurroundCount(1); ?>
<nav aria-label="Reservation pages">
    <ul class="pagination pagination-sm mb-0">
        <?php if ($pager->hasPrevious()): ?><li class="page-item"><a class="page-link" href="<?= $pager->getPrevious() ?>" aria-label="Previous"><i class="bi bi-chevron-left" aria-hidden="true"></i></a></li><?php endif; ?>
        <?php foreach ($pager->links() as $link): ?><li class="page-item <?= $link['active'] ? 'active' : '' ?>"><a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a></li><?php endforeach; ?>
        <?php if ($pager->hasNext()): ?><li class="page-item"><a class="page-link" href="<?= $pager->getNext() ?>" aria-label="Next"><i class="bi bi-chevron-right" aria-hidden="true"></i></a></li><?php endif; ?>
    </ul>
</nav>