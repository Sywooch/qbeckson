<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_search_filters_assignment`.
 */
class m170616_072950_create_user_search_filters_assignment_table extends Migration
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

        $this->createTable('user_search_filters_assignment', [
            'user_id' => $this->integer()->notNull(),
            'filter_id' => $this->integer()->notNull(),
            'user_columns' => $this->text(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-user-filter', 'user_search_filters_assignment', ['user_id', 'filter_id']
        );

        $this->addForeignKey('fk-user-filter-user', 'user_search_filters_assignment', 'user_id', 'user', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk-user-filter-filter', 'user_search_filters_assignment', 'filter_id', 'settings_search_filters', 'id', 'cascade', 'cascade');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_search_filters_assignment');
    }
}
