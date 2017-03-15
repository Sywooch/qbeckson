<?php

use yii\db\Migration;

class m170315_114806_update_auth_item_child extends Migration
{
    public function up()
    {
        $this->insert("{{%auth_item}}", [
            'name' => 'rbac-access',
            'type' => 2,
            'description' => 'Управление ролями',
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert("{{%auth_item_child}}", [
            'parent' => 'admins',
            'child' => 'rbac-access',
        ]);
    }

    public function down()
    {
        $this->delete("{{%auth_item_child}}", [
            'parent' => 'admins',
            'child' => 'rbac-access',
        ]);
    }
}
