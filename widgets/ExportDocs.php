<?php
namespace app\widgets;

use Yii;
use kartik\export\ExportMenu;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
            $this->_exportType = self::FORMAT_EXCEL_X;
            $this->_columnSelectorEnabled = false;
            $this->initSelectedColumns();
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->initI18N(__DIR__);
        $this->initColumnSelector();
        $this->setVisibleColumns();
        $this->initExport();
        if (!$this->_triggerDownload) {
            $this->registerAssets();
            echo $this->renderExportMenu();
            return;
        }
        if ($this->timeout >= 0) {
            set_time_limit($this->timeout);
        }
        if (!$this->_doNotStream) {
            $this->clearOutputBuffers();
        }
        $config = ArrayHelper::getValue($this->exportConfig, $this->_exportType, []);
        if ($this->_exportType === self::FORMAT_PDF) {
            $path = Yii::getAlias($this->pdfLibraryPath);
            if (!\PHPExcel_Settings::setPdfRenderer($this->pdfLibrary, $path)) {
                throw new InvalidConfigException("The pdf rendering library '{$this->pdfLibrary}' was not found or installed at path '{$path}'.");
            }
        }
        if (empty($config['writer'])) {
            throw new InvalidConfigException("The 'writer' setting for PHPExcel must be setup in 'exportConfig'.");
        }
        $this->initPHPExcel();
        $this->initPHPExcelWriter($config['writer']);
        $this->initPHPExcelSheet();
        $this->generateBeforeContent();
        $this->generateHeader();
        $this->generateBody();
        $row = $this->generateFooter();
        $this->generateAfterContent($row);
        $writer = $this->_objPHPExcelWriter;
        $sheet = $this->_objPHPExcelSheet;
        if ($this->autoWidth) {
            foreach ($this->getVisibleColumns() as $n => $column) {
                $sheet->getColumnDimension(self::columnName($n + 1))->setAutoSize(true);
            }
        }
        $this->raiseEvent('onRenderSheet', [$sheet, $this]);
        if (!$this->stream) {
            $this->folder = trim(Yii::getAlias($this->folder));
            if (!file_exists($this->folder)) {
                $this->folder = Yii::getAlias('@webroot');
            }
            $file = self::slash($this->folder) . $this->filename . '.' . $config['extension'];
            $writer->save($file);
            if ($this->streamAfterSave) {
                $this->clearOutputBuffers();
                $this->setHttpHeaders();
                readfile($file);
                if ($this->deleteAfterSave) {
                    @unlink($file);
                }
                $this->destroyPHPExcel();
                exit();
            } else {
                if ($this->_triggerDownload && $this->_doNotStream && $this->afterSaveView !== false) {
                    $this->registerAssets();
                    echo $this->renderExportMenu();
                    $config = ArrayHelper::getValue($this->exportConfig, $this->_exportType, []);
                    if (!empty($config)) {
                        $file = $this->filename . '.' . $config['extension'];
                        echo $this->render($this->afterSaveView, [
                            'file' => $file,
                            'icon' => ($this->fontAwesome ? 'fa fa-' : 'glyphicon glyphicon-') . $config['icon'],
                            'href' => Url::to([self::slash($this->linkPath, '/') . $file]),
                        ]);
                    }
                }
            }
            if ($this->deleteAfterSave) {
                @unlink($file);
            }
        } else {
            $this->clearOutputBuffers();
            $this->setHttpHeaders();
            $writer->save('php://output');
            $this->destroyPHPExcel();
            exit();
        }
    }
}
