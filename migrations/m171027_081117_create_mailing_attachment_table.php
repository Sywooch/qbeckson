<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mailing_attachment`.
 */
class m171027_081117_create_mailing_attachment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%mailing_attachment}}', [
            'id' => $this->primaryKey(),
            'mailing_list_id' => $this->integer()->notNull(),
            'local_file_name' => $this->string(255)->notNull(),
            'original_file_name' => $this->string(255)->notNull(),
        ]);
        $this->addForeignKey(
            'fk_mailing_attachment_mailing_list_id_mailing_list',
            '{{%mailing_attachment}}',
            'mailing_list_id',
            '{{%mailing_list}}',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk_mailing_attachment_mailing_list_id_mailing_list',
            '{{%mailing_attachment}}'
        );
        $this->dropTable('{{%mailing_attachment}}');
    }
}
