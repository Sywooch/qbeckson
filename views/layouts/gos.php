<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\Organization;

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
                   <div class="col-md-10 col-md-offset-1 text-center">
                       <a href="<?= Url::home() ?>">Портал персонифицированного финансирования дополнительного образования детей</a>
                   </div>
                   <div class="col-md-1">
                        <?php
                        if (!Yii::$app->user->isGuest) {
                            echo Nav::widget([
                                'options' => ['class' => 'navbar-nav navbar-right header-nav'],
                                'items' => [
                                    [
                                        'label' => 'Выйти(' .  $user->username . ')',
                                        'url' => ['/site/logout'],
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
                    <div class="col-xs-12">
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

                            if (Yii::$app->user->can('admins')) {
                                echo Nav::widget([
                                    'options' => ['class' => 'navbar-nav'],
                                    'items' => [
                                        ['label' => 'Главная', 'url' => ['/personal/index']],
                                        [
                                            'label' => 'Ролевая система',
                                            'url' => ['/personal/index'],
                                            'items' => [
                                                ['label' => 'Правила доступа', 'url' => ['/permit/access/permission']],
                                                ['label' => 'Управление ролями', 'url' => ['/rbac-access/role']],
                                                ['label' => 'Список пользователей', 'url' => ['/user/index']],
                                            ],
                                        ],
                                        [
                                            'label' => 'Импорт данных',
                                            'url' => ['/import/index'],
                                            'items' => [
                                                ['label' => 'Дети (сертификаты)', 'url' => ['/import/children']],
                                            ],
                                        ],
                                    ],
                                ]);
                            }

                            if (Yii::$app->user->can('operators')) {
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

                            if (Yii::$app->user->can('payer')) {
                                echo Nav::widget([
                                    'options' => ['class' => 'navbar-nav inner-nav'],
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

                            if (Yii::$app->user->can('organizations')) {
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

                            if (Yii::$app->user->can('certificate')) {
                                echo "<div class='row cert-menu'>";
                                echo "<div class='col-sm-6 col-md-4 col-sm-offset-1 col-md-offset-2 big-nav'>";
                                echo Nav::widget([
                                    'options' => ['class' => 'navbar-nav'],
                                    'items' => [
                                        ['label' => 'Программы', 'url' => ['/programs/search']],
                                        ['label' => 'Организации', 'url' => ['/personal/certificate-organizations']],
                                    ],
                                ]);
                                echo "</div>";

                                $certificate = Yii::$app->user->identity->certificate;
                                if ($certificate->actual == 0) {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav navbar-right balancefield'],
                                        'encodeLabels' => false,
                                        'items' => [
                                            ['label' => 'Заморожен <span class="glyphicon glyphicon-user"></span>', 'url' => ['/personal/certificate-info']],
                                        ],
                                    ]);
                                } else {
                                    echo Nav::widget([
                                        'options' => ['class' => 'navbar-nav navbar-right balancefield'],
                                        'encodeLabels' => false,
                                        'items' => [
                                            ['label' => $certificate['balance'].' руб. <span class="glyphicon glyphicon-user"></span>', 'url' => ['/personal/certificate-info']],
                                        ],
                                    ]);
                                }
                                echo "</div>";
                            }
                            NavBar::end();
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                    <?php
                        $links = isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [];
                        $links = array_merge($links, [[
                            'label' => 'Видео-уроки',
                            'url' => ['controller/action'],
                            'template' => "<li class=\"breadcrumbs-help-icon\"><a href=\"" . Url::to(['site/help']) . "\"><span class=\"glyphicon glyphicon-film\"></span> Видео-уроки</a></li>\n"
                        ]]);
                        echo Breadcrumbs::widget([
                            'homeLink' => ['label' => 'Главная', 'url' => '/', 'template' => '<span class="glyphicon glyphicon-home"></span> <li>{link}</li>'],
                            'links' => $links,
                        ])
                        ?>
                    </div>
                    <div class="col-xs-12 col-md-8 col-md-offset-2">
                         <?php
                            // TODO: Убрать всё это говно
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
                         ?>

                         <?= app\widgets\Alert::widget() ?>
                    </div>
                    <div class="col-xs-12">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
            if ($this->beginCache('main-footer', ['duration' => 3600])) {
                echo app\widgets\MainFooter::widget();
                $this->endCache();
            }
        ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
