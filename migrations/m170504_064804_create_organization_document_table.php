<?php

use yii\db\Migration;

/**
 * Handles the creation of table `organization_document`.
 */
class m170504_064804_create_organization_document_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('organization_document', [
            'id' => $this->primaryKey(),
            'organization_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'filename' => $this->string()->notNull(),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex('oid', 'organization_document', 'organization_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('organization_document');
    }
}
