<?php

use yii\db\Migration;

/**
 * Handles the creation of table `contract_auto_prolonged_log`.
 */
class m180105_151518_create_contract_auto_prolonged_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('contract_auto_prolonged_log', [
            'id' => $this->primaryKey(),
            'organization_id' => $this->integer(11)->notNull()->comment('id организации проводившей автопролонгацию'),
            'contract_parent_id' => $this->integer(11)->notNull()->comment('id родительского контракта'),
            'contract_child_id' => $this->integer(11)->notNull()->comment('id дочернего контракта'),
            'group_id' => $this->integer(11)->defaultValue(null)->comment('если автопролонгация проходила в новую группу id группы, иначе null'),
            'auto_prolonged_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('дата и время автопролонгации контракта'),
        ], 'comment "лог автопролонгированных контрактов"');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('contract_auto_prolonged_log');
    }
}
