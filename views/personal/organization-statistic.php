<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Информация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-md-10 col-md-offset-1 well">
<p>Количество сертифицированных программ образовательной организации - <?= $count_programs ?></p>
<p>Количество програм  образовательной организации ожидающих сертификации - <?= $count_wait_programs ?></p>
<p>Максимально допустимое количество детей для обучения по системе персонифицированного финансирования - <?= $organization['max_child'] ?></p>
<p>Количество детей обучающихся по системе персонифицированного финансирования - <?php
    $cert = (new \yii\db\Query())
                        ->select(['certificate_id'])
                        ->from('contracts')
                        ->where(['organization_id' => $organization['id']])
                        ->andWhere(['status' => 1])
                        ->column();
                $cert = array_unique($cert);
                $cert = count($cert);
    echo $cert;
    ?></p>
<p>Количество мест по которым могут быть заключены договора по системе персонифицированного финансирования - <?=  $organization['max_child'] - $cert ?></p>
<p>Количество заявок на заключение договоров по системе персонифицированного финансирования - <?= $contract_wait ?></p>
</div>