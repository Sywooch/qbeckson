<?php

use yii\db\Migration;

/**
 * Handles the creation of table `organization_operator_assignment`.
 */
class m170629_112103_create_organization_operator_assignment_table extends Migration
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

        $this->createTable('organization_operator_assignment', [
            'organization_id' => $this->integer()->notNull(),
            'operator_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-organization-operator', 'organization_operator_assignment', ['organization_id', 'operator_id']
        );

        $this->addForeignKey('fk-organization-operator-organization', 'organization_operator_assignment', 'organization_id', 'organization', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk-organization-operator-operator', 'organization_operator_assignment', 'operator_id', 'operators', 'id', 'cascade', 'cascade');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('organization_operator_assignment');
    }
}
