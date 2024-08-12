<?php

use listing\helpers\UrlHelper;
use listing\models\Order;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $servicesData */
/** @var array $orderStatuses */
/** @var array $orderModes */
/** @var string|null $statusFilterValue */
/** @var string $summary */
/** @var string $saveResultUrl */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('listing', 'Orders');
//@TODO find better place to remove BS assets
Yii::$app->assetManager->bundles['yii\bootstrap5\BootstrapAsset'] = false;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        <ul class="nav nav-tabs p-b">
            <li class="<?= is_null($statusFilterValue) ? 'active' : ''?>">
                <a href="<?= UrlHelper::createUrl('status', NULL, true) ?>">All orders</a>
            </li>
            <?php foreach ($orderStatuses as $orderStatusData): ?>
                <?= Html::tag('li', Html::a($orderStatusData['name'], $orderStatusData['url']),
                ['class' => $statusFilterValue === (string) $orderStatusData['value'] ? 'active' : '' ]) ?>
            <?php endforeach; ?>

            <?php echo $this->render('_search', ['statusFilterValue' => $statusFilterValue]); ?>
        </ul>
    </div>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}",
            'columns' => [
                'id',
                [
                    'attribute' => 'user',
                    'value' => function ($model) {
                        return $model->user->first_name . ' ' . $model->user->last_name;
                    }
                ],
                'link',
                'quantity',
                [
                    'header' => $this->render('_service_header', ['servicesData' => $servicesData]),
                    'attribute' => 'service',
                    'class' => DataColumn::class,
                    'format' => 'html',
                    'value' => function ($model) {
                        return Html::tag('span', $model->service->id, ['class' => 'label-id']) . $model->service->name;
                    },
                    'headerOptions' => ['class' => 'dropdown-th']
                ],
                [
                    'attribute' => 'mode',
                    'header' => $this->render('_mode_header', ['orderModes' => $orderModes]),
                    'value' => function ($model) {
                        return $model->mode == Order::MODE_AUTO ? Yii::t('listing', 'Auto')
                            : Yii::t('listing', 'Manual');
                    },
                    'headerOptions' => ['class' => 'dropdown-th']
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return date('Y-m-d H:i:s', $model->created_at);
                    }
                ],
            ]
        ]);
    ?>

    <div class="row">
        <div class="col-sm-8">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{pager}",
                'options' => [
                    'class' => '',
                    'tag' => 'nav'
                ],
            ]);
            ?>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{summary}",
            'options' => ['tag' => null],
            'summaryOptions' => [
                'class' => 'col-sm-4 pagination-counters',
            ],
            'summary' => Yii::t('listing', $summary),
        ]);
        ?>
    </div>
    <div class="row">
        <div class="col-sm-8"></div>
        <div class="col-sm-4 button-area">
            <a href="<?= $saveResultUrl ?>"
               class="btn btn-success" role="button"><?= Yii::t('listing', 'Save result') ?></a>
        </div>
    </div>
</div>
