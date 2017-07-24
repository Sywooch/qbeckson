<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170724_063021_add_operator_settings_search_filters extends Migration
{
    /**
     *
     */
    public function safeUp()
    {
        $this->insert('settings_search_filters', [
            'table_name' => 'certificates',
            'table_columns' => 'number,fio_child,nominal,rezerv,balance,contractCount,actual,payer',
            'inaccessible_columns' => 'number,name,soname,phname,payerMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'year,hours,directivity,zab,age_group_min,age_group_max,rating,limit,organization',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'open'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'year,hours,directivity,zab,age_group_min,age_group_max,organization',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'wait'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'year,hours,directivity,organization,zab,age_group_min,age_group_max',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'close'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'number,date,rezerv,paid,start_edu_contract,stop_edu_contract,group_id,programMunicipality,childFullName,moduleName,certificateNumber,programName,organizationName,payerName',
            'inaccessible_columns' => 'number,certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'active'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'certificateNumber,childFullName,moduleName,programMunicipality,programName,payerName,start_edu_contract,stop_edu_contract',
            'inaccessible_columns' => 'certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'confirmed'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'certificateNumber,programName,organizationName,payerName,start_edu_contract,stop_edu_contract,status,programMunicipality',
            'inaccessible_columns' => 'certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'pending'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'number,date,certificateNumber,programName,payerName,moduleName,date_termnate,programMunicipality,paid',
            'inaccessible_columns' => 'number,certificateNumber,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'dissolved'
        ]);
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_OPERATOR . '" AND table_name = "certificates"
        ');

        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_OPERATOR . '" AND table_name = "programs"
        ');

        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_OPERATOR . '" AND table_name = "contracts"
        ');

        return true;
    }
}
