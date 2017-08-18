<?php

use yii\db\Migration;

/**
 * Handles the creation of table `organization_address`.
 */
class m170813_090227_create_organization_address_table extends Migration
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

        $this->createTable('organization_address', [
            'id' => $this->primaryKey(),
            'organization_id' => $this->integer(11)->notNull(),
            'address' => $this->string(255)->notNull(),

            'lat' => $this->string(25)->notNull(),
            'lng' => $this->string(25)->notNull(),

            'status' => $this->integer(1)->defaultValue(0),
        ], $tableOptions);

        $this->createIndex(
            'idx-address',
            'organization_address',
            ['organization_id', 'address'],
            true
        );
        $this->addForeignKey(
            'fk-organization_address-organization_id-organization-id',
            'organization_address',
            'organization_id',
            'organization',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        //$this->dropIndex('idx-address', 'organization_address');
        $this->dropForeignKey('fk-organization_address-organization_id-organization-id', 'organization_address');
        $this->dropTable('organization_address');
    }
}
