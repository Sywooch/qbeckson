<?php

namespace app\controllers;

use app\models\ContractsStatus1onlySearch;
use app\models\GroupClass;
use app\models\Groups;
use app\models\GroupsInvoiceSearch;
use app\models\GroupsPreinvoiceSearch;
use app\models\GroupsSearch;
use app\models\Organization;
use app\models\ProgrammeModule;
use app\models\Programs;
use app\models\UserIdentity;
use Yii;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;


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
                'class'   => VerbFilter::className(),
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
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Groups model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Groups model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
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

    public function actionContracts($id)
    {
        $model = $this->findModel($id);

        $Contracts1Search = new ContractsStatus1onlySearch();
        $Contracts1Search->group_id = $id;
        $ContractsProvider = $Contracts1Search->search(Yii::$app->request->queryParams);

        return $this->render('contracts', [
            'model'             => $model,
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

        $groupClasses = [];
        foreach (GroupClass::weekDays() as $key => $day) {
            $groupClasses[$key] = new GroupClass([
                'week_day' => $day
            ]);
        }

        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            $model->organization_id = $organization['id'];

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
                Yii::$app->session->setFlash(
                    'error',
                    'Продолжительность программы должна быть ' . $rows['month'] . ' месяцев.'
                );
            } else {
                Model::loadMultiple($groupClasses, Yii::$app->request->post());
                if ($model->validate() && Model::validateMultiple($groupClasses)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($model->save(false)) {
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
        }

        return $this->render('create', [
            'groupClasses' => $groupClasses,
            'model'        => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionSelectAddresses()
    {
        $out = '';
        if ($parents = Yii::$app->request->post('depdrop_parents')) {
            $moduleId = $parents[0];
            $model = ProgrammeModule::findOne($moduleId);
            $programModuleAddresses = $model->addresses;
            $out = [];
            foreach ($programModuleAddresses as $key => $value) {
                $out[] = ['id' => $value->address, 'name' => $value->address];
            }
        }

        return Json::encode(['output' => $out, 'selected' => '']);
    }

    public function actionNewgroup($id)
    {
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            $module = ProgrammeModule::findOne(['id' => $id]);
            if (!$module) {
                throw new NotFoundHttpException();
            }
            if ($module->program->verification === Programs::VERIFICATION_DENIED) {
                throw new ForbiddenHttpException();
            }
        }

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
        $programModuleAddresses = ArrayHelper::map($model->module->addresses, 'address', 'address');

        if ($model->load(Yii::$app->request->post())) {
            $rows2 = (new \yii\db\Query())
                ->select(['month'])
                ->from('years')
                ->where(['id' => $model->year_id])
                ->one();

            $d1 = strtotime($model->datestart);
            $d2 = strtotime($model->datestop);
            $diff = $d2 - $d1;
            $diff = $diff / (60 * 60 * 24 * 31);
            $month = floor($diff);

            if ($rows2['month'] < $month - 1 or $rows2['month'] > $month + 1) {
                Yii::$app->session->setFlash(
                    'error',
                    'Продолжительность программы должна быть ' . $rows2['month'] . ' месяцев.'
                );
            } else {
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

                            return $this->redirect(['programs/view' . ($model->program->isMunicipalTask ? '-task' : ''), 'id' => $model->program_id]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->render('newgroup', [
            'model'                  => $model,
            'groupClasses'           => $groupClasses,
            'programModuleAddresses' => $programModuleAddresses
        ]);
    }

    /**
     * Updates an existing Groups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $programModuleAddresses = ArrayHelper::map($model->module->addresses, 'address', 'address');

        $groupClasses = [];
        foreach (GroupClass::weekDays() as $key => $day) {
            foreach ($model->classes as $class) {
                if ($class->week_day === $day) {
                    $class->status = 1;
                    $groupClasses[$key] = $class;
                }
            }
            if (null === $groupClasses[$key]) {
                $groupClasses[$key] = new GroupClass([
                    'week_day' => $day
                ]);
            }
        }

        if ($model->load(Yii::$app->request->post())) {
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
                Yii::$app->session->setFlash(
                    'error',
                    'Продолжительность программы должна быть ' . $rows['month'] . ' месяцев.'
                );
            } else {
                Model::loadMultiple($groupClasses, Yii::$app->request->post());
                if ($model->validate() && Model::validateMultiple($groupClasses)) {
                    $flag = true;
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($model->save(false)) {
                            foreach ($groupClasses as $classModel) {
                                /** @var GroupClass $classModel */
                                if ($classModel->status) {
                                    $classModel->group_id = $model->id;
                                    if (!($flag = $classModel->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                } else {
                                    if (!$classModel->isNewRecord) {
                                        $classModel->delete();
                                    }
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();

                            return $this->redirect(['programs/view' . ($model->program->isMunicipalTask ? '-task' : ''), 'id' => $model->program_id]);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->render('update', [
            'model'                  => $model,
            'programModuleAddresses' => $programModuleAddresses,
            'groupClasses'           => $groupClasses,
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
     * Ставит статус "архив" группе если нет активных контрактов
     *
     * @param integer $id
     *
     * @throws NotFoundHttpException | ForbiddenHttpException
     * @return mixed
     */
    public function actionDelete($id)
    {
        /** @var $identity UserIdentity */
        $identity = Yii::$app->user->getIdentity();
        $user = $identity->getUser()->setShortLoginScenario();
        if ($user->load(Yii::$app->request->post())) {
            if ($user->validate()) {
                $group = $this->findModel($id);
                if ($group->setIsArchive()) {
                    Yii::$app->session->setFlash('success',
                        sprintf('Группа %s отправлена в архив', $group->name));

                    return $this->redirect(['/personal/organization-groups']);
                } else {
                    Yii::$app->session->setFlash('warning',
                        sprintf('Удалить группу %s нельзя. Есть заявки или договоры на обучение', $group->name));
                }
            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');
            }

            return $this->redirect(['/groups/contracts', 'id' => $id]);
        }
        throw new ForbiddenHttpException();
    }

    public function actionInvoice()
    {
        /**@var $organization Organization */
        $organization = Yii::$app->user->identity->organization;

        $searchGroups = new GroupsInvoiceSearch(['invoice' => true]);
        $searchGroups->organization_id = $organization->id;

        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('invoice', [
            'searchGroups'   => $searchGroups,
            'GroupsProvider' => $GroupsProvider,
        ]);

    }

    public function actionPreinvoice()
    {
        /**@var $organization Organization */
        $organization = Yii::$app->user->identity->organization;

        $searchGroups = new GroupsPreinvoiceSearch();
        $searchGroups->organization_id = $organization['id'];

        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('preinvoice', [
            'searchGroups'   => $searchGroups,
            'GroupsProvider' => $GroupsProvider,
        ]);

    }

    // Это для декабря (12 месяц)
    public function actionDec()
    {
        /**@var $organization Organization */
        $organization = Yii::$app->user->identity->organization;

        // TODO: в случае если месяц == 12 выводить договоры дата начала обучения по которым меньше чем 31 декабря пред. года
        $searchGroups = new GroupsInvoiceSearch();
        $searchGroups->organization_id = $organization['id'];

        $GroupsProvider = $searchGroups->search(Yii::$app->request->queryParams);

        return $this->render('dec', [
            'searchGroups'   => $searchGroups,
            'GroupsProvider' => $GroupsProvider,
        ]);

    }
}
