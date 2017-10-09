<?php

namespace app\commands;

use app\models\ContractsPayerInvoiceSearch;
use app\widgets\ExportDocs;
use yii;
use yii\console\Controller;

class ExportController extends Controller
{
    public function actionMake()
    {
        $searchLastMonthContracts = new ContractsPayerInvoiceSearch([
            'payer_id' => 51,
        ]);
        $invoiceLastMonthProvider = $searchLastMonthContracts->search(null);
        ExportDocs::widget([
            'dataProvider' => $invoiceLastMonthProvider,
            'target' => ExportDocs::TARGET_SELF,
            'initDownloadOnStart' => true,
            'showColumnSelector' => false,
            'filename' => '_TEST_TEST_TEST_51',
            'stream' => false,
            'deleteAfterSave' => false,
            'folder' => '@pfdoroot/uploads/contracts',
            'linkPath' => '@pfdo/uploads/contracts',
            'dropdownOptions' => [
                'class' => 'btn btn-success',
                'label' => 'Заказать реестр договоров для субсидии',
                'icon' => false,
            ],
            'showConfirmAlert' => false,
            'afterSaveView' => false,
            'exportConfig' => [
                ExportDocs::FORMAT_TEXT => false,
                ExportDocs::FORMAT_CSV => false,
                ExportDocs::FORMAT_HTML => false,
                ExportDocs::FORMAT_PDF => false,
                ExportDocs::FORMAT_EXCEL_X => false,
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'number',
                    'label' => 'Реквизиты договора об обучении (твердой оферты)',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '№' . $model->number . ' от ' . Yii::$app->formatter->asDate($model->date);
                    }
                ],
                [
                    'label' => 'Объем обязательств Уполномоченной организации за текущий месяц в соответствии с договорами об обучении (твердыми офертами)',
                    'value' => function ($model) {

                        $start_edu_contract = explode("-", $model->start_edu_contract);
                        $month = $start_edu_contract[1];

                        if ($month == date('m')) {
                            $price = $model->payer_first_month_payment;
                        } else {
                            $price = $model->payer_other_month_payment;
                        }

                        return $price;
                    }
                ],
            ],
        ]);

        echo "done.";

        return 0;
    }
}
