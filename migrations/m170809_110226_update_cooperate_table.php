<?php

use yii\db\Migration;

class m170809_110226_update_cooperate_table extends Migration
{
    public function safeUp()
    {
        //current_year_payment (оплачено в текущем году)
        //total_payment (оплачено всего)
        //current_year_payment_limit (максимум в текущем году)
        //total_payment_limit (максимум всего)
        //additional_number реквизиты допсоглашения (номер)
        //additional_date реквизиты допсоглашения (дата)
        //additional_document_base_url additional_document_path ссылка на допсоглашение
        $this->addColumn('cooperate', 'current_year_payment', 'DECIMAL(19,4) DEFAULT NULL');
        $this->addColumn('cooperate', 'total_payment', 'DECIMAL(19,4) DEFAULT NULL');
        $this->addColumn('cooperate', 'current_year_payment_limit', 'DECIMAL(19,4) DEFAULT NULL');
        $this->addColumn('cooperate', 'total_payment_limit', 'DECIMAL(19,4) DEFAULT NULL');
        $this->addColumn('cooperate', 'additional_number', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn('cooperate', 'additional_date', 'DATE DEFAULT NULL');
        $this->addColumn('cooperate', 'additional_document_base_url', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn('cooperate', 'additional_document_path', 'VARCHAR(255) DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('cooperate', 'current_year_payment');
        $this->dropColumn('cooperate', 'total_payment');
        $this->dropColumn('cooperate', 'current_year_payment_limit');
        $this->dropColumn('cooperate', 'total_payment_limit');
        $this->dropColumn('cooperate', 'additional_number');
        $this->dropColumn('cooperate', 'additional_date');
        $this->dropColumn('cooperate', 'additional_document_base_url');
        $this->dropColumn('cooperate', 'additional_document_path');
    }
}
