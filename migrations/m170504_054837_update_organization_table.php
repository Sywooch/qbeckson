<?php

use yii\db\Migration;

class m170504_054837_update_organization_table extends Migration
{
    public function up()
    {
        $this->addColumn('organization', 'status', $this->integer() . ' DEFAULT 20');
        $this->addColumn('organization', 'refuse_reason', $this->text());
        $this->addColumn('organization', 'organizational_form', $this->text());
        $this->addColumn('organization', 'accepted_date', $this->integer());

        $this->update('organization', ['status' => 20]);
    }

    public function down()
    {
        echo "m170504_054837_update_organization_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
