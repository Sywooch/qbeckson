<?php

use yii\db\Migration;

class m170927_111128_certFreez extends Migration
{
    public function safeUp()
    {
        $this->addColumn('certificates', 'friezed_at', $this->date()->null());
        $this->addColumn('certificates', 'friezed_ballance', $this->money(19, 2)->null());
        $this->createIndex('idx_certificates_friezed_at', 'certificates', 'friezed_at');
    }

    public function safeDown()
    {
        $this->dropColumn('certificates', 'friezed_at');
        $this->dropColumn('certificates', 'friezed_ballance');
    }
}
