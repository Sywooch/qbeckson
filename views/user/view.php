<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
$this->title = 'Новый пользователь';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view  col-md-8  col-md-offset-2">

<?php if (empty($password)) { $password = 'Не изменялся';}  ?>
<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            [
                'attribute' => 'password',
                'value' => $password
            ],
        ],
    ]) ?>

<div class="form-group">
<?php
$roles = Yii::$app->authManager->getRolesByUser($model->id);
if (isset($roles['payer'])) {
    echo Html::a('Добавить еще плательщика', '/payers/create', ['class' => 'btn btn-primary']);
    echo "&nbsp;";
    echo Html::a('Список плательщиков', '/personal/operator-payers', ['class' => 'btn btn-primary']);
}
if (isset($roles['organizations'])) {
    echo Html::a('Добавить еще организацию', '/organization/create', ['class' => 'btn btn-primary']);
    echo "&nbsp;";
    echo Html::a('Список организаций', '/personal/operator-organizations', ['class' => 'btn btn-primary']);
}
if (isset($roles['certificate'])) {
    echo Html::a('Добавить еще сертификат', '/certificates/create', ['class' => 'btn btn-primary']);
    echo "&nbsp;";
    echo Html::a('Список сертификатов', '/personal/payer-certificates', ['class' => 'btn btn-primary']);
}    
?>
</div>

</div>
