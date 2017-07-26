<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170726_102021_add_organization_settings_search_filters extends Migration
{
    public function safeUp()
    {
        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'number,date,rezerv,paid,start_edu_contract,stop_edu_contract,group_id,programMunicipality,childFullName,moduleName,certificateNumber,programName,payerName',
            'inaccessible_columns' => 'number,certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'active'
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'number,date,rezerv,paid,start_edu_contract,stop_edu_contract,group_id,programMunicipality,childFullName,moduleName,certificateNumber,programName,payerName',
            'inaccessible_columns' => 'number,certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'ends'
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'certificateNumber,childFullName,moduleName,programMunicipality,programName,payerName,start_edu_contract,stop_edu_contract',
            'inaccessible_columns' => 'certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'confirmed'
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'certificateNumber,programName,payerName,start_edu_contract,stop_edu_contract,status,programMunicipality',
            'inaccessible_columns' => 'certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'pending'
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'number,date,certificateNumber,programName,payerName,moduleName,date_termnate,programMunicipality,paid',
            'inaccessible_columns' => 'number,certificateNumber,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'dissolved'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'year,hours,directivity,zab,age_group_min,age_group_max,rating,limit',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'open'
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'year,hours,directivity,zab,age_group_min,age_group_max',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'wait'
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'year,hours,directivity,zab,age_group_min,age_group_max',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'close'
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'invoices',
            'table_columns' => 'number,date,month,payer,prepayment,status,link,sum',
            'inaccessible_columns' => 'number,payer',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'payers',
            'table_columns' => 'name,mun,phone,email,fio,directionality,certificates,cooperates',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'open',
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'payers',
            'table_columns' => 'name,mun,phone,email,fio,directionality,certificates,cooperates',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'wait',
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'groups',
            'table_columns' => 'schedule,datestart,datestop,studentsCount,requestsCount,placesCount',
            'inaccessible_columns' => 'name,programName,address',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]);
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_ORGANIZATION . '"
        ');

        return true;
    }
}
