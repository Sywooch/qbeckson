<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `column_from_organization_payer_assignment`.
 */
class m170810_143117_drop_column_from_organization_payer_assignment_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('organization_payer_assignment', 'certificate_accounting_limit');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn('organization_payer_assignment', 'certificate_accounting_limit', $this->integer()->defaultValue(0));
    }
}
