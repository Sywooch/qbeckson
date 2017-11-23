<?php

use yii\db\Migration;

class m171109_055610_alter_organization_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('organization', 'correspondent_invoice', $this->string(20)->after('korr_invoice')->defaultValue(null)->comment('корреспондентский счёт'));
    }

    public function safeDown()
    {
        $this->dropColumn('organization', 'correspondent_invoice');
    }
}
