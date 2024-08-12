<?php

namespace app\modules\listing\models;

use listing\helpers\UrlHelper;
use listing\models\Order;
use listing\models\OrderSearch;
use listing\models\Service;
use listing\models\User;
use Yii;
use yii\base\InvalidConfigException;

class ServiceSearch
{
    /**
     * @param $params
     * @return array
     * @throws InvalidConfigException
     */
    public function getGroupedServiceData($params)
    {
        $orderSearch = Yii::createObject(OrderSearch::class);
        $orderSearch->loadAndValidate($params);
        $orderCondition = 'service.id = order.service_id';
        $userCondition = '';
        $params = [];
        if (isset($orderSearch->search) && isset($orderSearch->search_type)) {
            switch ($orderSearch->search_type) {
                case OrderSearch::INPUT_NAME_ORDER_ID:
                    $orderCondition .= ' AND order.id = :order_id';
                    $params['order_id'] = $orderSearch->search;
                    break;
                case OrderSearch::INPUT_NAME_LINK:
                    $orderCondition .= " AND order.link LIKE :link";
                    $params['link'] = "%$orderSearch->search%";
                    break;
                case OrderSearch::INPUT_NAME_USERNAME:
                    $userCondition .= " AND (user.first_name LIKE :name OR user.last_name LIKE :name)";
                    $params['name'] = "%$orderSearch->search%";;
                    break;
            }
        }

        if (array_key_exists($orderSearch->status, Order::STATUS_LIST)) {
            $orderCondition .= " AND order.status = :order_status";
            $params['order_status'] = $orderSearch->status;
        }
        if (!is_null($orderSearch->mode)) {
            $orderCondition .= " AND order.mode = :order_mode";
            $params['order_mode'] = $orderSearch->mode;
        }

        $query = Service::find()
            ->alias('service')
            ->select(['count' => 'COUNT(order.id)', 'id' => 'service.id', 'name' => 'service.name'])
            ->leftJoin(['order' => Order::tableName()], $orderCondition)
            ->groupBy('service.id')
            ->orderBy(['count' => SORT_DESC])
            ->asArray();

        if (!empty($userCondition)) {
            $userCondition = 'user.id = order.id' . $userCondition;
            $query->leftJoin(['user' => User::tableName()], $userCondition);
        }
        $query->params($params);
        $servicesData = [];
        foreach($query->all() as $serviceData) {
            $serviceData['url'] = UrlHelper::createUrl('service_id', $serviceData['id']);
            $servicesData[] = $serviceData;
        }

        return $servicesData;
    }
}