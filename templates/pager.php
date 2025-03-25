<nav class="pager">
<?php if ($page > 1): ?>
<a href="<?= $url ?>?page=<?= $page - 1 ?>&size=<?= $size ?>">&laquo;</a>
<?php else: ?>
<a>&laquo;</a>
<?php endif; ?>
<?php for ($p = 1; $p <= $total_pages; $p ++): ?>
<?php if ($p != $page): ?>
<a href="<?= $url ?>?page=<?= $p ?>&size=<?= $size ?>"><?= $p ?></a>
<?php else: ?>
<a class="selected"><?= $p ?></a>
<?php endif; ?>
<?php endfor; ?>
<?php if ($page < $total_pages): ?>
<a href="<?= $url ?>?page=<?= $page + 1 ?>&size=<?= $size ?>">&raquo;</a>
<?php else: ?>
<a>&raquo;</a>
<?php endif; ?>
</nav>
