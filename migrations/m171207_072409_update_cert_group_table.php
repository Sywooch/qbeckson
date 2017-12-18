<?php

use yii\db\Migration;

class m171207_072409_update_cert_group_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('cert_group', 'nominals_limit', $this->integer()->after('amount')->defaultValue(0)->comment('Ограничение суммы номиналов'));
    }

    public function safeDown()
    {
        $this->dropColumn('cert_group', 'nominals_limit');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171207_072409_update_cert_group_table cannot be reverted.\n";

        return false;
    }
    */
}
