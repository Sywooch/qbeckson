<?php

use yii\db\Migration;

/**
 * Handles the creation of table `module_address`.
 */
class m170731_134302_create_module_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('program_module_address', [
            'id' => $this->primaryKey(),
            'program_module_id' => $this->integer(11),
            'address' => $this->string(255),
            'lat' => $this->string(25),
            'lng' => $this->string(25),
            'status' => $this->integer(1)->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk-program_module_address-program_module_id-program_module-id',
            'program_module_address',
            'program_module_id',
            'years',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk-program_module_address-program_module_id-program_module-id',
            'program_module_address'
        );
        $this->dropTable('program_module_address');
    }
}
