<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\data\ActiveDataProvider;
use app\models\Informs;
use app\models\Organization;

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
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
        NavBar::begin([
            //'brandLabel' => 'My Company',
            //'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-default navbar-fixed-top',
            ],
        ]);

        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if ($roles['admins']) {
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
        if ($roles['operators']) {
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'Информация', 'items' => [
                        ['label' => 'Статическая информация', 'url' => ['/personal/operator-statistic']],
                        ['label' => 'Сведения об операторе', 'url' => ['/personal/operator-info']],
                    ]],
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
        if ($roles['payer']) {
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'Информация', 'items' => [
                        ['label' => 'Статическая информация', 'url' => ['/personal/payer-statistic']],
                        ['label' => 'Сведения о плательщике', 'url' => ['/personal/payer-info']],
                    ]],
                    ['label' => 'Стоимость групп', 'url' => ['/cert-group/index']],
                    ['label' => 'Сертификаты', 'url' => ['/personal/payer-certificates']],
                    ['label' => 'Договоры', 'url' => ['/personal/payer-contracts']],
                    ['label' => 'Счета', 'url' => ['/personal/payer-invoices']],
                    ['label' => 'Организации', 'url' => ['/personal/payer-organizations']],
                    ['label' => 'Программы', 'url' => ['/personal/payer-programs']],
                ],
            ]);
        }
        if ($roles['organizations']) {
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'Информация', 'items' => [
                        ['label' => 'Статическая информация', 'url' => ['/personal/organization-statistic']],
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
        if ($roles['certificate']) {
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'Информация', 'url' => ['/personal/certificate-statistic']],
                    ['label' => 'Программы', 'items' => [
                        ['label' => 'Обучение в текущем году', 'url' => ['/personal/certificate-programs']],
                        ['label' => 'Предварительная запись', 'url' => ['/personal/certificate-previus']],
                    ]],
                    ['label' => 'Договоры', 'url' => ['/personal/certificate-contracts']],
                    ['label' => 'Избранное', 'url' => ['/personal/certificate-favorites']],
                ],
            ]);
        }
        
        if ($roles['admins']) { 
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
        }

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [/*
                 $count > 0 ? ('<li><a role="button" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" title="Оповещения" data-content="'.$text.'"><span class="badge">'.$count.'</span> <span class="glyphicon glyphicon-flag"></span> ('.$label.')</a></li>') : ('<li><a role="button" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" title="Оповещения" data-content="Нет оповещений"><span class="badge">'.$count.'</span> <span class="glyphicon glyphicon-flag"></span></a></li>'),
                */
                
                Yii::$app->user->isGuest ? (
                    ['label' => 'Войти', 'url' => ['/site/login']]
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


    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])  ?>
       
         <?php 
            $organizations = new Organization();
            $organization = $organizations->getOrganization();
            if ($roles['organizations'] and $organization['actual'] == 0) {
                Yii::$app->session->setFlash('warning', 'Ваша деятельность приостановлена, обратитесь к оператору');
            }
        ?>

        <?= app\widgets\Alert::widget() ?>

        <?= $content ?>
    </div>
    </div>

<!--<footer class="footer">
    <div class="container">
        <p class="text-center">&copy; My Company <?= date('Y') ?></p>
    </div>
</footer> -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
