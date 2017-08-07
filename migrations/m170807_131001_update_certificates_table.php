<?php

use yii\db\Migration;

class m170807_131001_update_certificates_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('certificates', 'updated_cert_group', $this->integer()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('certificates', 'updated_cert_group');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170807_131001_update_certificate_table cannot be reverted.\n";

        return false;
    }
    */
}
