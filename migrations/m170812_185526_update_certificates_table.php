<?php

use yii\db\Migration;

class m170812_185526_update_certificates_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('certificates', 'nominal_f', 'DECIMAL(19,2) NOT NULL DEFAULT 0');
        $this->addColumn('certificates', 'balance_f', 'DECIMAL(19,2) NOT NULL DEFAULT 0');
        $this->addColumn('certificates', 'rezerv_f', 'DECIMAL(19,2) NOT NULL DEFAULT 0');

        $this->execute('UPDATE certificates set nominal_f = nominal WHERE id IS NOT NULL');
        $this->execute('UPDATE certificates set balance_f = nominal WHERE id IS NOT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('certificates', 'nominal_f');
        $this->dropColumn('certificates', 'balance_f');
        $this->dropColumn('certificates', 'rezerv_f');
    }
}
