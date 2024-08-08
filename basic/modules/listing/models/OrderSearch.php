<?php

namespace listing\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

class OrderSearch extends Model
{
    const INPUT_NAME_ORDER_ID = 'order_id';
    const INPUT_NAME_LINK = 'link';
    const INPUT_NAME_USERNAME = 'username';

    public $service_id;
    public $status;
    public $mode;
    public $search;
    public $search_type;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mode', 'status', 'service_id'], 'integer'],
            [['search', 'search_type'], 'string'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->loadAndValidate($params);

        $query = $this->prepareBaseQuery();
        $query->andFilterWhere(['service_id' => $this->service_id]);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    /**
     * @param $params
     * @return void
     */
    protected function loadAndValidate($params)
    {
        $this->load($params, '');
        if (!$this->validate()) {
            foreach ($this->getErrors() as $field => $error) {
                //reset field
                $this->$field = null;
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    protected function prepareBaseQuery()
    {
        $query = Order::find();
        $query->joinWith(['service', 'user']);
        // grid filtering conditions
        $query->andFilterWhere([
            'status' => $this->status,
            'mode' => $this->mode,
        ]);

        if (isset($this->search) && isset($this->search_type)) {
            switch ($this->search_type) {
                case self::INPUT_NAME_ORDER_ID:
                    $query->andFilterWhere([Order::tableName() . '.id' => $this->search]);
                    break;
                case self::INPUT_NAME_LINK:
                    $query->andFilterWhere(['like', 'link', $this->search]);
                    break;
                case self::INPUT_NAME_USERNAME:
                    $query->joinWith(['user']);
                    $query->andFilterWhere(['like', 'first_name', $this->search]);
                    $query->orFilterWhere(['like', 'last_name', $this->search]);
                    break;
            }
        }
        $query->orderBy([Order::tableName() . '.id' => SORT_DESC]);

        return $query;
    }

    /**
     * @return array
     */
    public function getGroupedServiceData()
    {
        $query = $this->prepareBaseQuery();
        $query->groupBy('service_id');
        $query->select(['count' => 'COUNT(`' . Order::tableName() . '`.`id`)', 'service_id']);
        $query->asArray();
        $query->orderBy(['service_id' => SORT_ASC]);

        return $query->all();
    }

    /**
     * @param array $params
     * @return \Generator
     * @throws \yii\base\InvalidConfigException
     */
    public function getOrderDataForCsvExport(array $params)
    {
        $this->loadAndValidate($params);
        $query = $this->prepareBaseQuery();
        $query->andFilterWhere(['service_id' => $this->service_id]);
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
            $query->offset(($pageNumber - 1) * 10000)->limit(10000);
            $data = $query->createCommand()->queryAll();
            foreach ($data as $item) {
                $rowData = [
                    $item['id'],
                    $item['full_name'],
                    $item['link'],
                    $item['quantity'],
                    sprintf("(%s) %s", $item['service_id'], $item['service_name']),
                    (int)$item['mode'] === 1 ? \Yii::t('app', 'Auto') : \Yii::t('app', 'Manual'),
                    date('Y-m-d H:i:s', $item['created_at'])
                ];
                yield $rowData;
            }
        }
    }
}
