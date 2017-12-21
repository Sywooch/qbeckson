<?php

use yii\db\Migration;

class m171211_220219_alter_payers_notification_tables extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('payers', 'certificate_can_create_contract', $this->boolean()->after('certificate_can_use_future_balance')->defaultValue(1)->comment('разрешено ли сертификату создавать договор на текущий период'));
        $this->renameColumn('payers', 'certificate_can_create_contract', 'certificate_can_use_current_balance');

        $this->alterColumn('payers', 'certificate_cant_create_contract_at', $this->dateTime()->after('certificate_can_use_current_balance')->defaultValue(null)->comment('сертификату запрещается создавать договор на текущий период с указанной даты и времени'));
        $this->renameColumn('payers', 'certificate_cant_create_contract_at', 'certificate_cant_use_current_balance_at');

        $this->alterColumn('notification', 'type', $this->string(255)->defaultValue(null)->comment('тип уведомления'));
    }

    public function safeDown()
    {
        $this->alterColumn('notification', 'type', $this->string(20)->defaultValue(null)->comment('тип уведомления'));

        $this->renameColumn('payers', 'certificate_cant_use_current_balance_at', 'certificate_cant_create_contract_at');
        $this->alterColumn('payers', 'certificate_cant_create_contract_at', $this->dateTime()->after('certificate_can_use_current_balance')->defaultValue(null)->comment('сертификату запрещается создавать контракт с указанной даты и времени'));

        $this->renameColumn('payers', 'certificate_can_use_current_balance', 'certificate_can_create_contract');
        $this->alterColumn('payers', 'certificate_can_create_contract', $this->boolean()->after('certificate_can_use_future_balance')->defaultValue(1)->comment('разрешено ли сертификату создавать контракт'));

    }
}
