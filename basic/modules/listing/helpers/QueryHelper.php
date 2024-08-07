<?php

namespace app\modules\listing\helpers;

use app\modules\listing\models\Order;
use yii\db\QueryInterface;

class QueryHelper
{
    /**
     * @param QueryInterface $query
     * @return array
     */
    public function getGroupedServiceDataFromQuery(QueryInterface $query)
    {
        $originalQuery = clone $query;
        $originalQuery->groupBy('service_id');
        $originalQuery->select(['count' => 'COUNT(`' . Order::tableName() . '`.`id`)', 'service_id']);
        $originalQuery->asArray(true);

        return $originalQuery->all();
    }
}
