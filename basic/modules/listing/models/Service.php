<?php

namespace listing\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "services".
 *
 * @property int $id
 * @property string $name
 */
class Service extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('listing', 'ID'),
            'name' => Yii::t('listing', 'Name'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['service_id' => 'id']);
    }
}
