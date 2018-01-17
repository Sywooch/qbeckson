<?php

use yii\db\Migration;

class m180111_121942_cert_rezerv_to_decimal extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            'certificates',
            'rezerv',
            $this->decimal(19, 2)->null()
        );
    }

    public function safeDown()
    {
        $this->alterColumn(
            'certificates',
            'rezerv',
            $this->float()->null()
        );
    }
}
