<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var listing\models\OrderSearch $model */
/** @var listing\helpers\GridHelper $gridHelper */

$li = [];
$totalCount = 0;
foreach ($model->getServiceHeaderData() as $serviceData) {
    $li[] = Html::a(Html::tag('span', $serviceData['count'], ['class' => 'label-id']) .
        \Yii::t('listing', \Yii::t('listing', $serviceData['name'])),
        $gridHelper->createUrl('service_id', $serviceData['id']),
        ['disabled' => $serviceData['count'] == 0 ? 'true' : 'false'] //disable service filters without results
    );
    $totalCount += $serviceData['count'];
}
array_unshift(
    $li,
    Html::a(Yii::t('listing', "All ($totalCount)"), $gridHelper->createUrl('service_id'))
);
$ul = Html::ul($li, ['class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu1', 'encode' => false]);
?>

<div class="dropdown">
    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
        Service
        <span class="caret"></span>
    </button>
    <?= $ul ?>
</div>