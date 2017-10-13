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

    public $redirectUrl;

    /**
     * @return string
     */
    public function run()
    {
        $doc = ExportFile::findByUserId(Yii::$app->user->id, $this->group);
        // Проверка на повторное нажатие кнопки
        if (Yii::$app->request->isPost && !empty($doc) && $doc->status == ExportFile::STATUS_PROCESS) {
            $this->redirect();
        }
        if (Yii::$app->request->isPost && !empty($doc) && $doc->cannotBeUpdated) {
            Yii::$app->session->setFlash('info', 'Вы сможете заказать новую выписку не ранее чем в ' . date('H:i', $doc->cannotBeUpdated) . ' по мск');

            $this->redirect();
        }

        if (Yii::$app->request->isPost && !empty(Yii::$app->request->post('initExport')) && Yii::$app->request->post('initExport') == $this->group) {
            $newDoc = ExportFile::createInstance(GridviewHelper::getFileName($this->group), $this->group, $this->table, $this->dataProvider, $this->columns);
            if (!empty($newDoc)) {
                $filepath = Yii::getAlias('@pfdoroot/uploads/') . $newDoc->table . '/' . $newDoc->file . '.xlsx';
                if (file_exists($filepath))
                {
                    @unlink($filepath);
                }
                exec("php " . Yii::getAlias('@app') . '/yii export/make ' . $newDoc->id . ' > /dev/null 2>&1 &', $resultArray);
                //exec("php " . Yii::getAlias('@app') . '/yii export/make ' . $newDoc->id, $resultArray);
            }

            $this->redirect();
        }

        echo '<span id="excel-download"></span>';
        if (!empty($doc) && $doc->status == ExportFile::STATUS_PROCESS) {
            echo '<div class="well text-warning">Выписка готовится. Продолжительность ее создания зависит от количества записей и может занимать от нескольких секунд до нескольких часов. Обновите страницу через некоторое время.</div>';
        } elseif (!empty($doc) && $doc->cannotBeUpdated) {
            echo '<div class="well text-info">Вы сможете заказать новую выписку не ранее чем в ' . date('H:i', $doc->cannotBeUpdated) . ' по мск</div>';
        } else {
            $form = ActiveForm::begin();
            echo Html::hiddenInput('initExport', $this->group);
            echo "<p class=\"lead\">Экспорт данных:</p>";
            echo Html::submitButton('Сформировать новую выписку из реестра', ['class' => 'btn btn-success']) . '<br /><br />';
            ActiveForm::end();
        }

        if (!empty($doc) && $doc->status == ExportFile::STATUS_READY) {
            echo Html::a('Скачать выписку от ' . Yii::$app->formatter->asDatetime($doc->created_at), Yii::getAlias('@pfdo/uploads/' . $this->table . '/' . $doc->file . '.xlsx'), ['class' => 'btn btn-primary']) . '<br /><br />';
        }
    }

    private function redirect()
    {
        $this->view->context->redirect(['personal/' . (!empty($this->redirectUrl) ? $this->redirectUrl : $this->group), '#' => 'excel-download']);
        Yii::$app->end();
    }
}