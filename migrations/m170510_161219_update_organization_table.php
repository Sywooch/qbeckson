<?php

use yii\db\Migration;

class m170510_161219_update_organization_table extends Migration
{
    public function up()
    {
        $this->addColumn('organization', 'anonymous_update_token', $this->string());
    }

    public function down()
    {
        $this->dropColumn('organization', 'anonymous_update_token');
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
