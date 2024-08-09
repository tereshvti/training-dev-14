<?php

namespace listing\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property string $link
 * @property int $quantity
 * @property int $service_id
 * @property int $status 0 - Pending, 1 - In progress, 2 - Completed, 3 - Canceled, 4 - Fail
 * @property int $created_at
 * @property int $mode 0 - Manual, 1 - Auto
 */
class Order extends ActiveRecord
{
    /**
     *  Service mode auto
     */
    const MODE_AUTO = 1;

    /**
     *  Service mode auto
     */
    const MODE_MANUAL = 0;

    /**
     *  Status pending
     */
    const STATUS_PENDING = 0;

    /**
     *  Status in progress
     */
    const STATUS_IN_PROGRESS = 1;

    /**
     *  Status completed
     */
    const STATUS_COMPLETED = 2;

    /**
     *  Status canceled
     */
    const STATUS_CANCELED = 3;

    /**
     *  Status error
     */
    const STATUS_ERROR = 4;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'link', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'required'],
            [['user_id', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'integer'],
            [['link'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'link' => 'Link',
            'quantity' => 'Quantity',
            'service_id' => 'Service ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'mode' => 'Mode',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }

    /**
     * @return array
     */
    public static function getStatusList() {
        return [
            self::STATUS_PENDING => Yii::t('listing', 'Pending'),
            self::STATUS_IN_PROGRESS => Yii::t('listing', 'In progress'),
            self::STATUS_COMPLETED => Yii::t('listing', 'Completed'),
            self::STATUS_CANCELED => Yii::t('listing', 'Canceled'),
            self::STATUS_ERROR => Yii::t('listing', 'Error'),
        ];
    }
}
