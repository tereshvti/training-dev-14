<?php

use listing\models\Order;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var listing\models\OrderSearch $searchModel */
/** @var listing\helpers\GridHelper $gridHelper */
/** @var array $orderStatuses */
/** @var string|null $statusFilterValue */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Orders');
//@TODO find better place to remove BS assets
Yii::$app->assetManager->bundles['yii\bootstrap5\BootstrapAsset'] = false;

?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        <ul class="nav nav-tabs p-b">
            <li class="<?= is_null($statusFilterValue) ? 'active' : ''?>">
                <a href="<?= $gridHelper->createUrl('status', NULL, true) ?>">All orders</a>
            </li>
            <?php foreach ($orderStatuses as $value => $orderStatus): ?>
                <?= Html::tag('li', Html::a($orderStatus, $gridHelper->createUrl('status', $value, true)),
                ['class' => $statusFilterValue === (string) $value ? 'active' : '' ]) ?>
            <?php endforeach; ?>

            <?php  echo $this->render('_search', [
                'model' => $searchModel,
                'statusFilterValue' => $statusFilterValue
            ]); ?>
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
                    'header' => $gridHelper->getServiceHeaderHtml($dataProvider),
                    'attribute' => 'service',
                    'class' => \yii\grid\DataColumn::class,
                    'format' => 'html',
                    'value' => function ($model) {
                        return Html::tag('span', $model->service->id, ['class' => 'label-id']) . $model->service->name;
                    },
                    'headerOptions' => ['class' => 'dropdown-th']
                ],
                [
                    'attribute' => 'mode',
                    'header' => $gridHelper->getModeHeaderHtml(),
                    'value' => function ($model) {
                        return $model->mode == Order::MODE_AUTO ? Yii::t('app', 'Auto')
                            : Yii::t('app', 'Manual');
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
            'summary' => Yii::t('yii','{begin, number} to {end, number} of {totalCount, number}'),
        ]);
        ?>
    </div>
    <div class="row">
        <div class="col-sm-8"></div>
        <div class="col-sm-4 button-area">
            <a href="<?= Url::to(array_merge(['export'], Yii::$app->request->getQueryParams())); ?>"
               class="btn btn-success" role="button"><?= Yii::t('app', 'Save result') ?></a>
        </div>
    </div>
</div>
