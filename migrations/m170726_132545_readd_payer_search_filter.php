<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170726_132545_readd_payer_search_filter extends Migration
{
    public function safeUp()
    {
        $this->execute('DELETE FROM settings_search_filters WHERE table_name = "certificates" AND role = "' . UserIdentity::ROLE_PAYER . '"');
        $this->insert('settings_search_filters', [
            'table_name' => 'certificates',
            'table_columns' => 'number, fio_child, nominal, rezerv, balance, contractCount, cert_group, actual',
            'inaccessible_columns' => 'number, name, soname, phname',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
        ]);
    }

    public function safeDown()
    {
        return true;
    }
}
