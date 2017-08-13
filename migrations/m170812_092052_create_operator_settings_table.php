<?php

use yii\db\Migration;

/**
 * Handles the creation of table `operator_settings`.
 */
class m170812_092052_create_operator_settings_table extends Migration
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

        $this->createTable('operator_settings', [
            'id' => $this->primaryKey(11),
            'operator_id' => $this->integer(11)->notNull(),
            'document_name' => $this->string(255)->notNull(),

            'general_document_path' => $this->string(255)->notNull(),
            'general_document_base_url' => $this->string(255)->notNull(),

            'extend_document_path' => $this->string(255)->notNull(),
            'extend_document_base_url' => $this->string(255)->notNull(),

            'current_program_date_from' => $this->date()->notNull(),
            'current_program_date_to' => $this->date()->notNull(),

            'future_program_date_from' => $this->date()->notNull(),
            'future_program_date_to' => $this->date()->notNull(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('operator_settings');
    }
}
