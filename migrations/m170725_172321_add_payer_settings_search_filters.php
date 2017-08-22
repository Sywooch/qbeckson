<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170725_172321_add_payer_settings_search_filters extends Migration
{
    /**
     *
     */
    public function safeUp()
    {
        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'form,name,year,hours,directivity,zab,age_group_min,age_group_max,rating,limit',
            'inaccessible_columns' => 'name,mun,organization,organization_id',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'number,date,rezerv,paid,start_edu_contract,stop_edu_contract,group_id,programMunicipality,childFullName,moduleName,certificateNumber,programName,organizationName,payerName',
            'inaccessible_columns' => 'payer_id,number,certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'active'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'certificateNumber,childFullName,moduleName,programMunicipality,programName,start_edu_contract,stop_edu_contract',
            'inaccessible_columns' => 'certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'confirmed'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'certificateNumber,programName,organizationName,start_edu_contract,stop_edu_contract,status,programMunicipality',
            'inaccessible_columns' => 'certificateNumber,childFullName,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'pending'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'contracts',
            'table_columns' =>
                'number,date,certificateNumber,programName,moduleName,date_termnate,programMunicipality,paid',
            'inaccessible_columns' => 'number,certificateNumber,programMunicipality',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'dissolved'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'organization',
            'table_columns' => 'name,fio_contact,cratedate,site,phone,max_child,raiting,type,mun,programs,children,amount_child,actual',
            'inaccessible_columns' => 'name,fio_contact,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'register'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'organization',
            'table_columns' => 'name,fio_contact,site,email,mun,type',
            'inaccessible_columns' => 'name,mun,fio_contact',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'request'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'invoices',
            'table_columns' => 'number,date,month,organization,prepayment,status,link,sum',
            'inaccessible_columns' => 'number,organization',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_PAYER,
        ]);
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_PAYER . '"
        ');

        return true;
    }
}
