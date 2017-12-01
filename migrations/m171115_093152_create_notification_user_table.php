<?php

use yii\db\Migration;

/**
 * Handles the creation of table `notification_user`.
 */
class m171115_093152_create_notification_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('notification_user', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->comment('id пользователя'),
            'notification_id' => $this->integer(11)->notNull()->comment('id уведомления'),
        ], 'COMMENT "назначение уведомлений пользователям"');

        $this->addForeignKey('fk_notification_user-user', 'notification_user', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_notification_user-notification', 'notification_user', 'notification_id', 'notification', 'id', 'CASCADE', 'CASCADE');

        $this->addColumn('notification', 'delete_after_show', $this->boolean()->defaultValue(1)->comment('удалять ли уведомление после отображения'));
        $this->addColumn('notification', 'type', $this->string(20)->defaultValue(null)->comment('тип уведомления'));
        $this->alterColumn('notification', 'user_id', $this->integer(11)->comment('идентификатор пользователя создавшего уведомление'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->alterColumn('notification', 'user_id', $this->integer(11)->comment('идентификатор пользователя'));
        $this->dropColumn('notification', 'type');
        $this->dropColumn('notification', 'delete_after_show');

        $this->dropTable('notification_user');
    }
}
