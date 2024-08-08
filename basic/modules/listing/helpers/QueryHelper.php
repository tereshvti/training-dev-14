<?php

namespace listing\helpers;

use listing\models\Order;
use listing\models\OrderSearch;
use yii\db\QueryInterface;
use yii2tech\csvgrid\ExportResult;

class QueryHelper
{
    /**
     * @param QueryInterface $query
     * @return array
     */
    public function getGroupedServiceDataFromQuery(QueryInterface $originalQuery)
    {
        $query = clone $originalQuery;
        //remove service ID filter to list all services
        if (!is_null($query->where)) {
            foreach ($query->where as $index => $wherePart) {
                if (is_array($wherePart) && array_key_exists('service_id', $wherePart)) {
                    unset($query->where[$index]['service_id']);
                }
            }
        }

        $query->groupBy('service_id');
        $query->select(['count' => 'COUNT(`' . Order::tableName() . '`.`id`)', 'service_id']);
        $query->asArray(true);

        return $query->all();
    }

    /**
     * @param array $params
     * @return \Generator
     * @throws \yii\base\InvalidConfigException
     */
    public function getOrderDataForCsvExport(array $params)
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($params);
        $query = $dataProvider->query;
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

        /** @var ExportResult $exportResult */
        $exportResult = \Yii::createObject(['class' => ExportResult::class]);
        $csvFile = $exportResult->newCsvFile();
        $csvFile->writeRow([
            \Yii::t('app', 'ID'),
            \Yii::t('app', 'User'),
            \Yii::t('app', 'Link'),
            \Yii::t('app', 'Quantity'),
            \Yii::t('app', 'Service'),
            \Yii::t('app', 'Mode'),
            \Yii::t('app', 'Created')
        ]);

        for ($i = 1; $i <= $pageNumber; $i++) {
            $query->offset(($pageNumber - 1) * 10000)->limit(10000);
            $data = $query->createCommand()->queryAll();
            foreach ($data as $item) {
                $rowData = [
                    $item['id'],
                    $item['full_name'],
                    $item['link'],
                    $item['quantity'],
                    sprintf("(%s) %s", $item['service_id'], $item['service_name']),
                    (int) $item['mode'] === 1 ? \Yii::t('app', 'Auto') : \Yii::t('app', 'Manual'),
                    date('Y-m-d H:i:s', $item['created_at'])
                ];
                yield $rowData;
            }
        }
    }
}
