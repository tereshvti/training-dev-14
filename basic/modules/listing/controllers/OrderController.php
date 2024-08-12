<?php

namespace listing\controllers;

use app\modules\listing\models\OrderExport;
use app\modules\listing\models\ServiceSearch;
use listing\helpers\UrlHelper;
use listing\models\OrderSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\helpers\Url;
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
        $orderSearch = Yii::createObject(OrderSearch::class);
        $serviceSearch = Yii::createObject(ServiceSearch::class);
        $dataProvider = $orderSearch->getDataProviderWithSearch($this->request->queryParams);
        $dataProvider->setSort(false);
        $dataProvider->setPagination(['pageSize' => 100]);
        $pageCount = $dataProvider->getPagination()->getLimit();
        $totalCount = $dataProvider->getTotalCount();
        $summary = $totalCount > $pageCount ? '{begin, number} to {end, number} of {totalCount, number}'
            : '{totalCount, number}';

        return $this->render('index', [
            'servicesData' => $serviceSearch->getGroupedServiceData($this->request->queryParams),
            'dataProvider' => $dataProvider,
            'orderStatuses' => UrlHelper::getStatusList(),
            'orderModes' => UrlHelper::getModeList(),
            'statusFilterValue' => $this->request->getQueryParam('status'),
            'summary' => $summary,
            'saveResultUrl' => Url::to(array_merge(['export'], $orderSearch->getAttributes())), //values are validated
        ]);
    }

    /**
     * @return Response
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionExport()
    {
        /** @var OrderExport $export */
        $export = Yii::createObject(['class' => OrderExport::class]);
        /** @var ExportResult $exportResult */
        $exportResult = $export->exportServiceDataToCsv($this->request->queryParams);
        $filename = 'items' . '-' . time() . '.csv';

        return $exportResult->send($filename);
    }
}
