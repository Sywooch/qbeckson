<?php

/**
 * страница импорта сертификатов из excel таблицы
 *
 * @var $this View
 * @var $certificateImportTemplate CertificateImportTemplate|null
 * @var $certificateImportRegistry CertificateImportRegistry - модель для обновления буфера списка сертификатов для импорта
 */

use app\models\certificates\CertificateImportRegistry;
use app\models\certificates\CertificateImportTemplate;
use trntv\filekit\widget\Upload;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Импорт сертификатов';

$js = <<<JS
$('.upload-certificate-list-modal-toggle').on('click', function() {
  $('#upload-certificate-list-modal').modal();
});

$('.upload-certificate-list').on('click', function() {
  var url = $(this).data('upload-url'),
      form = $('#certificate-list-upload-form'),
      alert = form.find('.alert'),
      uploadButton = $(this),
      formData = new FormData(form[0]);
  
  uploadButton.attr("disabled","disabled");
  
  alert.show();
  alert.removeClass('alert-warning');
  alert.removeClass('alert-success');
  alert.addClass('alert-info');
  alert.html('Ваш файл принят в обработку системой. Если в нем много записей, то рекомендуем подождать достаточное время, после чего обновить страничку. Как только система отработает запрос на добавление сертификатов - Вы увидите на обновленной страничке новый реестр для загрузки на компьютер.');
  
  $.ajax({
    url: url,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function(data) {
        uploadButton.removeAttr('disabled');
        
        if(data == 1) {
            alert.removeClass('alert-warning');
            alert.removeClass('alert-info');
            alert.addClass('alert-success');
            alert.html('Список сертификатов успешно загружен');
            location.reload();
            
            return;
        } 

        alert.removeClass('alert-success');
        alert.removeClass('alert-info');
        alert.addClass('alert-warning');
            
        if (data == 0) {
            alert.html('Возникла ошибка при загрузке списка сертификатов');
            
            return;
        }
        
        alert.html(data);
    }
  })
});

$('.download-certificate-registry').on('click', function() {
  $('.delete-certificate-registry').show();
  $('.download-registry').hide();
  
  $('.new-certificate-list-checkbox').removeAttr('disabled');
});

$('input[type="file"]').on('change', function() {
  $('.upload-certificate-list').show();
})

$('.download-imported-certificate-list').on('click', function() {
  $('.download-registry').show();
  $('.delete-certificate-registry').hide();
})
JS;
$this->registerJs($js);

?>

<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-body">

            <!-- ссылка на шаблон импорта списка сертификатов -->
            <?php if (!is_null($certificateImportTemplate) && !is_null($certificateImportTemplate->templateDownloadUrl())): ?>
                <h1>Импорт списка сертификатов</h1>
                <p>В данном разделе Вы можете самостоятельно загрузить сразу большое количество сертификатов дополнительного образования (пока установлено ограничение на список в 5000 сертификатов). Пожалуйста,
                    неукоснительно следуйте следующей простой инструкции:
                    <br>
                    1) Скачайте шаблон (лист в формате *.xlsx) для загрузки сертификатов дополнительного образования - щелкните на данную <a href="<?= $certificateImportTemplate->templateDownloadUrl() ?>">ссылку</a>
                    <br>
                    2) Сохраните <a href="<?= $certificateImportTemplate->templateDownloadUrl() ?>">шаблон для загрузки</a> на своем компьютере и перенесите туда ФИО детей, для которых будут создаваться
                    сертификаты в системе. Проверьте правильность заполнения списка, наличие возможных повторов (имейте ввиду, что информационная система не станет вместо Вас проверять есть ли дублирующие
                    записи в Вашем списке, удалить дубли Вы должны заблаговременно). Обязательно сохраните заполненную версию списка.
                    <br>
                    3) Подтвердите готовность загрузки списка установив галочку внизу.
                    <br>
                    4) Загрузите список. Системе может потребоваться значительное время на то, чтобы сформировать номера сертификатов и пароли к ним. Ждите.
                    <br>
                    5) Скачайте временный файл с логинами и паролями для личных кабинетов детей из Вашего списка. Обязательно удалите этот временный файл после скачивания, но сперва убедитесь, что он сохранен
                    на Вашем компьютере. Ровно так же как мы не хотим оставлять пароли в незашифрованном виде в системе - мы не хотим, чтобы Вы после создания сертификатов потеряли пароли к ним.</p>
            <?php else: ?>
                <h1>Нет шаблона для импорта списка сертификатов</h1>
            <?php endif; ?>

            <?php $registryNotDownloadedOption = (!$certificateImportRegistry->is_registry_downloaded && $certificateImportRegistry->registryFileExist()) ? ['disabled' => true, 'title' => 'Ранее загруженный реестр не был ни разу скачан. Пожалуйста скачайте его перед загрузкой нового.'] : [] ?>

            <!-- загрузка файла xlsx со списком сертификатов -->
            <div class="">
                <div class="checkbox-container">
                    <?= Html::checkbox('new-certificate-list-checkbox',
                        false, [
                            'class' => 'new-certificate-list-checkbox',
                            'label' => 'Таблица со списком сертификатов подготовлена к загрузке (новый импорт удалит ранее загруженный список сертификатов, переходите к импорту только, если Вы уже сохранили номера сертификатов и пароли для предыдущего списка).',
                            'onClick' => 'showNextContainer(this);',
                        ] + $registryNotDownloadedOption) ?>
                </div>
                <div style="display: none">
                    <?= Html::button('Загрузить список сертификатов', ['class' => 'btn btn-primary upload-certificate-list-modal-toggle']) ?>
                </div>
            </div>
            <?php Modal::begin([
                'id' => 'upload-certificate-list-modal',
                'header' => '<b>Загрузка списка сертификатов для импорта</b>',
                'headerOptions' => [
                    'style' => ['text-align' => 'center'],
                ]
            ]) ?>
            <?php $form = ActiveForm::begin([
                'id' => 'certificate-list-upload-form',
                'options' => ['enctype' => 'multipart/form-data'],
                'enableAjaxValidation' => true,
            ]) ?>
            <p>В квадратном окне загрузчика, расположенном ниже выберите заполненный список детей, который планируете импортировать в систему, после чего нажмите кнопку "загрузить выбранный список" (появится
                после
                выбора списка).</p>

            <div class="text-center">
                <?= $form->field($certificateImportRegistry, 'certificateImportListFile')->widget(Upload::class, [
                    'url' => ['file-storage/upload'],
                    'maxFileSize' => 10 * 1024 * 1024,
                    'acceptFileTypes' => new JsExpression('/(\.|\/)(xlsx)$/i'),
                ])->label(''); ?>

            </div>

            <div class="alert alert-info" style="display: none"></div>

            <div class="text-center">
                <?= Html::button('загрузить выбранный список', ['class' => 'btn btn-primary upload-certificate-list', 'data' => ['upload-url' => '/certificates/upload-certificate-list'], 'style' => ['display' => 'none']]) ?>
            </div>

            <?php $form->end() ?>
            <?php Modal::end() ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <!-- ссылка на импортированный список сертификатов и пользователей -->
            <?php if ($certificateImportRegistry->registryFileExist()): ?>
                <p>Последний реестр сертификатов дополнительного образования загруженный Вами <b><?= \Yii::$app->formatter->asDate($certificateImportRegistry->registry_created_at) ?>
                        в <?= \Yii::$app->formatter->asTime($certificateImportRegistry->registry_created_at) ?></b> по мск доступен для скачивания (дети уже внесены в систему, им присвоены номера сертификатов и
                    пароли
                    для входа в личные кабинеты).
                    <br>
                    Пожалуйста, как можно скорее скачайте файл на свой компьютер и удалите его с сервера (чтобы никто другой уже не мог его скачать). Хранение паролей от личных кабинетов на сервере в течение
                    длительного времени не безопасно.
                    <br>
                    <br>
                    *обратите внимание, что все сертификаты созданы как сертификаты учета (с нулевыми номиналами).</p>

                <?php Modal::begin([
                'id' => 'download-imported-certificate-list-modal',
                'header' => '<b>Сохранить загруженный ранее реестр</b>',
                'toggleButton' => [
                    'tag' => 'button',
                    'class' => 'btn btn-primary download-imported-certificate-list',
                    'label' => 'Скачать реестр импортированных сертификатов',
                ],
                'headerOptions' => [
                    'style' => ['text-align' => 'center'],
                ]
            ]) ?>
                <div class="delete-certificate-registry" style="display: none;">
                    <div class="text-center">
                        <?= Html::a('удалить файл реестра с сервера', '/certificates/delete-certificate-list-registry', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <div class="download-registry">
                    <p>Загрузка реестра на Ваш компьютер началась. Пожалуйста, убедитесь (!), что реестр сохранен на Вашем компьютере и нажмите кнопку "удалить реестр с сервера" (появится после скачивания).</p>
                    <div class="text-center">
                        <?= Html::a('скачать реестр', '/certificates/download-certificate-list-registry', ['class' => 'btn btn-primary download-certificate-registry']) ?>
                    </div>
                </div>
                <?php Modal::end() ?>
            <?php endif; ?>
            <br>
            <br>
        </div>
    </div>

</div>
