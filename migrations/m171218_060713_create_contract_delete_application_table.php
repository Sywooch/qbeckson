<?php

use yii\db\Migration;

/**
 * Handles the creation of table `contract_delete_application`.
 * Has foreign keys to the tables:
 *
 * - `contracts`
 * - `user`
 */
class m171218_060713_create_contract_delete_application_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('contract_delete_application', [
            'id' => $this->primaryKey(),
            'reason' => $this->string(),
            'file' => $this->string(),
            'base_url' => $this->string(),
            'filename' => $this->string(),
            'created_at' => $this->datetime(),
            'confirmed_at' => $this->datetime(),
            'contract_id' => $this->integer(),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(1),
        ]);

        // creates index for column `contract_id`
        $this->createIndex(
            'idx-contract_delete_application-contract_id',
            'contract_delete_application',
            'contract_id'
        );

        // add foreign key for table `contracts`
        $this->addForeignKey(
            'fk-contract_delete_application-contract_id',
            'contract_delete_application',
            'contract_id',
            'contracts',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `contracts`
        $this->dropForeignKey(
            'fk-contract_delete_application-contract_id',
            'contract_delete_application'
        );

        // drops index for column `contract_id`
        $this->dropIndex(
            'idx-contract_delete_application-contract_id',
            'contract_delete_application'
        );

        $this->dropTable('contract_delete_application');
    }
}
