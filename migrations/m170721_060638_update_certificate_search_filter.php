<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170721_060638_update_certificate_search_filter extends Migration
{
    public function safeUp()
    {
        $this->execute('
            UPDATE settings_search_filters 
            SET inaccessible_columns = "number, name, soname, phname" 
            WHERE table_name = "certificates" AND role = "' . UserIdentity::ROLE_PAYER . '"
        ');
    }

    public function safeDown()
    {
        return true;
    }
}
