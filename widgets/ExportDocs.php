<?php
namespace app\widgets;

use Yii;
use kartik\export\ExportMenu;

class ExportDocs extends ExportMenu
{
    public $initDownloadOnStart = false;

    public function init()
    {
        parent::init();
        if ($this->initDownloadOnStart === true) {
            $this->_triggerDownload = true;
        }
        if ($this->_triggerDownload) {
            if (!$this->_doNotStream) {
                Yii::$app->controller->layout = false;
            }
            $this->_exportType = self::FORMAT_EXCEL;
            $this->_columnSelectorEnabled = false;
            $this->initSelectedColumns();
        }
    }
}
