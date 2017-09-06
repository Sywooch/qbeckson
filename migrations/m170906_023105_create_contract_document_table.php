<?php

use yii\db\Migration;

/**
 * Handles the creation of table `contract_document`.
 */
class m170906_023105_create_contract_document_table extends Migration
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

        $this->createTable('contract_document', [
            'id' => $this->primaryKey(),
            'payer_id' => $this->integer(),
            'contract_list' => $this->text(),
            'file' => $this->string(),
            'created_at' => $this->date(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('contract_document');
    }
}
