<?php

use yii\db\Migration;

class m170802_142709_add_certificate_information_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('certificate_information', [
            'id' => $this->primaryKey(),
            'payer_id' => $this->integer(11),
            'children_category' => $this->string(255),
            'organization_name' => $this->string(255),
            'work_time' => $this->string(255),
            'full_name' => $this->string(255),
            'rules' => $this->text(),
            'statement_path' => $this->string(255),
            'statement_base_url' => $this->string(255)
        ], $tableOptions);

        $this->addForeignKey(
            'certificate_information-payer_id-payers-id',
            'certificate_information',
            'payer_id',
            'payers',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('certificate_information-payer_id-payers-id', 'certificate_information');
        $this->dropTable('certificate_information');
    }
}
