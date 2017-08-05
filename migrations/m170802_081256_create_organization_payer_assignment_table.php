<?php

use yii\db\Migration;

/**
 * Handles the creation of table `organization_payer_assignment`.
 */
class m170802_081256_create_organization_payer_assignment_table extends Migration
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

        $this->createTable('organization_payer_assignment', [
            'organization_id' => $this->integer()->notNull(),
            'payer_id' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(20),
        ], $tableOptions);

        $this->addPrimaryKey('pk-organization-payer', 'organization_payer_assignment', ['organization_id', 'payer_id']
        );

        $this->addForeignKey('fk-organization-payer-organization', 'organization_payer_assignment', 'organization_id', 'organization', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk-organization-payer-payer', 'organization_payer_assignment', 'payer_id', 'payers', 'id', 'cascade', 'cascade');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('organization_payer_assignment');
    }
}
