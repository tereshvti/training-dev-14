<?php

namespace app\modules\listing\controllers;

use app\modules\listing\helpers\GridHelper;
use app\modules\listing\models\Order;
use app\modules\listing\models\OrderSearch;
use yii\web\Controller;
use yii2tech\csvgrid\CsvGrid;

/**
 * OrderController
 */
class OrderController extends Controller
{
    public $layout = '@app/modules/listing/views/layouts/main.php';

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $gridHelper = new GridHelper();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->setSort(false);
        $dataProvider->setPagination(['pageSize' => 100]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'orderStatuses' => Order::getStatusList(),
            'gridHelper' => $gridHelper,
            'statusFilterValue' => $this->request->getQueryParam('status'),
        ]);
    }

    /**
     * @return \yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionExport()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->setSort(false);
        $dataProvider->setPagination(['pageSize' => 8000]);

        $exporter = new CsvGrid([
            'dataProvider' => $dataProvider,
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
                    'attribute' => 'service',
                    'format' => 'html',
                    'value' => function ($model) {
                        return sprintf("(%s) %s", $model->service->id, $model->service->name);
                    },
                ],
                [
                    'attribute' => 'mode',
                    'value' => function ($model) {
                        return $model->mode == Order::MODE_AUTO ? \Yii::t('app', 'Auto')
                            : \Yii::t('app', 'Manual');
                    },
                ],
                [
                    'attribute' => 'created',
                    'value' => function ($model) {
                        return date('Y-m-d H:i:s', $model->created_at);
                    }
                ],
            ],
        ]);
        $filename = 'items' . '-' . time() . '.csv';

        return $exporter->export()->send($filename);
    }
}
