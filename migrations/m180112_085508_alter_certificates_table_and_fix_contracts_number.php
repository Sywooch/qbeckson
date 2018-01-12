<?php

use app\models\Contracts;
use yii\db\Migration;

class m180112_085508_alter_certificates_table_and_fix_contracts_number extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('certificates', 'rezerv', $this->decimal(19, 2));

        $this->fixContractNumber();
    }

    public function safeDown()
    {
        $this->alterColumn('certificates', 'rezerv', $this->float());
    }

    private function fixContractNumber()
    {
        $contracts = \app\models\Contracts::find()->leftJoin([
            'doubleNumbers' =>
                \app\models\Organization::find()
                    ->select('contracts.number as number, count(contracts.number) as count, organization.id as organizationId')
                    ->leftJoin(\app\models\Contracts::tableName(), 'contracts.organization_id = organization.id')
                    ->groupBy(['number', 'organizationId'])
                    ->orderBy(['count' => 'desc'])],
            'doubleNumbers.number = contracts.number and doubleNumbers.organizationId = contracts.organization_id'
        )->where(['>', 'doubleNumbers.count', 1])
            ->andWhere(['>', 'contracts.created_at', '2018-01-01'])
            ->andWhere('contracts.parent_id is not null')
            ->all();

        foreach ($contracts as $contract) {
            $existNumberList = Contracts::find()
                ->select('number')
                ->where(['organization_id' => $contract->organization_id])
                ->andWhere(['<>', 'id', $contract->id])
                ->column();

            if (in_array($contract->number, $existNumberList)) {
                $i = 0;
                do {
                    $number = (count($existNumberList) + $i++) . ' - ПФ';
                } while (in_array($number, $existNumberList));

                $contract->number = $number;
                $contract->save();

                $contractRequest = new \app\models\contracts\ContractRequest();
                $mpdf = $contractRequest->makePdfForContract($contract);
                $mpdf->Output(Yii::getAlias('@pfdoroot/uploads/contracts/') . $contract->url, 'F');
            }
        }
    }
}
