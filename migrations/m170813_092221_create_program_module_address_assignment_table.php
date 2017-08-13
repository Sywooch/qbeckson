<?php

use yii\db\Migration;

/**
 * Handles the creation of table `program_module_address_assignment`.
 */
class m170813_092221_create_program_module_address_assignment_table extends Migration
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
        $this->createTable('program_module_address_assignment', [
            'program_address_assignment_id' => $this->integer(11)->notNull(),
            'program_module_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(1)->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey(
            'fk-module_address-program_address_id-program_address-id',
            'program_module_address_assignment',
            'program_address_assignment_id',
            'program_address_assignment',
            'id'
        );
        $this->addForeignKey(
            'fk-module_address-module_id-years-id',
            'program_module_address_assignment',
            'program_module_id',
            'years',
            'id'
        );
        $this->createIndex(
            'idx-program_address_id-program_module_id',
            'program_module_address_assignment',
            ['program_address_assignment_id', 'program_module_id'],
            true
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        /*$this->dropIndex(
            'idx-program_address_id-program_module_id',
            'program_module_address_assignment'
        );*/
        $this->dropForeignKey(
            'fk-module_address-program_address_id-program_address-id',
            'program_module_address_assignment'
        );
        $this->dropForeignKey(
            'fk-module_address-module_id-years-id',
            'program_module_address_assignment'
        );
        $this->dropTable('program_module_address_assignment');
    }
}
