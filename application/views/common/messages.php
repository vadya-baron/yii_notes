<?php

/** @var array<string> $list */

if (!$list) {
    return false;
}
?>

<ul class="messages-list">
    <?php foreach ($list as $msg): ?>
        <li><?php echo $msg ?></li>
    <?php endforeach; ?>
</ul>