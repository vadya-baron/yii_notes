<?php

/** @var array<string> $list */

if (!$list) {
    return false;
}
?>

<ul class="errors-list">
    <?php foreach ($list as $error): ?>
        <li><?php echo $error ?></li>
    <?php endforeach?>
</ul>