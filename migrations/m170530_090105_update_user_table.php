<?php

use yii\db\Migration;

class m170530_090105_update_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('user', 'mun_id', 'int(11) DEFAULT NULL');
        $this->addForeignKey('fk-user_mun_id-mun_id', 'user', 'mun_id', 'mun', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-user_mun_id-mun_id', 'user');
        $this->dropColumn('user', 'mun_id');
    }
}
