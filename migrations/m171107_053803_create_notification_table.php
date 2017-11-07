<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification`.
 */
class m171107_053803_create_notification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('notification', [
            'id' => $this->primaryKey(11),
            'user_id' => $this->integer(11)->comment('идентификатор пользователя'),
            'message' => $this->text()->comment('текст уведомления'),
        ], 'COMMENT "уведомление пользователя"');

        $this->addForeignKey('fk_notification_user', 'notification', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('notification');
    }
}
