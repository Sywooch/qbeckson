<?php

use yii\db\Migration;

class m171208_062603_invoice_have_contracts extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            '{{%invoice_have_contract}}',
            [
                'invoice_id' => $this->integer()->notNull(),
                'contract_id' => $this->integer()->notNull()
            ]
        );
        $this->addPrimaryKey(
            'pk_invoice_have_contract_invoice_id_contract_id',
            'invoice_have_contract',
            ['invoice_id', 'contract_id']
        );
        $this->addForeignKey(
            'fk_invoice_have_contract_invoice',
            '{{%invoice_have_contract}}',
            'invoice_id',
            '{{%invoices}}',
            'id'
        );
        $this->addForeignKey(
            'fk_invoice_have_contract_contract',
            '{{%invoice_have_contract}}',
            'contract_id',
            '{{%contracts}}',
            'id'
        );
        $this->buildLinks();
    }

    private function buildLinks()
    {
        $invoices = \app\models\Invoices::find()
            ->where(['not in', 'id', \app\models\InvoiceHaveContract::find()->select('invoice_id')])
            ->all();
        array_map(
            function (\app\models\Invoices $invoice) {
                $this->buildLink($invoice);
            },
            $invoices
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171208_062603_invoice_have_contracts cannot be reverted.\n";

        return false;
    }
    */

    private function buildLink(\app\models\Invoices $invoice)
    {
        $contractIds = explode(',', $invoice->contracts);
        $preparedIdsTrim = array_map('trim', $contractIds);
        $preparedIdsFiltred = array_filter($preparedIdsTrim, function ($id) {
            return $id != '';
        });
        $preparedIdsInts = array_map('intVal', $preparedIdsFiltred);
        $preparedIdsFiltredZero = array_filter($preparedIdsInts, function ($id) {
            return $id != 0;
        });
        $uniqIds = array_unique($preparedIdsFiltredZero);
        $invoiceId = $invoice->id;
        array_map(
            function (int $contractId) use ($invoiceId) {
                $invoiceHaveContract = new \app\models\InvoiceHaveContract(
                    ['contract_id' => $contractId, 'invoice_id' => $invoiceId]
                );
                $invoiceHaveContract->save()
                || (Yii::getLogger()
                        ->log(\app\helpers\ModelHelper::getFirstError($invoiceHaveContract), LOG_ERR)
                    && print_r(\app\helpers\ModelHelper::getFirstError($invoiceHaveContract)));
            },
            $uniqIds
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_invoice_have_contract_contract',
            '{{%invoice_have_contract}}'
        );
        $this->dropForeignKey(
            'fk_invoice_have_contract_invoice',
            '{{%invoice_have_contract}}'
        );
        $this->dropTable('{{%invoice_have_contract}}');
    }
}
