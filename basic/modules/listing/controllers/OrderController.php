<?php

namespace listing\controllers;

use listing\helpers\GridHelper;
use listing\helpers\QueryHelper;
use listing\models\Order;
use listing\models\OrderSearch;
use yii\web\Controller;
use yii\web\Response;
use yii2tech\csvgrid\ExportResult;

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
     * @return Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionExport()
    {
        /** @var ExportResult $exportResult */
        $exportResult = \Yii::createObject(['class' => ExportResult::class]);
        $csvFile = $exportResult->newCsvFile();
        $csvFile->writeRow(['ID', 'User', 'Link', 'Quantity', 'Service', 'Mode', 'Created']);

        /** @var QueryHelper $queryHelper */
        $queryHelper =  \Yii::createObject(['class' => QueryHelper::class]);
        foreach ($queryHelper->getOrderDataForCsvExport($this->request->queryParams) as $rowData) {
            $csvFile->writeRow($rowData);
        }

        $filename = 'items' . '-' . time() . '.csv';

        return $exportResult->send($filename);
    }
}
