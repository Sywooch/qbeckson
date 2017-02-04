<?php

use yii\helpers\Html;
use app\models\Disputes;

/* @var $this yii\web\View */
/* @var $model app\models\Disputes */

$this->title = 'Оставить возражение';
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/certificate-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disputes-create col-md-10 col-md-offset-1">
    
    <div class="container-fluid">
        
    <?php if (isset($disputes)) {
        foreach ($disputes as $dispute) {
            
            $disp = Disputes::findOne($dispute);
                
            $datedisput = explode('-',$disp->date);
            
            if ($disp->user_id == Yii::$app->user->id) {
                echo '<div class="row message-wrapper">
                    <div class="col-xs-8 col-xs-offset-4 text-right message-box-my">
                        <div class="small pull-right">'.$datedisput[2].'.'.$datedisput[1].'.'.$datedisput[0].'</div>
                        <p class="message">'.$disp->text.'</p>
                    </div>
                </div>';
            }
            else {
                $roles = Yii::$app->authManager->getRolesByUser($disp->user_id);
                
                if (isset($roles['operators'])) {
                    $name = (new \yii\db\Query())
                        ->select(['name'])
                        ->from('operators')
                        ->where(['user_id' => $disp->user_id])
                        ->one();
                    $username = $name['name'];
                }
                if (isset($roles['organizations'])) {
                    $name = (new \yii\db\Query())
                        ->select(['name'])
                        ->from('organization')
                        ->where(['user_id' => $disp->user_id])
                        ->one();
                    $username = $name['name'];
                }

                if (isset($roles['payer'])) {
                    $name = (new \yii\db\Query())
                        ->select(['name'])
                        ->from('payers')
                        ->where(['user_id' => $disp->user_id])
                        ->one();
                    $username = $name['name'];
                }
                if (isset($roles['certificate'])) {
                    $name = (new \yii\db\Query())
                        ->select(['fio_child'])
                        ->from('certificates')
                        ->where(['user_id' => $disp->user_id])
                        ->one();
                    $username = $name['fio_child'];
                }
                
                $rol = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
                if (isset($rol['organizations'])) {
                    if ($disp->display == 0) {
                        echo '<div class="row message-wrapper">
                        <div class="col-xs-8 message-box">
                            <div class="small pull-right">'.$datedisput[2].'.'.$datedisput[1].'.'.$datedisput[0].'<br>'.$username.'</div>
                            <p class="message">'.$disp->text.'</p>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="row message-wrapper">
                        <div class="col-xs-8 message-box">
                            <div class="small pull-right">'.$datedisput[2].'.'.$datedisput[1].'.'.$datedisput[0].'<br>'.$username.'</div>
                            <p class="message">'.$disp->text.'</p>
                        </div>
                    </div>';
                }    
                
            }
            
        }
    }
    ?>
    
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'contract' => $contract,
    ]) ?>

</div>
