<?php

use yii\db\Migration;

class m170526_084118_update_organization_document_table extends Migration
{
    public function up()
    {
        $this-$this->addColumn('organization_document', 'base_url', 'varchar(50) DEFAULT NULL');
        $this-$this->addColumn('organization_document', 'path', 'varchar(50) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('organization_document', 'base_url');
        $this->dropColumn('organization_document', 'path');
    }
}
