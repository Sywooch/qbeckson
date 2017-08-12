<?php

use yii\db\Migration;

class m170728_122934_insert_role extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $monitor = $auth->createRole('monitor');
        $auth->add($monitor);
    }

    public function safeDown()
    {
        echo "m170728_122934_insert_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170728_122934_insert_role cannot be reverted.\n";

        return false;
    }
    */
}
