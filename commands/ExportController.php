<?php

namespace app\commands;

use app\helpers\GridviewHelper;
use app\models\ContractsPayerInvoiceSearch;
use app\widgets\ExportDocs;
use yii;
use yii\console\Controller;

class ExportController extends Controller
{
    public function actionMake($exportId)
    {
        $searchLastMonthContracts = new ContractsPayerInvoiceSearch([
            'payer_id' => 51,
        ]);
        $dataProvider = $searchLastMonthContracts->search(null);

        ExportDocs::widget([
            'dataProvider' => $dataProvider,
            'target' => ExportDocs::TARGET_SELF,
            'showColumnSelector' => false,
            'filename' => GridviewHelper::getFileName($group),
            'initDownloadOnStart' => true,
            'stream' => false,
            'deleteAfterSave' => false,
            'folder' => '@pfdoroot/uploads/' . $table,
            'linkPath' => '@pfdo/uploads/' . $table,
            'showConfirmAlert' => false,
            'afterSaveView' => false,
            'exportConfig' => [
                ExportDocs::FORMAT_TEXT => false,
                ExportDocs::FORMAT_CSV => false,
                ExportDocs::FORMAT_HTML => false,
                ExportDocs::FORMAT_PDF => false,
                ExportDocs::FORMAT_EXCEL_X => false,
            ],
            'columns' => GridviewHelper::prepareColumns($table, $columns, empty($type) ? null : $type, 'export'),
        ]);

        echo "done.";

        return 0;
    }
}
