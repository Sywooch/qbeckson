<?php

use yii\db\Migration;

class m170306_112613_update_auth_item_child extends Migration
{
    public function up()
    {
        $this->update("{{%auth_item_child}}", ['child' => 'certificates/view'], [
            'parent' => 'operators',
            'child' => 'certificates',
        ]);
    }

    public function down()
    {
        $this->update("{{%auth_item_child}}", ['child' => 'certificates'], [
            'parent' => 'operators',
            'child' => 'certificates/view',
        ]);
    }
}
