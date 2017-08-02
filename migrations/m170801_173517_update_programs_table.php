<?php

use yii\db\Migration;

class m170801_173517_update_programs_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('programs', 'photo_path', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn('programs', 'photo_base_url', 'VARCHAR(255) DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('programs', 'photo_path');
        $this->dropColumn('programs', 'photo_base_url');
    }
}
