<?php

use yii\db\Migration;

/**
 * Handles the creation of table `key_storage_item`.
 */
class m170809_080002_create_key_storage_item_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('key_storage_item', [
            'id' => $this->primaryKey(),
            'operator_id' => $this->integer(11)->notNull(),
            'type' => $this->string(128)->notNull(),
            'key' => $this->string(128)->notNull(),
            'value' => $this->text()->notNull(),
            'comment' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('key_storage_item');
    }
}
