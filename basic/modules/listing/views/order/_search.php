<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use listing\models\OrderSearch;

/** @var yii\web\View $this */
/** @var string|null $statusFilterValue */
/** @var yii\widgets\ActiveForm $form */

?>

<li class="pull-right custom-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'form-inline'
        ]
    ]); ?>
    <div class="input-group">
        <input type="text" name="search" class="form-control" value="" placeholder="Search orders">
        <span class="input-group-btn search-select-wrap">

        <?=
            Html::dropDownList('search_type', null, [
                OrderSearch::INPUT_NAME_ORDER_ID => 'Order ID',
                OrderSearch::INPUT_NAME_LINK => 'Link',
                OrderSearch::INPUT_NAME_USERNAME => 'Username'
            ],
            ['class' => 'form-control search-select', 'name' => 'search_type'])
        ?>

            <?= Html::hiddenInput('status', $statusFilterValue, ['id' => 'status']) ?>

        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
        </span>
    </div>
    <?php ActiveForm::end(); ?>
</li>

<div class="order-search">

</div>
