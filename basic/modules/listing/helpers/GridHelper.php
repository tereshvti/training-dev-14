<?php

namespace listing\helpers;

use listing\models\Order;
use listing\models\Service;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\Request;

class GridHelper
{
    /**
     * @param ActiveDataProvider $dataProvider
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getServiceHeaderHtml(ActiveDataProvider $dataProvider)
    {
        $queryHelper = \Yii::createObject(QueryHelper::class);
        $usedServices = [];
        foreach($queryHelper->getGroupedServiceDataFromQuery($dataProvider->query) as $serviceData) {
            $usedServices[$serviceData['service_id']] = $serviceData['count'];
        }

        $totalCount = 0;
        foreach (Service::find()->all() as $service) {
            $count = isset($usedServices[$service->id]) ? $usedServices[$service->id] : 0;
            $totalCount += $count;
            $li[] = [
                'tag' => Html::a(Html::tag('span', $count, ['class' => 'label-id']) .
                    \Yii::t('app', \Yii::t('app', $service->name)),
                    $this->createUrl('service_id', $service->id),
                    ['disabled' => $count == 0 ? 'true' : 'false'] //disable service filters without results
                ),
                'count' => $count,
            ];
        }
        //some array functions for sorting by count
        usort($li, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $li = array_column($li, 'tag');

        array_unshift(
            $li,
            Html::a(\Yii::t('app', "All ($totalCount)"), $this->createUrl('service_id'))
        );

        $ul = Html::ul($li, ['class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu1', 'encode' => false]);

        $html = '<div class="dropdown">
          <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="false">
        Service
            <span class="caret"></span>
          </button>' . $ul . '</div>';

        return $html;
    }

    /**
     * @return string
     */
    public function getModeHeaderHtml()
    {
        $ul = Html::ul([
            Html::a(\Yii::t('app', 'All'), $this->createUrl('mode', NULL)),
            Html::a(\Yii::t('app', 'Manual'), $this->createUrl('mode', Order::MODE_MANUAL)),
            Html::a(\Yii::t('app', 'Auto'), $this->createUrl('mode', Order::MODE_AUTO)),
        ], ['class' => 'dropdown-menu', 'aria-labelledby' => 'dropdownMenu1', 'encode' => false]);


        $html = '<div class="dropdown">
          <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Mode
            <span class="caret"></span>
          </button>' . $ul . '</div>';


        return $html;
    }

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