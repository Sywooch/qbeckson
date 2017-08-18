<?php

use yii\db\Migration;

class m170817_120924_update_certificates_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('certificates', 'nominal_p', 'DECIMAL(19,2) NOT NULL DEFAULT 0');
        $this->addColumn('certificates', 'balance_p', 'DECIMAL(19,2) NOT NULL DEFAULT 0');
        $this->addColumn('certificates', 'rezerv_p', 'DECIMAL(19,2) NOT NULL DEFAULT 0');
    }

    public function safeDown()
    {
        $this->dropColumn('certificates', 'nominal_p');
        $this->dropColumn('certificates', 'balance_p');
        $this->dropColumn('certificates', 'rezerv_p');
    }
}
