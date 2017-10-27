<?php

use yii\db\Migration;

class m171023_130141_createUserPersonalAssign extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_personal_assign', [
            'id' => $this->primaryKey(11),
            'user_id' => $this->integer(11)->notNull()->comment('id пользователя'),
            'assign_user_id' => $this->integer(11)->notNull()->comment('id связанного пользователя'),
        ], 'COMMENT "связь личных кабинетов пользователей"');
        
        $this->addForeignKey('fk-user_personal_assign-user', 'user_personal_assign', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-user_personal_assign-assign-user', 'user_personal_assign', 'assign_user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('user_personal_assign');
    }
}
