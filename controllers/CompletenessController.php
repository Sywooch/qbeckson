<?php

namespace app\controllers;

use Yii;
use app\models\Completeness;
use app\models\CompletenessSearch;
use app\models\Contracts;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompletenessController implements the CRUD actions for Completeness model.
 */
class CompletenessController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Completeness models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompletenessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Completeness model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Completeness model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Completeness();

        if ($model->load(Yii::$app->request->post())) {

            $model->group_id = $id;
            $model->month = date(m);
            $model->year = date(Y);

            if ($model->validate() && $model->save()) {
                return $this->redirect(['/personal/organization#panel7']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Completeness model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            $completenes = (new \yii\db\Query())
                ->select(['id'])
                ->from('completeness')
                ->where(['group_id' => $model->group_id])
                ->andWhere(['month' => $model->month])
                ->andWhere(['year' => $model->year])
                ->andWhere(['preinvoice' => 0])
                ->column();
        
            foreach($completenes as $completenes_id){      
                $complet = $this->findModel($completenes_id);
                
                $contract = Contracts::findOne($complet->contract_id);
                
                //return var_dump($complet);
                
                $start_edu_contract  = explode("-", $contract->start_edu_contract);
                
                $month = $start_edu_contract[1];
                        
                        if ($month == date('m')-1) {
                            $price = $contract->payer_first_month_payment;
                        } else {
                            $price = $contract->payer_other_month_payment;
                        }
                
                
                        
                $complet->completeness = $model->completeness;
                $complet->sum = ($price * $model->completeness) / 100;
                
                $complet->save();
                
                //$model->completeness = $completeness['completeness'];  
                //$model->payers_id = $contract->payer_id;
            }
            
            return $this->redirect(['groups/invoice']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionPreinvoice($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {
            
            if ($model->completeness > 80) { 
                 return $this->render('preinvoice', [
                    'model' => $model, 
                     'display' => 'Нельзя авансировать больше 80%',
                ]);
            } 
            
            /*$contracts = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['<=', 'start_edu_contract', date('Y-m-d')])
                ->andWhere(['>=', 'stop_edu_contract', date('Y-m-d')])
                ->andWhere(['organization_id' => $organization->id])
                ->andWhere(['payer_id' => $payer])
                ->column();
                */
            
            $completenes = (new \yii\db\Query())
                ->select(['id'])
                ->from('completeness')
                ->where(['group_id' => $model->group_id])
                ->andWhere(['month' => $model->month])
                ->andWhere(['year' => $model->year])
                ->andWhere(['preinvoice' => 1])
                ->column();
        
            foreach($completenes as $completenes_id){      
                $complet = $this->findModel($completenes_id);
                
                $contract = Contracts::findOne($complet->contract_id);
                
                //return var_dump($complet);
                
                $start_edu_contract  = explode("-", $contract->start_edu_contract);
                
                $month = $start_edu_contract[1];
                        
                        if ($month == date('m')) {
                            $price = $contract->payer_first_month_payment;
                        } else {
                            $price = $contract->payer_other_month_payment;
                        }
                
                
                        
                $complet->completeness = $model->completeness;
                $complet->sum = ($price * $model->completeness) / 100;
                
                $complet->save();
                
                //$model->completeness = $completeness['completeness'];  
                //$model->payers_id = $contract->payer_id;
            }
            
            //if ($model->save()) {
                return $this->redirect(['groups/preinvoice']);   
            //}
        } else {
            return $this->render('preinvoice', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Completeness model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Completeness model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Completeness the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Completeness::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
