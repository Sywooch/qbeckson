<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\form\ActiveForm;

$this->title = 'Главная';
/* @var $this yii\web\View */
//$this->params['breadcrumbs'][] = 'Поиск';
?>


<div class="container-fluid">
    <div class="row search-bar">
       <div class="col-xs-8 col-xs-offset-2 text-center">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, "search", ['addon' => ['append' => ['content'=> Html::submitButton('Поиск', ['class' => 'btn btn-default']), 
            'asButton' => true]]])->textInput()->label(false) ?>     
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
           <br><br>
            <div class="container-fluid">
                <div class="row">
                   <div class="col-md-4 text-center">
                      <div class="container-fluid box-hover">
                       <a href="/personal/certificate-contracts">
                        <div class="row">
                           <div class="col-xs-5 text-center">
                                <img src="/img/contract.png" alt="">
                            </div>
                            <div class="col-xs-7 text-left">
                               <span class="badge pull-right"><?= $contracts_count ?></span>
                                <h4>Действующие договоры</h4>
                                <p class="minitext">договоры на обучение по осваиваемым в текущем учебном году программам</p>
                            </div>
                        </div>
                        </a>
                    </div>
                   </div>
                   <div class="col-md-4 text-center">
                      <div class="container-fluid box-hover">
                       <a href="/personal/certificate-wait-contract">
                        <div class="row">
                           <div class="col-md-5 text-center">
                               <img src="/img/wait_contract.png" alt="">
                           </div>
                            <div class="col-xs-7 text-left">
                               <span class="badge pull-right"><?= $contracts_wait_count ?></span>
                                <h4>Ожидающие договоры</h4>
                                <p class="minitext">договоры, созданные организацией и ожидающие подписания</p>
                            </div>
                        </div>
                          </a>
                    </div>
                   </div>
                   <div class="col-md-4 text-center">
                      <div class="container-fluid box-hover">
                       <a href="/personal/certificate-wait-request">
                        <div class="row">
                           <div class="col-md-5 text-center">
                               <img src="/img/entry.png" alt="">
                           </div>
                            <div class="col-xs-7 text-left">
                               <span class="badge pull-right"><?= $contracts_wait_request ?></span>
                                <h4>Мои заявки</h4>
                                <p class="minitext">созданые заявки, ожидающие рассмотрения образовательной организацией</p>
                            </div>
                        </div>
                          </a>
                    </div>
                   </div>
                </div>
                <br><br>
                <div class="row">
                   <div class="col-md-4 text-center">
                      <div class="container-fluid box-hover">
                       <a href="/personal/certificate-archive">
                        <div class="row">
                           <div class="col-md-5 text-center">
                               <img src="/img/archive.png" alt="">
                            </div>
                            <div class="col-xs-7 text-left">
                              <span class="badge pull-right"><?= $contracts_arhive ?></span>
                               <h4>Архив</h4>
                               <p class="minitext">договоры, закончившие действие и отклоненные заявки</p>
                                <p></p>
                            </div>
                        </div>
                        </a>
                    </div>
                   </div>
                   <div class="col-md-4 text-center">
                      <div class="container-fluid box-hover">
                       <a href="/personal/certificate-previus">
                        <div class="row">
                           <div class="col-md-5 text-center">
                               <img src="/img/previus.png" alt="">
                            </div>
                            <div class="col-xs-7 text-left">
                              <span class="badge pull-right"><?= $contracts_previus ?></span>
                               <h4>Предварительные записи</h4>
                                <p class="minitext">программы, выбранные для обучения в следующем учебном году</p>
                            </div>
                        </div>
                          </a>
                    </div>
                   </div>
                   <div class="col-md-4 text-center">
                      <div class="container-fluid box-hover">
                       <a href="/personal/certificate-favorites">
                        <div class="row">
                           <div class="col-md-5 text-center">
                               <img src="/img/favorit.png" alt="">
                            </div>
                            <div class="col-xs-7 text-left">
                              <span class="badge pull-right"><?= $contracts_favorites ?></span>
                               <h4>Избранные программы</h4>
                                <p class="minitext">быстрый доступ к понравившимся программам</p>
                            </div>
                        </div>
                    </a>
                    </div>
                   </div>
                </div>
            </div>
            
        </div>
    </div>
</div>