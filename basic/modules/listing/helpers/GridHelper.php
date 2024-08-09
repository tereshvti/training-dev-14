<?php

namespace listing\helpers;

use yii\web\Request;

class GridHelper
{
    /**
     * @param $paramName
     * @param $value
     * @return string
     */
    public function createUrl($paramName, $value = null, $skipOtherParams = false)
    {
        $request = \Yii::$app->getRequest();
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
        $params[0] = \Yii::$app->controller->getRoute();
        $urlManager = \Yii::$app->getUrlManager();

        return $urlManager->createUrl($params);
    }
}