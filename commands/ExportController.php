<?php

namespace app\commands;

use yii;
use yii\console\Controller;
use app\helpers\GridviewHelper;
use app\models\ExportFile;
use app\widgets\ExportDocs;

class ExportController extends Controller
{
    public function actionMake($exportId)
    {
        if (null === ($doc = ExportFile::findOne($exportId))) {
            return false;
        }

        ExportDocs::widget([
            'dataProvider' => $doc->data_provider,
            'target' => ExportDocs::TARGET_SELF,
            'showColumnSelector' => false,
            'filename' => $doc->file,
            'initDownloadOnStart' => true,
            'stream' => false,
            'deleteAfterSave' => false,
            'folder' => '@pfdoroot/uploads/' . $doc->table,
            'linkPath' => '@pfdo/uploads/' . $doc->table,
            'showConfirmAlert' => false,
            'afterSaveView' => false,
            'exportConfig' => [
                ExportDocs::FORMAT_TEXT => false,
                ExportDocs::FORMAT_CSV => false,
                ExportDocs::FORMAT_HTML => false,
                ExportDocs::FORMAT_PDF => false,
                ExportDocs::FORMAT_EXCEL_X => false,
            ],
            'columns' => $doc->columns,
        ]);

        if ($doc->setReady()) {
            echo "done.";

            return Controller::EXIT_CODE_NORMAL;
        }

        return Controller::EXIT_CODE_ERROR;
    }
}
