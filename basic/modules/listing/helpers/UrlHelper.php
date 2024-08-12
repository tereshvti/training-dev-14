<?php

namespace listing\helpers;

use listing\models\Order;
use Yii;
use yii\web\Request;

class UrlHelper
{
    /**
     * @param $paramName
     * @param $value
     * @param $skipOtherParams
     * @return string
     */
    public static function createUrl($paramName, $value = null, $skipOtherParams = false)
    {
        $request = Yii::$app->getRequest();
        if ($skipOtherParams) {
            $params= [];
        } else {
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }

        if (is_null($value)) {
            unset($params[$paramName]);
        } else {
            $params[$paramName] = (string) $value;
        }
        $params[0] = Yii::$app->controller->getRoute();
        $urlManager = Yii::$app->getUrlManager();

        return $urlManager->createUrl($params);
    }

    /**
     * Return list of order statuses with URLs
     * @return array
     */
    public static function getStatusList() {
        $data = [];
        foreach (Order::STATUS_LIST as $value => $name) {
            $data[] = [
                'name' => Yii::t('listing', $name),
                'url' => self::createUrl('status', $value),
                'value' => $value
            ];
        }

        return $data;
    }

    /**
     * Return list of order modes with URLs
     * @return array[]
     */
    public static function getModeList() {
        $data = [
            [
                'name' => Yii::t('listing', 'All'),
                'url' => self::createUrl('mode', NULL)
            ]
        ];
        foreach (Order::MODE_LIST as $value => $name) {
            $data[] = [
                'name' => Yii::t('listing', $name),
                'url' => self::createUrl('mode', $value)
            ];
        }

        return $data;
    }
}