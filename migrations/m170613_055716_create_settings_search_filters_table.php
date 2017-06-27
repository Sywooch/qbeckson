<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings_search_filters`.
 */
class m170613_055716_create_settings_search_filters_table extends Migration
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

        $this->createTable('settings_search_filters', [
            'id' => $this->primaryKey(),
            'table_name' => $this->string()->unique(),
            'table_columns' => $this->text(),
            'inaccessible_columns' => $this->text(),
            'is_active' => $this->integer()->defaultValue(1),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('settings_search_filters');
    }
}
