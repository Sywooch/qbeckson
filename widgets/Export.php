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
    public $searchModel;

    public $group;

    public $columns;

    public $table;

    /**
     * @return string
     */
    public function run()
    {
        if (Yii::$app->request->isPost && !empty(Yii::$app->request->post('initExport')) && Yii::$app->request->post('initExport') == 1) {
            print_r(GridviewHelper::prepareColumns($this->table, $this->columns, empty($this->group) ? null : $this->group, 'export'));exit;
            ExportFile::createInstance(GridviewHelper::getFileName($this->group), $this->group, $this->table, $this->searchModel, $this->columns);
            $this->view->context->redirect(['personal/' . $this->group, '#' => 'excel-download']);
            Yii::$app->end();
        }

        if (0) {
            echo '<p>Пожалуйста, обновите эту страницу через несколько минут &ndash; ваша выписка уже почти готова.</p>';
        } else {
            $form = ActiveForm::begin();
            echo Html::hiddenInput('initExport', 1);
            echo Html::submitButton('Сформировать новую выписку из реестра', ['class' => 'btn btn-success']) ;
            ActiveForm::end();
        }

        if ($doc = \app\models\ExportFile::findByUserId(Yii::$app->user->id, $this->group)) {
            echo '&nbsp;&nbsp;' . Html::a('Скачать выписку от ' . Yii::$app->formatter->asDatetime($doc->created_at), Yii::getAlias('@pfdo/uploads/' . $this->table . '/' . $doc->file), ['class' => 'btn btn-primary']);
        }
    }
}