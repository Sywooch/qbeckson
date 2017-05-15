<?php

use yii\db\Migration;

/**
 * Handles the creation of table `directory_organization_form`.
 */
class m170515_103509_create_directory_organization_form_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('directory_organization_form', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'is_separator' => $this->boolean(),
            'is_active' => $this->boolean(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('directory_organization_form');
    }
}
