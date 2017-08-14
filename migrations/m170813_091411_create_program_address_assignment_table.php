<?php

use yii\db\Migration;

/**
 * Handles the creation of table `program_address_assignment`.
 */
class m170813_091411_create_program_address_assignment_table extends Migration
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

        $this->createTable('program_address_assignment', [
            'id' => $this->primaryKey(),
            'organization_address_id' => $this->integer(11)->notNull(),
            'program_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(1)->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-program_address-address_id-organization_address-id',
            'program_address_assignment',
            'organization_address_id',
            'organization_address',
            'id'
        );

        $this->addForeignKey(
            'fk-program_address-program_id-programs-id',
            'program_address_assignment',
            'program_id',
            'programs',
            'id'
        );

        $this->createIndex(
            'idx-organization_address_id-program_id',
            'program_address_assignment',
            ['organization_address_id', 'program_id'],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
/*        $this->dropIndex(
            'idx-organization_address_id-program_id',
            'program_address_assignment'
        );*/
        $this->dropForeignKey(
            'fk-program_address-address_id-organization_address-id',
            'program_address_assignment'
        );
        $this->dropForeignKey(
            'fk-program_address-program_id-programs-id',
            'program_address_assignment'
        );
        $this->dropTable('program_address_assignment');
    }
}
