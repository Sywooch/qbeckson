<?php

use yii\db\Migration;

class m170802_150650_clear_user_search_filters_assignment extends Migration
{
    public function safeUp()
    {
        $this->execute('DELETE FROM user_search_filters_assignment');
    }

    public function safeDown()
    {
        return true;
    }
}
