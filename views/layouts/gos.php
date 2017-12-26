<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\models\PersonalAssignmentViewHelper;
use app\widgets\Alert;
use app\widgets\MainFooter;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
/** @var \app\models\UserIdentity $user */
$user = Yii::$app->user->getIdentity();
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
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <div class="container-fluid">
        <div class="top-line row">
            <div class="col-md-7 col-md-offset-1 text-center">
                <a href="<?= Url::home() ?>">
                    Портал персонифицированного финансирования дополнительного образования детей
                </a>
            </div>
            <div class="col-md-4">
                <?php
                if (!Yii::$app->user->isGuest) {
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav navbar-right header-nav'],
                        'items' => [
                            PersonalAssignmentViewHelper::getAssignedUsersNavItems(),
                            [
                                'label' => 'Выйти(' .  ($user->isMonitored ? $user->monitor->username : $user->username) . ')',
                                'url' => ['site/logout'],
                                'linkOptions' => [
                                    'data-method' => 'post',
                                    'class' => 'btn btn-link'
                                ],
                            ]
                        ],
                    ]);
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                NavBar::begin([
                    'brandLabel' => '<div class="logo"></div>',
                    'brandUrl' => Yii::$app->homeUrl,
                    'options' => [
                        'class' => 'navbar navbar-default main-menu',
                    ],
                    'innerContainerOptions' => [
                        'class' => 'container-fluid',
                    ],
                ]);
                if (Yii::$app->user->can('certificate')) {
                    echo "<div class='row cert-menu'>";
                    echo "<div class='col-sm-6 col-md-4 col-sm-offset-1 col-md-offset-2 big-nav'>";
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav'],
                        'items' => [
                            ['label' => 'Программы', 'url' => ['/personal/certificate-programs']],
                            ['label' => 'Организации', 'url' => ['/personal/certificate-organizations']],
                        ],
                    ]);
                    echo '</div>';
                    $certificate = Yii::$app->user->identity->certificate;
                    if ($certificate->actual === 0) {
                        echo Nav::widget([
                            'options' => ['class' => 'navbar-nav navbar-right balancefield'],
                            'encodeLabels' => false,
                            'items' => [
                                [
                                    'label' => 'Заморожен <span class="glyphicon glyphicon-user"></span>',
                                    'url' => ['/personal/certificate-info']
                                ],
                            ],
                        ]);
                    } elseif ($certificate->certGroup->is_special > 0) {
                        $items = [
                            ['label' => 'Сертификат учёта <span class="glyphicon glyphicon-user"></span>',

                                'url' => ['/personal/certificate-info']],
                        ];
                    } else {
                        echo Nav::widget([
                            'options' => ['class' => 'navbar-nav navbar-right balancefield'],
                            'encodeLabels' => false,
                            'items' => [
                                [
                                    'label' => $certificate['balance'] .
                                        ' руб. <span class="glyphicon glyphicon-user"></span>',
                                    'url' => ['/personal/certificate-info']
                                ], \app\models\Menu::exitButton()
                            ],
                        ]);
                    }
                    echo '</div>';
                } else {
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav  inner-nav'],
                        'items'   => \app\models\Menu::getByCurrentUser()
                    ]);
                }
                NavBar::end();
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php
                $links = isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [];
                $links = array_merge($links,
                    [[
                         'label' => 'Видео-уроки',
                         'url' => ['controller/action'],
                         'template' =>
                             '<li class="breadcrumbs-help-icon"><a href="' .
                             Url::to(['site/help']) .
                             '"><span class="glyphicon glyphicon-film"></span> Видео-уроки</a></li>'
                     ]],
                    [[
                        'label' => 'О работе в ИС ПФДО',
                        'url' => ['site/manuals'],
                        'template' =>
                            '<li class="breadcrumbs-help-icon"><a href="' .
                            Url::to(['site/manuals']) .
                            '"><span class="glyphicon glyphicon-info-sign"></span> О работе в ИС ПФДО </a>&nbsp&nbsp</li>'
                     ]]);
                echo Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => 'Главная',
                        'url' => ['site/index'],
                        'template' => '<span class="glyphicon glyphicon-home"></span> <li>{link}</li>'
                    ],
                    'links' => $links,
                ])
                ?>
            </div>
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <?= \app\components\widgets\AlertModal::widget() ?>
                <?= Alert::widget() ?>
            </div>
            <div class="col-md-12">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
<div class="input-title-body"></div>
<?= MainFooter::widget(); ?>
<?= $this->render('../parts/_popups') ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
