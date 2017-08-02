<?php

namespace app\controllers;

use app\models\GroupClass;
use app\models\ProgramModuleAddress;
use Yii;
use app\models\Groups;
use app\models\ProgrammeModule;
use app\models\Payers;
use app\models\User;
use app\models\GroupsSearch;
use app\models\GroupsInvoiceSearch;
use app\models\Completeness;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Organization;
use app\models\Contracts;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\models\ContractsStatus1onlySearch;


/**
 * GroupsController implements the CRUD actions for Groups model.
 */
class GroupsController extends Controller
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
     * Lists all Groups models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GroupsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Groups model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionContracts($id)
    {
        $model = $this->findModel($id);

        /* $rows = (new \yii\db\Query())
                 ->select(['id'])
                 ->from('contracts')
                 ->where(['group_id' => $id])
                 ->andWhere(['status' => 1])
                 ->column();

         if (empty($rows)) { $rows = 0; }*/


        $Contracts1Search = new ContractsStatus1onlySearch();
        $Contracts1Search->group_id = $id;
        $ContractsProvider = $Contracts1Search->search(Yii::$app->request->queryParams);

        //return var_dump($rows);

        return $this->render('contracts', [
            'model' => $model,
            'ContractsProvider' => $ContractsProvider,
        ]);
    }

    /**
     * Creates a new Groups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Groups();

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        if ($model->load(Yii::$app->request->post())) {
            $model->organization_id = $organization['id'];
            //$model->program_id = (int) $model->program_id;

            $rows = (new \yii\db\Query())
                ->select(['month'])
                ->from('years')
                ->where(['id' => $model->year_id])
                ->one();

            $d1 = strtotime($model->datestart);
            $d2 = strtotime($model->datestop);
            $diff = $d2 - $d1;
            $diff = $diff / (60 * 60 * 24 * 31);
            $month = floor($diff);

            if ($rows['month'] < $month - 1 or $rows['month'] > $month + 1) {
                Yii::$app->session->setFlash('error', 'Продолжительность программы должна быть ' . $rows['month'] . ' месяцев.');

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->save()) {
                /* $completeness = new Completeness();
                 $completeness->group_id = $model->id;

                 if (date(m) == 1) {
                     $completeness->month = 12;
                     $completeness->year = date(Y) - 1;
                 } else {
                     $completeness->month = date(m) - 1;
                     $completeness->year = date(Y);
                 }
                 $completeness->preinvoice = 0;
                 $completeness->completeness = 100;
                 if ($completeness->save()) {
                     $preinvoice = new Completeness();
                     $preinvoice->group_id = $model->id;
                     $preinvoice->month = date(m);
                     $preinvoice->year = date(Y);
                     $preinvoice->preinvoice = 1;
                     $preinvoice->completeness = 80;
                     if ($preinvoice->save()) { */
                return $this->redirect(['/personal/organization-groups']);
                //  }
                // }
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionNewgroup($id)
    {
        $model = new Groups();
        $model->year_id = $id;
        $rows = (new \yii\db\Query())
            ->select(['program_id'])
            ->from('years')
            ->where(['id' => $id])
            ->one();
        $model->program_id = $rows['program_id'];
        $organizations = new Organization();
        $organization = $organizations->getOrganization();
        $model->organization_id = $organization['id'];

        $groupClasses = [];
        foreach (GroupClass::weekDays() as $key => $day) {
            $groupClasses[$key] = new GroupClass([
                'week_day' => $day
            ]);
        }
        $programModuleAddresses = ArrayHelper::map(
            ProgramModuleAddress::find()->andWhere(['program_module_id' => $id])->all(),
            'address',
            'address'
        );

        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            Model::loadMultiple($groupClasses, Yii::$app->request->post());
            if ($model->validate() && Model::validateMultiple($groupClasses)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        foreach ($groupClasses as $classModel) {
                            /** @var GroupClass $classModel */
                            if ($classModel->status) {
                                $classModel->group_id = $model->id;
                                if (!($flag = $classModel->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->redirect(['programs/view', 'id' => $model->program_id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('newgroup', [
            'model' => $model,
            'groupClasses' => $groupClasses,
            'programModuleAddresses' => $programModuleAddresses
        ]);
    }

    /**
     * Updates an existing Groups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $programModuleAddresses = ArrayHelper::map(
            ProgramModuleAddress::find()->andWhere(['program_module_id' => $model->module->id])->all(),
            'address',
            'address'
        );

        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            Model::loadMultiple($model->classes, Yii::$app->request->post());
            if ($model->validate() && Model::validateMultiple($model->classes)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        foreach ($model->classes as $classModel) {
                            /** @var GroupClass $classModel */
                            $classModel->group_id = $model->id;
                            if (!($flag = $classModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->redirect(['personal/organization-groups']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'programModuleAddresses' => $programModuleAddresses,
        ]);
    }

    public function actionYear()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $prog_id = $parents[0];

                //$out = ProgrammeModule::find()->where(['program_id' => $prog_id])->asArray()->all();

                $rows = (new \yii\db\Query())
                    ->select(['id', 'year'])
                    ->from('years')
                    ->where(['program_id' => $prog_id])
                    ->all();

                $out = [];
                foreach ($rows as $value) {
                    array_push($out, ['id' => $value['id'], 'name' => $value['year']]);
                }


                //$out = ArrayHelper::map(ProgrammeModule::find()->where(['program_id' => $prog_id])->all(), 'id', 'year');
                //$out = self::getSubCatList($cat_id); 
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                //$out = [['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']];
                echo Json::encode(['output' => $out, 'selected' => '']);

                return;
            }
        }
        echo Json::encode(['output' => '', 'selected' => '']);
    }

    /**
     * Deletes an existing Groups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if ($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                $this->findModel($id)->delete();

                return $this->redirect(['/personal/organization-groups']);
            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');

                return $this->redirect(['/personal/payer-certificates']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
            'title' => 'Удалить группу',
        ]);
    }

    public function actionInvoice()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $searchGroups = new GroupsInvoiceSearch();
        $searchGroups->organization_id = $organization['id'];

        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('invoice', [
            'searchGroups' => $searchGroups,
            'GroupsProvider' => $GroupsProvider,
        ]);

    }

    public function actionDec()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $searchGroups = new GroupsInvoiceSearch();
        $searchGroups->organization_id = $organization['id'];

        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('dec', [
            'searchGroups' => $searchGroups,
            'GroupsProvider' => $GroupsProvider,
        ]);

    }

    public function actionPreinvoice()
    {
        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        $searchGroups = new GroupsInvoiceSearch();
        $searchGroups->organization_id = $organization['id'];

        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('preinvoice', [
            'searchGroups' => $searchGroups,
            'GroupsProvider' => $GroupsProvider,
        ]);

    }
    /*
    public function actionFgroup()
    {
        $arr = array(220,
221,
223,
224,
225,
226,
227,
228,
229,
230,
231,
232,
233,
234,
235,
236,
237,
238,
243,
244,
245,
252,
259,
260,
265,
266,
272,
273,
292,
294,
295,
296,
301,
302,
303,
304,
305,
306,
307,
308,
309,
311,
313,
315,
318,
319);
    
    foreach ($arr as $groups) {
        $model = $this->findModel($groups);
        
        $year = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('years')
                    ->where(['program_id' => $model->program_id])
                    ->andWhere(['year' => 1])
                    ->one();
        
        $model->year_id = $year['id'];
        if ($model->save()) {
            echo "ok!<br>";
        }
        
    }
       
    }
*/
    /**
     * Finds the Groups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Groups the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Groups::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
