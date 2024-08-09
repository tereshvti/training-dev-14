<?php

namespace listing\migrations;

use yii\db\Migration;

/**
 * Class m240802_134934_create_missing_fks
 */
class m240802_134934_create_missing_fks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey("USER_ID_FK", "orders", "user_id", "users", "id", "RESTRICT", "RESTRICT");
        $this->addForeignKey("SERVICE_ID_FK", "orders", "user_id", "users", "id", "RESTRICT", "RESTRICT");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey("USER_ID_FK", "orders");
        $this->dropForeignKey("SERVICE_ID_FK", "orders");
    }
}
