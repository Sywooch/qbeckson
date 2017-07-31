<?php

use yii\db\Migration;

/**
 * Handles the creation of table `group_trainee`.
 */
class m170731_143133_create_group_trainee_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('group_class', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer(11),
            'address_id' => $this->integer(11),
            'classroom' => $this->integer(5),
            'week_day' => $this->string(20),
            'hours_count' => $this->integer(20),
            'time_from' => $this->string(10),
            'time_to' => $this->string(10),
        ]);

        $this->addForeignKey(
            'fk-group_class-address_id-program_module_address-id',
            'group_class',
            'address_id',
            'program_module_address',
            'id'
        );

        $this->addForeignKey(
            'fk-group_class-group_id-groups-id',
            'group_class',
            'group_id',
            'groups',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk-group_class-address_id-program_module_address-id',
            'group_class'
        );
        $this->dropForeignKey(
            'fk-group_class-group_id-groups-id',
            'group_class'
        );
        $this->dropTable('group_class');
    }
}
