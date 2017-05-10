<?php

use yii\db\Migration;

class m170510_161219_update_organization_table extends Migration
{
    public function up()
    {
        $this->addColumn('organization', 'anonymous_update_token', $this->integer());
    }

    public function down()
    {
        echo "m170510_161219_update_organization_table cannot be reverted.\n";

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
