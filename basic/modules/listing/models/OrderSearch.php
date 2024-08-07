<?php

namespace app\modules\listing\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderSearch represents the model behind the search form of `app\modules\listing\models\Order`.
 */
class OrderSearch extends Order
{
    const INPUT_NAME_ORDER_ID = 'order_id';
    const INPUT_NAME_LINK = 'link';
    const INPUT_NAME_USERNAME = 'username';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'integer'],
            [['link'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = Order::find();
        $query->joinWith(['service', 'user']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (isset($params['search']) && isset($params['search-type'])) {
            switch ($params['search-type']) {
                case self::INPUT_NAME_ORDER_ID:
                    $query->andFilterWhere([Order::tableName() . '.id' => $params['search']]);
                    break;
                case self::INPUT_NAME_LINK:
                    $query->andFilterWhere(['like', 'link', $params['search']]);
                    break;
                case self::INPUT_NAME_USERNAME:
                    $query->joinWith(['user']);
                    $query->andFilterWhere(['like', 'first_name', $params['search']]);
                    $query->orFilterWhere(['like', 'last_name', $params['search']]);
                    break;
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'service_id' => $this->service_id,
            'status' => $this->status,
            'mode' => $this->mode,
        ]);

        return $dataProvider;
    }
}
