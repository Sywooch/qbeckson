<?php

use yii\db\Migration;

class m170529_133221_update_organization_document_table extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE `organization_document` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
    }

    public function down()
    {
        echo "m170529_133221_update_organization_document_table cannot be reverted.\n";

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
