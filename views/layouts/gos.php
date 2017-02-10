<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\bootstrap\Alert;
use yii\data\ActiveDataProvider;
use app\models\Informs;
use app\models\Organization;
use app\models\Operators;
use app\models\Certificates;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">
           <div class="container-fluid">
               <div class="top-line row">
                   <div class="col-md-10 col-md-offset-1 text-center">
                       <a href="#">Портал персонифицированного финансирования дополнительного образования детей</a>
                   </div>
                   <div class="col-md-1">
                        <?php
                            echo Nav::widget([
                                'options' => ['class' => 'navbar-nav navbar-right'],
                                'items' => [/*
                                     $count > 0 ? ('<li><a role="button" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" title="Оповещения" data-content="'.$text.'"><span class="badge">'.$count.'</span> <span class="glyphicon glyphicon-flag"></span> ('.$label.')</a></li>') : ('<li><a role="button" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" title="Оповещения" data-content="Нет оповещений"><span class="badge">'.$count.'</span> <span class="glyphicon glyphicon-flag"></span></a></li>'),
                                    */
                                    Yii::$app->user->isGuest ? (
                                        ['label' => '']
                                    ) : (
                                        '<li>'
                                            . Html::beginForm(['/site/logout'], 'post', ['class' => 'navbar-form'])
                                                . Html::submitButton(
                                                    'Выйти (' . Yii::$app->user->identity->username . ')',
                                                    ['class' => 'btn btn-link']
                                                )
                                            . Html::endForm()
                                        . '</li>'
                                    ),               
                                ],
                            ]);
                        ?>
                    </div>
                </div>

                <div class="row">
                   <!-- <div class="col-xs-6 col-lg-2">
                        <a href="<?php // Yii::$app->homeUrl ?>">
                            <div class="logo"></div>
                        </a>
                    </div> -->
                    <div class="col-xs-12">    
                        <?php
                        
                            NavBar::begin([
                                'brandLabel' => '<div class="logo"></div>',
                                'brandUrl' => Yii::$app->homeUrl,
                                'options' => [
                                    'class' => 'navbar navbar-default  main-menu',
                                ],
                                'innerContainerOptions' => [
                                    'class' => 'container-fluid',
                                ],
                            ]);
                        
                                $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

                               if (isset($roles['admins'])) {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav'],
                                        'items' => [
                                            ['label' => 'Главная', 'url' => ['/site/index']],
                                            ['label' => 'Справочный раздел', 'url' => ['/site/about']],
                                            ['label' => 'Обратная связь', 'url' => ['/site/contact']],
                                            ['label' => 'Поиск программ', 'url' => ['/programs/index']],
                                        ],
                                    ]);
                                }
                                if (isset($roles['operators'])) {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav inner-nav'],
                                        'items' => [
                                            ['label' => 'Информация', 'url' => ['/personal/operator-statistic']],
                                            ['label' => 'Коэффициенты', 'items' => [
                                                ['label' => 'Муниципалитеты', 'url' => ['/mun/index']],
                                                ['label' => 'Общие параметры', 'url' => ['/coefficient/update']],
                                            ]],    
                                            ['label' => 'Плательщики', 'url' => ['/personal/operator-payers']],
                                            ['label' => 'Организации', 'url' => ['/personal/operator-organizations']],
                                            ['label' => 'Сертификаты', 'url' => ['/personal/operator-certificates']],
                                            ['label' => 'Договоры', 'url' => ['/personal/operator-contracts']],
                                            ['label' => 'Программы', 'url' => ['/personal/operator-programs']],
                                        ],
                                    ]);
                                }
                                if (isset($roles['payer'])) {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav'],
                                        'items' => [
                                            ['label' => 'Информация', 'url' => ['/personal/payer-statistic']],
                                            ['label' => 'Номиналы групп', 'url' => ['/cert-group/index']],
                                            ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']],
                                            ['label' => 'Договоры', 'url' => ['/personal/payer-contracts']],
                                            ['label' => 'Счета', 'url' => ['/personal/payer-invoices']],
                                            ['label' => 'Организации', 'url' => ['/personal/payer-organizations']],
                                            ['label' => 'Программы', 'url' => ['/personal/payer-programs']],
                                        ],
                                    ]);
                                }
                                if (isset($roles['organizations'])) {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav'],
                                        'items' => [
                                            ['label' => 'Информация', 'items' => [
                                                ['label' => 'Статистическая информация', 'url' => ['/personal/organization-statistic']],
                                                ['label' => 'Сведения об организации', 'url' => ['/personal/organization-info']],
                                                ['label' => 'Предварительные записи', 'url' => ['/personal/organization-favorites']],
                                            ]],
                                            ['label' => 'Программы', 'url' => ['/personal/organization-programs']],
                                            ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']],
                                            ['label' => 'Счета', 'url' => ['/personal/organization-invoices']],
                                            ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']],
                                            ['label' => 'Группы', 'url' => ['/personal/organization-groups']],
                                        ],
                                    ]);
                                }
                                if (isset($roles['certificate'])) {
                                    echo "<div class='row cert-menu'>";
                                    echo "<div class='col-sm-6 col-md-4 col-sm-offset-1 col-md-offset-2 big-nav'>";
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav'],
                                        'items' => [
                                            /*['label' => 'Информация', 'url' => ['/personal/certificate-statistic']],
                                            ['label' => 'Программы', 'items' => [
                                                ['label' => 'Обучение в текущем году', 'url' => ['/personal/certificate-programs']],
                                                ['label' => 'Предварительная запись', 'url' => ['/personal/certificate-previus']],
                                            ]],
                                            ['label' => 'Договоры', 'url' => ['/personal/certificate-contracts']],
                                            ['label' => 'Избранное', 'url' => ['/personal/certificate-favorites']], */
                                            ['label' => 'Программы', 'url' => ['/programs/search']],
                                            ['label' => 'Организации', 'url' => ['/personal/certificate-organizations']],
                                        ],
                                    ]);
                                    echo "</div>";
                                    
                                     $certificates = new Certificates();
                                    $certificate = $certificates->getCertificates();
                                    
                                    if ($certificate->actual == 0) {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav navbar-right balancefield'],
                                        'encodeLabels' => false,
                                        'items' => [
                                            ['label' => 'Заморожен <span class="glyphicon glyphicon-user"></span>', 'url' => ['/personal/certificate-info']],
                                        ],
                                    ]); }
                                    else {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav navbar-right balancefield'],
                                        'encodeLabels' => false,
                                        'items' => [
                                            ['label' => $certificate['balance'].' руб. <span class="glyphicon glyphicon-user"></span>', 'url' => ['/personal/certificate-info']],
                                        ],
                                    ]); }
                                     echo "</div>";
                                }
                              /*  if ($roles['admins']) { 
                                    $label = 'Админ';
                                }
                                if ($roles['operators']) { 
                                    $label = 'Оператор';
                                    $informs = (new \yii\db\Query())
                                        ->select(['text', 'program_id'])
                                        ->from('informs')
                                        ->where(['read' => 0])
                                        ->andwhere(['from'=> 1])
                                        ->all();
                                    $count = count($informs);
                                    $text = '<table class=\'table\'>';
                                    foreach ($informs as $value) {
                                        $text = $text.'<tr><td><a href=\'/programs/view?id='.$value['program_id'].'\'>'.$value['text'].'</a></td></tr>';
                                    }
                                    $text = $text.'</table>';

                                    $informsold = (new \yii\db\Query())
                                        ->select(['text', 'program_id'])
                                        ->from('informs')
                                        ->where(['read' => 1])
                                        ->andwhere(['from'=> 1])
                                        ->all();
                                    $count = count($informs);
                                    $old = '<table class=\'table\'>';
                                    foreach ($informsold  as $value) {
                                        $old = $old.'<tr><td><a href=\'/programs/view?id='.$value['program_id'].'\'>'.$value['text'].'</a></td></tr>';
                                    }
                                    $old = $old.'</table>';
                                }
                                if ($roles['payer']) { 
                                    $label = 'Плательщик';
                                    $informs = (new \yii\db\Query())
                                            ->select(['text', 'program_id'])
                                            ->from('informs')
                                            ->where(['read' => 0])
                                            ->andwhere(['from'=> 2])
                                            ->all();
                                    $count = count($informs);
                                    $text = '<table class=\'table\'>';
                                    foreach ($informs as $value) {
                                        $text = $text.'<tr><td><a href=\'/programs/view?id='.$value['program_id'].'\'>'.$value['text'].'</a></td></tr>';
                                    }
                                    $text = $text.'</table>';
                                }
                                if ($roles['organizations']) { 
                                    $label = 'Организация';
                                    $informs = (new \yii\db\Query())
                                            ->select(['text', 'program_id'])
                                            ->from('informs')
                                            ->where(['read' => 0])
                                            ->andwhere(['from'=> 3])
                                            ->all();
                                    $count = count($informs);
                                    $text = '<table class=\'table\'>';
                                    foreach ($informs as $value) {
                                        $text = $text.'<tr><td><a href=\'/programs/view?id='.$value['program_id'].'\'>'.$value['text'].'</a></td></tr>';
                                    }
                                    $text = $text.'</table>';
                                }
                                if ($roles['certificate']) { 
                                    $label = 'Ребенок';
                                    $informs = (new \yii\db\Query())
                                            ->select(['text', 'program_id'])
                                            ->from('informs')
                                            ->where(['read' => 0])
                                            ->andwhere(['from'=> 4])
                                            ->all();
                                    $count = count($informs);
                                    $text = '<table class=\'table\'>';
                                    foreach ($informs as $value) {
                                        $text = $text.'<tr><td><a href=\'/programs/view?id='.$value['program_id'].'\'>'.$value['text'].'</a></td></tr>';
                                    }
                                    $text = $text.'</table>';
                                } */
                            NavBar::end();
                        ?>
                    </div>
                </div>
            
                <div class="row"> 
                   <div class="col-xs-12">
                       <?= Breadcrumbs::widget([
                        'homeLink' => ['label' => 'Главная', 'url' => '/', 'template' => '<span class="glyphicon glyphicon-home"></span> <li>{link}</li>'],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                   </div>                  
                    <div class="col-xs-12 col-md-8 col-md-offset-2">
                         <?php 
                            $organizations = new Organization();
                            $organization = $organizations->getOrganization();
                            
                            if (isset($roles['organizations']) and $organization['actual'] == 0) {
                                Yii::$app->session->setFlash('warning', 'Ваша деятельность приостановлена, обратитесь к оператору');
                            }
                       
                            if (isset($roles['operators'])) {
                                $coef = (new \yii\db\Query())
                                    ->select(['p21v', 'p21s', 'p21o', 'p22v', 'p22s', 'p22o', 'p3v', 'p3s', 'p3n', 'blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc', 'minraiting', 'weekyear', 'weekmonth', 'pk', 'norm', 'potenc', 'ngr', 'sgr', 'vgr', 'chr1', 'zmr1', 'chr2', 'zmr2', 'ngrp', 'sgrp', 'vgrp', 'ppchr1', 'ppzm1', 'ppchr2', 'ppzm2', 'ocsootv', 'ocku', 'ocmt', 'obsh', 'ktob', 'vgs', 'sgs', 'pchsrd', 'pzmsrd'])
                                    ->from('coefficient')
                                    ->one();
                                
                                
                                $res = array_search(0, $coef);
                                
                                
                                if ($res == true) {
                                    Yii::$app->session->setFlash('warning', 'Необходимо выставить корректные коэффициенты');
                                }
                            }

                            if (Yii::$app->session->hasFlash('error')) {
                                echo Alert::widget([
                                    'options' => [
                                        'class' => 'alert-danger'
                                    ],
                                    'body' => '<b>Ошибка! </b>'.Yii::$app->session->getFlash('error')
                                ]);
                            } 

                            if (Yii::$app->session->hasFlash('warning')) {
                                echo Alert::widget([
                                    'options' => [
                                        'class' => 'alert-warning'
                                    ],
                                    'body' => '<b>Внимание! </b>'.Yii::$app->session->getFlash('warning')
                                ]);
                            } 
                       
                            if (Yii::$app->session->hasFlash('info')) {
                                echo Alert::widget([
                                    'options' => [
                                        'class' => 'alert-info'
                                    ],
                                    'body' => Yii::$app->session->getFlash('info')
                                ]);
                            } 
                         ?>
                    </div> 
                    <div class="col-xs-12">                           
                        <?= $content ?>
                    </div>    
                </div>
            </div>
        </div>             
    

<footer>
 <?php 
           // $operators = new Operators();
            //$operator = $operators->getOperators();
    
            $operator = (new \yii\db\Query())
                ->select(['id', 'name', 'phone', 'email', 'address_actual'])
                ->from('operators')
                ->one();
                        
    ?>
  <div class="container-fluid footers">
     <div class="row">
         <div class="col-md-2 col-md-offset-2 text-center">Сопровождение Портала:<br>
             <?= Html::a($operator['name'], Url::to(['operators/view', 'id' => $operator['id']])) ?></div>
         <div class="col-md-2 text-center">Контактный телефон:<br><div class="phone"><?= $operator['phone'] ?></div></div>
         <div class="col-md-2 text-center">E-mail:<br><a href="mailto:<?= $operator['email'] ?>"><?= $operator['email'] ?></a>
                                                 <br><a href="mailto:<?= Yii::$app->params['adminEmail']; ?>"><?= Yii::$app->params['adminEmail']; ?></a></div>
         <div class="col-md-2 text-center">Адрес:<br><?= $operator['address_actual'] ?></div>
     </div>
  </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
