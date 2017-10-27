<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mailing_list`.
 */
class m171027_080558_create_mailing_list_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%mailing_list}}', [
            'id' => $this->primaryKey(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'subject' => $this->string(40)->notNull(),
            'message' => $this->text()->notNull(),
        ]);
        $this->createIndex('idx_mailing_list_created_by', '{{%mailing_list}}', 'created_by');
        $this->createIndex('idx_mailing_list_created_at', '{{%mailing_list}}', 'created_at');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%mailing_list}}');
    }
}
