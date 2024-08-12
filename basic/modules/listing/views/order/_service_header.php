<?php

use yii\helpers\Html;
use listing\helpers\UrlHelper;

/** @var yii\web\View $this */
/** @var array $servicesData */

$li = [];
$totalCount = 0;
foreach ($servicesData as $serviceData) {
    $li[] = Html::a(Html::tag('span', $serviceData['count'], ['class' => 'label-id']) .
        $serviceData['name'],
        $serviceData['url'],
        ['disabled' => $serviceData['count'] == 0 ? 'true' : 'false'] //disable service filters without results
    );
    $totalCount += $serviceData['count'];
}
array_unshift(
    $li,
    Html::a(Yii::t('listing', "All ($totalCount)"), UrlHelper::createUrl('service_id'))
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