<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Organization;

/* @var $this yii\web\View */  

$date=explode(".", date("d.m.Y"));
            switch ($date[1]){
            case 1: $m='январь'; break;
            case 2: $m='февраль'; break;
            case 3: $m='март'; break;
            case 4: $m='апрель'; break;
            case 5: $m='май'; break;
            case 6: $m='июнь'; break;
            case 7: $m='июль'; break;
            case 8: $m='август'; break;
            case 9: $m='сентябрь'; break;
            case 10: $m='октябрь'; break;
            case 11: $m='ноябрь'; break;
            case 12: $m='декабрь'; break;
            }
        
        
$this->title = 'Авансировать за '.$m;

  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
   $this->params['breadcrumbs'][] = $this->title;
?>


<?php
    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
    $organizations = new Organization();
    $organization = $organizations->getOrganization();
    ?>
    <div class="col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
       
       
<?= GridView::widget([
    'dataProvider' => $GroupsProvider,
    'filterModel' => $searchGroups,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],

        //'id',
        //'organization_id',
        'program.name',
        [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value'=> function($data){
                    return Html::a($data->name, Url::to(['/groups/contracts', 'id' => $data->id]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],
        [
                  'format' => 'raw',
                  'value' => function($data){
                      $completeness = (new \yii\db\Query())
                        ->select(['completeness', 'id'])
                        ->from('completeness')
                        ->where(['group_id' => $data->id])
                        ->andWhere(['month' => date('m')])
                        ->andWhere(['preinvoice' => 1])
                        ->one();
                      
                      if ($completeness['completeness'] > 80) { $comp = 80; } else { $comp = $completeness['completeness']; }
                    return Html::a($comp.' %' , Url::to(['/completeness/preinvoice', 'id' => $completeness['id']]), ['class' => 'btn btn-primary']);
                  }
                 ],

    ],
]); ?>

<?= Html::a('Назад', ['/personal/organization-invoices'], ['class' => 'btn btn-primary']) ?>
&nbsp;
<?php 
    if ($GroupsProvider->getTotalCount() > 0) {
        echo Html::a('Продолжить', ['contracts/preinvoice'], ['class' => 'btn btn-success']);
    }
?>
</div>