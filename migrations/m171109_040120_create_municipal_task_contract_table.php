<?php

use yii\db\Migration;

/**
 * Handles the creation of table `municipal_task_contract`.
 */
class m171109_040120_create_municipal_task_contract_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('municipal_task_contract', [
            'id' => $this->primaryKey(),
            'certificate_id' => $this->integer(),
            'payer_id' => $this->integer(),
            'program_id' => $this->integer(),
            'organization_id' => $this->integer(),
            'group_id' => $this->integer(),
            'status' => $this->integer()->defaultValue(10),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex('idx_municipal_task_contract_certificate_id', '{{%municipal_task_contract}}', 'certificate_id');
        $this->createIndex('idx_municipal_task_contract_payer_id', '{{%municipal_task_contract}}', 'payer_id');
        $this->createIndex('idx_municipal_task_contract_program_id', '{{%municipal_task_contract}}', 'program_id');
        $this->createIndex('idx_municipal_task_contract_organization_id', '{{%municipal_task_contract}}', 'organization_id');
        $this->createIndex('idx_municipal_task_contract_group_id', '{{%municipal_task_contract}}', 'group_id');
        $this->addForeignKey(
            'fk_municipal_task_contract_certificate_id',
            '{{%municipal_task_contract}}',
            'certificate_id',
            '{{%certificates}}',
            'id'
        );
        $this->addForeignKey(
            'fk_municipal_task_contract_payer_id',
            '{{%municipal_task_contract}}',
            'payer_id',
            '{{%payers}}',
            'id'
        );
        $this->addForeignKey(
            'fk_municipal_task_contract_organization_id',
            '{{%municipal_task_contract}}',
            'organization_id',
            '{{%organization}}',
            'id'
        );
        $this->addForeignKey(
            'fk_municipal_task_contract_program_id',
            '{{%municipal_task_contract}}',
            'program_id',
            '{{%programs}}',
            'id'
        );
        $this->addForeignKey(
            'fk_municipal_task_contract_group_id',
            '{{%municipal_task_contract}}',
            'group_id',
            '{{%groups}}',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('municipal_task_contract');
    }
}
