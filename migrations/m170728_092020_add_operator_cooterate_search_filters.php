<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170728_092020_add_operator_cooterate_search_filters extends Migration
{
    public function safeUp()
    {
        $this->insert('settings_search_filters', [
            'table_name' => 'cooperate',
            'table_columns' => 'number,date,organizationName,payerName,payerMunicipality,contractsCount',
            'inaccessible_columns' => 'number,payerMunicipality,organizationName',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
        ]);
    }

    public function safeDown()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE table_name = "cooperate" AND role = "' . UserIdentity::ROLE_OPERATOR . '"
        ');

        return true;
    }
}
