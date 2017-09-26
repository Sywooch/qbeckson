<?php

use yii\db\Migration;

/**
 * Handles the creation of table `export_file`.
 */
class m170915_071612_create_export_file_table extends Migration
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

        $this->createTable('export_file', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'item_list' => $this->text(),
            'path' => $this->string(),
            'file' => $this->string(),
            'created_at' => $this->integer(),
        ], $tableOptions);

        $this->addForeignKey('fk-export-file-user', 'export_file', 'user_id', 'user', 'id', 'cascade', 'cascade');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('export_file');
    }
}
