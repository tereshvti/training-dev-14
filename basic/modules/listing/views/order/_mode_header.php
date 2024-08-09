<?php

use listing\models\Order;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var listing\helpers\GridHelper $gridHelper */

$ul = Html::ul([
    Html::a(\Yii::t('listing', 'All'), $gridHelper->createUrl('mode', NULL)),
    Html::a(\Yii::t('listing', 'Manual'), $gridHelper->createUrl('mode', Order::MODE_MANUAL)),
    Html::a(\Yii::t('listing', 'Auto'), $gridHelper->createUrl('mode', Order::MODE_AUTO)),
], ['class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu1', 'encode' => false]);

?>

<div class="dropdown">
    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Mode
        <span class="caret"></span>
    </button>
    <?= $ul ?>
</div>