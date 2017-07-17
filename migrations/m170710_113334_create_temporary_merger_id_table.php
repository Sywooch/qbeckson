<?php

use yii\db\Migration;

/**
 * Handles the creation of table `temporary_merger_id`.
 */
class m170710_113334_create_temporary_merger_id_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('temporary_merger_id', [
            'id' => $this->primaryKey(),
            'table_name' => $this->string(),
            'old_id' => $this->integer(),
            'new_id' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('temporary_merger_id');
    }
}
