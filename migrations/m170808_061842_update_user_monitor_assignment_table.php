<?php

use yii\db\Migration;

class m170808_061842_update_user_monitor_assignment_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user_monitor_assignment', 'access_rights', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('user_monitor_assignment', 'access_rights');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170808_061842_update_user_monitor_assignment_table cannot be reverted.\n";

        return false;
    }
    */
}
