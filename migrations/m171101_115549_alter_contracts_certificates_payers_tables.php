<?php

use yii\db\Migration;

class m171101_115549_alter_contracts_certificates_payers_tables extends Migration
{
    public function safeUp()
    {
        $this->addColumn('payers', 'days_to_first_contract_request', $this->integer(11)->unsigned()->notNull()->defaultValue(5)->comment('кол-во дней для создания первой заявки после создания сертификата или перевода в тип ПФ'));
        $this->addColumn('payers', 'days_to_contract_request_after_refused', $this->integer(11)->unsigned()->notNull()->defaultValue(10)->comment('кол-во дней для создания новой заявки после отклонения предыдущей'));

        $this->addColumn('certificates', 'created_at', $this->dateTime()->defaultValue(null)->comment('дата и время создания сертификата'));
        $this->addColumn('certificates', 'type_changed_at', $this->dateTime()->defaultValue(null)->comment('дата и время изменения типа сертификата'));

        $this->addColumn('contracts', 'created_at', $this->dateTime()->defaultValue(null)->comment('дата и время создания договора'));
        $this->addColumn('contracts', 'requested_at', $this->dateTime()->defaultValue(null)->comment('дата и время создания заявки'));
        $this->addColumn('contracts', 'refused_at', $this->dateTime()->defaultValue(null)->comment('дата и время отклонения заявки'));
        $this->addColumn('contracts', 'accepted_at', $this->dateTime()->defaultValue(null)->comment('дата и время подтверждения заявки'));
        $this->addColumn('contracts', 'activated_at', $this->dateTime()->defaultValue(null)->comment('дата и время заключения договора'));
        $this->renameColumn('contracts', 'date_initiate_termination', 'termination_initiated_at');
        $this->alterColumn('contracts', 'termination_initiated_at', $this->dateTime()->after('activated_at')->defaultValue(null)->comment('дата и время перевода в статус ожидания расторжения (wait_termnate = 1)'));
    }

    public function safeDown()
    {
        $this->alterColumn('contracts', 'termination_initiated_at', $this->date());
        $this->renameColumn('contracts', 'termination_initiated_at', 'date_initiate_termination');
        $this->dropColumn('contracts', 'activated_at');
        $this->dropColumn('contracts', 'accepted_at');
        $this->dropColumn('contracts', 'refused_at');
        $this->dropColumn('contracts', 'requested_at');
        $this->dropColumn('contracts', 'created_at');

        $this->dropColumn('certificates', 'type_changed_at');
        $this->dropColumn('certificates', 'created_at');

        $this->dropColumn('payers', 'days_to_contract_request_after_refused');
        $this->dropColumn('payers', 'days_to_first_contract_request');
    }
}
