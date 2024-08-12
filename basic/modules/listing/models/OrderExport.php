<?php

namespace app\modules\listing\models;

use listing\models\OrderSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii2tech\csvgrid\ExportResult;

class OrderExport
{
    /**
     * @param array $params
     * @return Generator
     * @throws Exception
     */
    public function getServiceDataForExport(array $params)
    {
        $orderSearch = new OrderSearch();
        $orderSearch->loadAndValidate($params);
        $query = $orderSearch->prepareBaseQuery();
        $count = $query->count();
        $pageNumber = ceil($count / 10000);
        $query->select([
            'id' => 'orders.id',
            'full_name' => 'CONCAT(users.first_name, " ", users.last_name)',
            'link',
            'quantity',
            'service_id' => 'services.id',
            'service_name' => 'services.name',
            'mode',
            'created_at'
        ]);

        for ($i = 1; $i <= $pageNumber; $i++) {
            $query->offset(($i - 1) * 10000)->limit(10000);
            $data = $query->createCommand()->queryAll();
            foreach ($data as $item) {
                $rowData = [
                    $item['id'],
                    $item['full_name'],
                    $item['link'],
                    $item['quantity'],
                    sprintf("(%s) %s", $item['service_id'], $item['service_name']),
                    (int)$item['mode'] === 1 ? Yii::t('listing', 'Auto') : Yii::t('listing', 'Manual'),
                    date('Y-m-d H:i:s', $item['created_at'])
                ];
                yield $rowData;
            }
        }
    }

    /**
     * @param array $params
     * @return ExportResult
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function exportServiceDataToCsv(array $params)
    {
        /** @var ExportResult $exportResult */
        $exportResult = Yii::createObject(['class' => ExportResult::class]);
        $csvFile = $exportResult->newCsvFile();
        $csvFile->writeRow([
            Yii::t('listing', 'ID'),
            Yii::t('listing', 'User'),
            Yii::t('listing', 'Link'),
            Yii::t('listing', 'Quantity'),
            Yii::t('listing', 'Service'),
            Yii::t('listing', 'Mode'),
            Yii::t('listing', 'Created')
        ]);

        $rows = 0;
        foreach ($this->getServiceDataForExport($params) as $rowData) {
            $rows++;
            $csvFile->writeRow($rowData);
        }

        return $exportResult;
    }
}