<?php

use yii\db\Migration;

/**
 * Handles the creation of table `help`.
 */
class m170829_094554_create_help_table extends Migration
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

        $this->createTable('help', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'body' => $this->text(),
            'applied_to' => $this->text(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('help');
    }
}
