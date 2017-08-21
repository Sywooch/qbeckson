<?php

use yii\db\Migration;

/**
 * Handles the creation of table `organization_contract_settings`.
 */
class m170817_072541_create_organization_contract_settings_table extends Migration
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
        $this->createTable('organization_contract_settings', [
            'id' => $this->primaryKey(),
            'organization_id' => $this->integer(11)->notNull(),
            'organization_first_ending' => $this->string(10),
            'organization_second_ending' => $this->string(10),
            'director_name_ending' => $this->string(10),
            'document_type' => $this->string(20),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('organization_contract_settings');
    }
}
