<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $orderModes */

$li = [];
foreach ($orderModes as $modeData) {
    $li[] = Html::a($modeData['name'], $modeData['url']);
}
$ul = Html::ul($li, [
        'class' => 'dropdown-menu',
        'aria-labelledby' => 'dropdownMenu1',
        'encode' => false
    ]
);
?>

<div class="dropdown">
    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Mode
        <span class="caret"></span>
    </button>
    <?= $ul ?>
</div>