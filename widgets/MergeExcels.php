<?php
namespace app\widgets;

use yii;
use yii\base\Widget;

/**
 * Class MergeExcels
 * @package app\widgets
 */
class MergeExcels extends Widget
{
    public $fileName;

    public $provider;

    /**
     * @return string
     */
    public function run()
    {
        $filenames = [
            Yii::getAlias('@pfdoroot/uploads/contracts/') . '_PART1_' . $this->fileName . '.xls' => \Yii::t('app', '{0, date, LLLL}', strtotime("first day of this month"), 10),
            Yii::getAlias('@pfdoroot/uploads/contracts/') . '_PART2_' . $this->fileName . '.xls' => \Yii::t('app', '{0, date, LLLL}', strtotime("first day of previous month"), 10),
        ];
        $bigExcel = new \PHPExcel();
        $bigExcel->removeSheetByIndex(0);
        $reader = \PHPExcel_IOFactory::createReader('Excel5');
        foreach ($filenames as $filename => $title) {
            $excel = $reader->load($filename);

            foreach ($excel->getAllSheets() as $sheet) {
                $sheet->setTitle($title);
                $bigExcel->addExternalSheet($sheet);
                break;
            }

            foreach ($excel->getNamedRanges() as $namedRange) {
                $bigExcel->addNamedRange($namedRange);
            }
        }

        $writer = \PHPExcel_IOFactory::createWriter($bigExcel, 'Excel5');
        $writer->save(Yii::getAlias('@pfdoroot/uploads/contracts/') . $this->fileName . '.xls');
        foreach ($filenames as $filename => $title) {
            unlink($filename);
        }

        \app\models\ContractDocument::createInstance($this->fileName . '.xls', $this->provider->models);

        $this->view->context->redirect(['personal/payer-contracts', '#' => 'excel-download']);
        Yii::$app->end();
    }
}