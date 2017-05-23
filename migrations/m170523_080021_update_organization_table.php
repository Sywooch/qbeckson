<?php

use yii\db\Migration;

class m170523_080021_update_organization_table extends Migration
{
    public function up()
    {
        $this->alterColumn('organization', 'anonymous_update_token', $this->string());
    }

    public function down()
    {
        echo "m170523_080021_update_organization_table cannot be reverted.\n";

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
