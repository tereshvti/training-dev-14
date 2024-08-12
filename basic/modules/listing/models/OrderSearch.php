<?php

namespace listing\models;

use listing\helpers\UrlHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

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
    public function getDataProviderWithSearch($params)
    {
        $this->loadAndValidate($params);

        $query = $this->prepareBaseQuery();

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
    public function loadAndValidate($params)
    {
        if ($this->load($params, '') && !$this->validate()) {
            foreach ($this->getErrors() as $field => $error) {
                //reset field
                $this->$field = null;
            }
        }
    }

    /**
     * @return ActiveQuery
     */
    public function prepareBaseQuery()
    {
        $query = Order::find();
        $query->joinWith(['service', 'user']);
        // grid filtering conditions
        $query->andFilterWhere([
            'status' => $this->status,
            'mode' => $this->mode,
            'service_id' => $this->service_id
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
                    $query->andFilterWhere(['like', 'first_name', $this->search]);
                    $query->orFilterWhere(['like', 'last_name', $this->search]);
                    break;
            }
        }
        $query->orderBy([Order::tableName() . '.id' => SORT_DESC]);

        return $query;
    }
}
