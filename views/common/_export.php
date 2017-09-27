<?php
use app\helpers\GridviewHelper;
use kartik\export\ExportMenu;
use yii\helpers\Html;
?>

<p class="lead">Экспорт данных:</p>
<?= ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'target' => ExportMenu::TARGET_SELF,
    'showColumnSelector' => false,
    'filename' => GridviewHelper::getFileName($group),
    'stream' => false,
    'deleteAfterSave' => false,
    'folder' => '@pfdoroot/uploads/' . $table,
    'linkPath' => '@pfdo/uploads/' . $table,
    'showConfirmAlert' => false,
    'afterSaveView' => '@app/views/common/export-view',
    'dropdownOptions' => [
        'class' => 'btn btn-success',
        'label' => 'Сформировать новую выписку из реестра',
        'icon' => false,
    ],
    'exportConfig' => [
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_CSV => false,
        ExportMenu::FORMAT_HTML => false,
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_EXCEL_X => false,
    ],
    'columns' => GridviewHelper::prepareColumns($table, $columns, empty($type) ? null : $type),
]); ?>
<?php
if ($doc = \app\models\ExportFile::findByUserId(Yii::$app->user->id, $group)) {
    echo '&nbsp;&nbsp;' . Html::a('Скачать выписку от ' . Yii::$app->formatter->asDatetime($doc->created_at), Yii::getAlias('@pfdo/uploads/' . $table . '/' . $doc->file), ['class' => 'btn btn-primary']);
}
?>
