<?php

use yii\db\Migration;

class m171206_150215_add_columns_to_mun_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('mun', 'mun_id', $this->integer());
        $this->addColumn('mun', 'user_id', $this->integer());
        $this->addColumn('mun', 'type', $this->smallInteger()->notNull()->defaultValue(1));
        $this->addColumn('mun', 'file', $this->string());
        $this->addColumn('mun', 'base_url', $this->string());

        $this->createIndex(
            'idx-mun-mun_id',
            'mun',
            'mun_id'
        );

        $this->addForeignKey(
            'fk-mun-mun_id',
            'mun',
            'mun_id',
            'mun',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex(
            'idx-mun-user_id',
            'mun',
            'user_id'
        );

        $this->addForeignKey(
            'fk-mun-user_id',
            'mun',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-mun-user_id', 'mun');
        $this->dropForeignKey('fk-mun-mun_id', 'mun');

        $this->dropIndex('idx-mun-user_id', 'mun');
        $this->dropIndex('idx-mun-mun_id', 'mun');

        $this->dropColumn('mun', 'mun_id');
        $this->dropColumn('mun', 'user_id');
        $this->dropColumn('mun', 'type');
        $this->dropColumn('mun', 'file');
        $this->dropColumn('mun', 'base_url');
    }
}
