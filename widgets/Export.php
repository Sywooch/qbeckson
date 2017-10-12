<?php
namespace app\widgets;

use app\helpers\GridviewHelper;
use app\models\ExportFile;
use yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * Class Export
 * @package app\widgets
 */
class Export extends Widget
{
    public $dataProvider;

    public $group;

    public $columns;

    public $table;

    /**
     * @return string
     */
    public function run()
    {
        if (Yii::$app->request->isPost && !empty(Yii::$app->request->post('initExport')) && Yii::$app->request->post('initExport') == 1) {
            $doc = ExportFile::createInstance(GridviewHelper::getFileName($this->group), $this->group, $this->table, $this->dataProvider, $this->columns);
            if (!empty($doc)) {
                $filepath = Yii::getAlias('@pfdoroot/uploads/') . $doc->table . '/' . $doc->file . '.xls';
                if (file_exists($filepath))
                {
                    @unlink($filepath);
                }
                exec("php " . Yii::getAlias('@app') . '/yii export/make ' . $doc->id . ' > /dev/null 2>&1 &', $resultArray);
            }
            $this->view->context->redirect(['personal/' . $this->group, '#' => 'excel-download']);
            Yii::$app->end();
        }

        echo '<span id="excel-download"></span>';
        $doc = ExportFile::findByUserId(Yii::$app->user->id, $this->group);
        if (!empty($doc) && $doc->status == ExportFile::STATUS_PROCESS) {
            echo '<div class="well text-warning">Пожалуйста, обновите эту страницу через несколько минут &ndash; ваша выписка уже почти готова.</div>';
        } else {
            $form = ActiveForm::begin();
            echo Html::hiddenInput('initExport', 1);
            echo Html::submitButton('Сформировать новую выписку из реестра', ['class' => 'btn btn-success']) ;
            ActiveForm::end();
        }

        if (!empty($doc) && $doc->status == ExportFile::STATUS_READY) {
            echo '<br />' . Html::a('Скачать выписку от ' . Yii::$app->formatter->asDatetime($doc->created_at), Yii::getAlias('@pfdo/uploads/' . $this->table . '/' . $doc->file . '.xls'), ['class' => 'btn btn-primary']);
        }
    }
}