<?php

namespace listing\controllers;

use listing\helpers\GridHelper;
use listing\models\Order;
use listing\models\OrderSearch;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii2tech\csvgrid\ExportResult;

/**
 * OrderController
 */
class OrderController extends Controller
{
    public $layout = '@listing/views/layouts/main.php';

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
     * @throws Yii\base\InvalidConfigException
     */
    public function actionExport()
    {
        /** @var ExportResult $exportResult */
        $exportResult = Yii::createObject(['class' => ExportResult::class]);
        $csvFile = $exportResult->newCsvFile();
        $csvFile->writeRow([
            Yii::t('app', 'ID'),
            Yii::t('app', 'User'),
            Yii::t('app', 'Link'),
            Yii::t('app', 'Quantity'),
            Yii::t('app', 'Service'),
            Yii::t('app', 'Mode'),
            Yii::t('app', 'Created')
        ]);

        $searchModel = new OrderSearch();
        foreach ($searchModel->getOrderDataForCsvExport($this->request->queryParams) as $rowData) {
            $csvFile->writeRow($rowData);
        }

        $filename = 'items' . '-' . time() . '.csv';

        return $exportResult->send($filename);
    }
}
