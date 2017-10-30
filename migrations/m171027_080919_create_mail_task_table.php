<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mail_task`.
 */
class m171027_080919_create_mail_task_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%mail_task}}', [
            'id' => $this->primaryKey(),
            'mailing_list_id' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull()
                ->comment('1 - created; 10 - inQueue  30 - finish; 40 - has errors; '),
            'target_user_id' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'log_message' => $this->string(255)->null(),
            'email' => $this->string(255)->notNull(),
            'target_type' => $this->integer()->comment('10 - organization, 20 - payer')->notNull(),
        ]);
        $this->createIndex('idx_mail_task_status', '{{%mail_task}}', 'status');
        $this->addForeignKey(
            'fk_mail_task_mailing_list_id_mailing_list',
            '{{%mail_task}}',
            'mailing_list_id',
            '{{%mailing_list}}',
            'id'
        );
        $this->addForeignKey(
            'fk_mail_task_target_user_id_user_id',
            '{{%mail_task}}',
            'target_user_id',
            '{{%user}}',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk_mail_task_mailing_list_id_mailing_list',
            '{{%mail_task}}'
        );
        $this->dropTable('{{%mail_task}}');
    }
}
