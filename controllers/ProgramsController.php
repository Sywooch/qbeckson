<?php

namespace app\controllers;

use app\assets\programsAsset\ProgramsAsset;
use app\models\AllProgramsSearch;
use app\models\AutoProlongation;
use app\models\Contracts;
use app\models\ContractsSearch;
use app\models\forms\ProgramAddressesForm;
use app\models\forms\ProgramSectionForm;
use app\models\forms\TaskTransferForm;
use app\models\Groups;
use app\models\Informs;
use app\models\Model;
use app\models\module\CertificateAccessModuleDecorator;
use app\models\module\ModuleViewDecorator;
use app\models\Organization;
use app\models\ProgrammeModule;
use app\models\Programs;
use app\models\programs\ProgramViewDecorator;
use app\models\ProgramsallSearch;
use app\models\ProgramsFile;
use app\models\ProgramsPreviusSearch;
use app\models\search\ProgramsSearch;
use app\models\UserIdentity;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * ProgramsController implements the CRUD actions for Programs model.
 */
class ProgramsController extends Controller
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
     * @param integer $id
     *
     * @return string|Response
     */
    public function actionAddAddresses($id)
    {
        $program = $this->findModel($id);
        $form = new ProgramAddressesForm($program);

        if ($form->load(Yii::$app->request->post())) {
            if ($form->save()) {
                Yii::$app->session->setFlash('success', 'Адреса успешно обновлены');

                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Что-то не так');
            }
        }

        return $this->render('add-addresses', [
            'model' => $form,
            'program' => $program,
        ]);
    }

    /**
     * Finds the Programs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Programs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Programs::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param integer $id
     *
     * @throws NotFoundHttpException
     *
     * @return string|Response
     */
    public function actionAddPhoto($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('add-picture', [
            'model' => $model
        ]);
    }

    /**
     * Lists all Programs models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgramsallSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionSearch()
    {
        $searchModel = new AllProgramsSearch();
        if (isset($_GET['org'])) {
            $searchModel->organization_id = $_GET['org'];
        }
        if (isset($_GET['name'])) {
            $searchModel->name = $_GET['name'];
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('search', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Lists all Programs models.
     * @return mixed
     */
    public function actionPrevius()
    {
        $searchModel = new ProgramsPreviusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('previus', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdateTask($id)
    {
        $program = $this->findModel($id);
        if (!$program->isMunicipalTask) {
            throw new BadRequestHttpException();
        }
        $model = new ProgramSectionForm($program);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Муниципальное задание успешно обновлено.');

            $this->redirect(['view-task', 'id' => $id]);
        }

        return $this->render('update-task', [
            'model' => $model,
        ]);
    }

    public function actionTransferTask($id)
    {
        $model = $this->findModel($id);
        if (!$model->isMunicipalTask || !$model->canTaskBeTransferred) {
            throw new BadRequestHttpException();
        }
        $model->setTransferParams();
        $modelYears = $model->years;
        $file = new ProgramsFile();

        return $this->render('update', [
            'strictAction' => ['/programs/update', 'id' => $model->id],
            'model' => $model,
            'file' => $file,
            'modelYears' => (empty($modelYears)) ? [new ProgrammeModule(['scenario' => ProgrammeModule::SCENARIO_CREATE])] : $modelYears
        ]);
    }

    public function actionTransferProgramme($id)
    {
        $model = $this->findModel($id);
        if ($model->isMunicipalTask || !$model->canProgrammeBeTransferred) {
            throw new BadRequestHttpException();
        }
        $model->setTransferParams(false);

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Вы успешно перевели программу на муниципальное задание в реестр "Ожидающие рассмотрения"');

            return $this->redirect(['/programs/view-task', 'id' => $model->id]);
        } else {
            Yii::$app->session->setFlash('danger', 'Произошла ошибка в процессе переноса.');

            return $this->redirect(['/programs/view', 'id' => $model->id]);
        }
    }

    public function actionRefuseTask($id)
    {
        $program = $this->findModel($id);
        if (!$program->isMunicipalTask) {
            throw new BadRequestHttpException();
        }
        $model = new ProgramSectionForm($program);
        $model->scenario = ProgramSectionForm::SCENARIO_REFUSE;

        if ($model->load(Yii::$app->request->post()) && $model->refuse()) {
            Yii::$app->session->setFlash('success', 'Задание успешно отклонено.');

            $this->redirect(['/personal/payer-municipal-task']);
        }

        return $this->render('refuse-task', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Programs model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('viewProgramme', ['id' => $id])) {
            throw new ForbiddenHttpException('Нет прав на просмотр программы.');
        }
        $modelOriginal = $this->findModel($id);
        $model = ProgramViewDecorator::decorate($modelOriginal);
        $modules = ModuleViewDecorator::decorateMultiple($model->modules);

        if (!$model->isActive) {
            throw new NotFoundHttpException();
        }

        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            $modules = CertificateAccessModuleDecorator::decorateMultiple($modules);
        }

        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
            || Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
        ) {
            if ($model->verification === Programs::VERIFICATION_DENIED) {
                Yii::$app->session->setFlash(
                    'danger',
                    $this->renderPartial(
                        'informers/list_of_reazon',
                        [
                            'dataProvider' => new ActiveDataProvider(
                                [
                                    'query' => $model->getInforms()
                                        ->andWhere(['status' => Programs::VERIFICATION_DENIED]),
                                    'sort' => ['defaultOrder' => ['date' => SORT_DESC]]
                                ]
                            )
                        ]
                    )
                );
            }
        }


        ProgramsAsset::register($this->view);

        return $this->render(
            'view/view',
            ['model' => $model, 'modules' => $modules]
        );
    }

    /**
     * Displays a single Programs model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionViewTask($id)
    {
        /** @var $user UserIdentity */
        $user = Yii::$app->user->identity;
        $modelOriginal = $this->findModel($id);
        $model = ProgramViewDecorator::decorate($modelOriginal);

        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
            && $user->organization->id !== $model->organization_id
        ) {
            throw new ForbiddenHttpException('Нет доступа');
        }
        // При первом просмотре от плательщика меняем статус, чтобы запретить редактирование организации
        if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)
            && $model->verification === Programs::VERIFICATION_UNDEFINED
        ) {
            $model->verification = Programs::VERIFICATION_WAIT;
            $model->save(false, ['verification']);
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
            || Yii::$app->user->can(UserIdentity::ROLE_PAYER)
        ) {
            if ($model->verification === Programs::VERIFICATION_DENIED) {
                Yii::$app->session->setFlash(
                    'danger',
                    $this->renderPartial(
                        'informers/list_of_reazon',
                        [
                            'dataProvider' => new ActiveDataProvider(
                                [
                                    'query' => $model->getInforms()
                                        ->andWhere(['status' => Programs::VERIFICATION_DENIED]),
                                    'sort' => ['defaultOrder' => ['date' => SORT_DESC]]
                                ]
                            )
                        ]
                    )
                );
            }
        }

        ProgramsAsset::register($this->view);

        return $this->render('task/view', ['model' => $model, 'cooperate' => null]);
    }

    /**
     * Creates a new Programs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($isTask = null)
    {
        $model = new Programs([
            'is_municipal_task' => empty($isTask) ? null : 1,
        ]);

        if ($model->getIsMunicipalTask()
            && !Yii::$app->user->identity->organization->suborderPayer
        ) {
            throw new ForbiddenHttpException();
        }

        $file = new ProgramsFile();
        $modelsYears = [
            new ProgrammeModule(
                [
                    'kvfirst' => 'Педагог, обладающий соответствующей квалификацией',
                    'scenario' =>
                        $model->isMunicipalTask
                            ? ProgrammeModule::SCENARIO_MUNICIPAL_TASK
                            : null

                ]
            )
        ];

        if ($model->load(Yii::$app->request->post())) {
            $modelsYears = Model::createMultiple(
                ProgrammeModule::classname(),
                [],
                $model->asDraft
                    ? ProgrammeModule::SCENARIO_DRAFT
                    : (
                $model->isMunicipalTask
                    ? ProgrammeModule::SCENARIO_MUNICIPAL_TASK
                    : null
                )
            );
            Model::loadMultiple($modelsYears, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                if ($model->asDraft) {
                    $model->setScenario(Programs::SCENARIO_DRAFT);
                    $modelsYears = array_map(function (ProgrammeModule $module) {
                        $module->setScenario(ProgrammeModule::SCENARIO_DRAFT);

                        return $module;
                    }, $modelsYears);
                    $model->verification = Programs::VERIFICATION_DRAFT;
                }

                return $this->asJson(ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsYears),
                    ActiveForm::validate($model)
                ));
            }

            /**@var $userIdentity UserIdentity */
            $userIdentity = Yii::$app->user->identity;
            $organization = $userIdentity->organization;
            $model->organization_id = $organization->id;
            if ($model->asDraft) {
                $model->setScenario(Programs::SCENARIO_DRAFT);
                $model->verification = Programs::VERIFICATION_DRAFT;
            } else {
                $model->verification = Programs::VERIFICATION_UNDEFINED;
            }
            $model->open = 0;
            if ($model->ovz == 2) {
                if (!empty($model->zab)) {
                    $model->zab = implode(',', $model->zab);
                }
            }

            if (Yii::$app->request->isPost) {
                $file->docFile = UploadedFile::getInstance($file, 'docFile');

                if (empty($file->docFile) && !$model->isADraft()) {
                    Yii::$app->session->setFlash('error', 'Пожалуйста, добавьте файл образовательной программы.');

                    return $this->render('create', [
                        'model' => $model,
                        'file' => $file,
                        'modelsYears' => $modelsYears,
                    ]);
                } elseif (empty($file->docFile)) {
                    $model->link = null;
                } else {
                    $datetime = time();
                    $filename = 'program-' . $organization['id'] . '-' . $datetime . '.' . $file->docFile->extension;
                    $model->link = $filename;
                }
                $model->year = count($modelsYears);
                if (($model->link && $file->upload($filename)) || $model->isADraft()) {
                    $valid = $model->validate();
                    $valid = Model::validateMultiple($modelsYears) && $valid;
                    if ($valid) {
                        $transaction = \Yii::$app->db->beginTransaction();
                        try {
                            if ($flag = $model->save(false)) {
                                $i = 1;
                                /**
                                 * @var $modelYears ProgrammeModule
                                 */
                                foreach ($modelsYears as $modelYears) {
                                    $modelYears->program_id = $model->id;
                                    $modelYears->year = $i;

                                    $p3 = Yii::$app->coefficient->data->p3v;

                                    $mun = (new \yii\db\Query())
                                        ->select(
                                            [
                                                'pc', 'zp', 'cozp', 'stav', 'costav', 'dop',
                                                'codop', 'uvel', 'couvel', 'otch', 'cootch',
                                                'otpusk', 'cootpusk', 'polezn', 'copolezn',
                                                'nopc', 'conopc', 'rob', 'corob', 'tex', 'cotex',
                                                'est', 'coest', 'fiz', 'cofiz', 'xud', 'coxud',
                                                'tur', 'cotur', 'soc', 'cosoc'
                                            ]
                                        )
                                        ->from('mun')
                                        ->where(['id' => $model->mun])
                                        ->one();

                                    if ($model->ground == 1) {
                                        $p5 = $mun['pc'];
                                        $p6 = $mun['zp'];
                                        $p12 = $mun['stav'];
                                        $p7 = $mun['dop'];
                                        $p8 = $mun['uvel'];
                                        $p9 = $mun['otch'];
                                        $p10 = $mun['otpusk'];
                                        $p11 = $mun['polezn'];
                                        $p4 = $mun['nopc'];
                                        if ($model->directivity == 'Техническая (робототехника)') {
                                            $p1 = $mun['rob'];
                                        }
                                        if ($model->directivity == 'Техническая (иная)') {
                                            $p1 = $mun['tex'];
                                        }
                                        if ($model->directivity == 'Естественнонаучная') {
                                            $p1 = $mun['est'];
                                        }
                                        if ($model->directivity == 'Физкультурно-спортивная') {
                                            $p1 = $mun['fiz'];
                                        }
                                        if ($model->directivity == 'Художественная') {
                                            $p1 = $mun['xud'];
                                        }
                                        if ($model->directivity == 'Туристско-краеведческая') {
                                            $p1 = $mun['tur'];
                                        }
                                        if ($model->directivity == 'Социально-педагогическая') {
                                            $p1 = $mun['soc'];
                                        }
                                    }

                                    if ($model->ground == 2) {
                                        $p5 = $mun['pc'];
                                        $p6 = $mun['cozp'];
                                        $p12 = $mun['costav'];
                                        $p7 = $mun['codop'];
                                        $p8 = $mun['couvel'];
                                        $p9 = $mun['cootch'];
                                        $p10 = $mun['cootpusk'];
                                        $p11 = $mun['copolezn'];
                                        $p4 = $mun['conopc'];
                                        if ($model->directivity == 'Техническая (робототехника)') {
                                            $p1 = $mun['corob'];
                                        }
                                        if ($model->directivity == 'Техническая (иная)') {
                                            $p1 = $mun['cotex'];
                                        }
                                        if ($model->directivity == 'Естественнонаучная') {
                                            $p1 = $mun['coest'];
                                        }
                                        if ($model->directivity == 'Физкультурно-спортивная') {
                                            $p1 = $mun['cofiz'];
                                        }
                                        if ($model->directivity == 'Художественная') {
                                            $p1 = $mun['coxud'];
                                        }
                                        if ($model->directivity == 'Туристско-краеведческая') {
                                            $p1 = $mun['cotur'];
                                        }
                                        if ($model->directivity == 'Социально-педагогическая') {
                                            $p1 = $mun['cosoc'];
                                        }
                                    }

                                    $p14 = Yii::$app->coefficient->data->weekmonth;
                                    $p16 = Yii::$app->coefficient->data->norm;
                                    $p15 = Yii::$app->coefficient->data->pk;
                                    $p13 = Yii::$app->coefficient->data->weekyear;
                                    $p21 = Yii::$app->coefficient->data->p21v;
                                    $p22 = Yii::$app->coefficient->data->p22v;
                                    if (!$model->isADraft()) {
                                        $childAverage = $modelYears->getChildrenAverage() ? $modelYears->getChildrenAverage() : ($modelYears->maxchild + $modelYears->minchild) / 2;
                                        $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / ($childAverage)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * ($childAverage)) / ($p11 * ($childAverage))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * ($childAverage)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * ($childAverage))) * $p5;

                                        $modelYears->normative_price = round($nprice);
                                        $modelYears->previus = 1;
                                    }
                                    $i++;
                                    if (!($flag = $modelYears->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                            if ($flag) {
                                if (!$model->isADraft()) {
                                    $informs = new Informs();
                                    $informs->program_id = $model->id;
                                    $informs->text = 'Поступила программа на сертификацию';
                                    $informs->from = UserIdentity::ROLE_ORGANIZATION_ID;
                                    $informs->date = date("Y-m-d");
                                    $informs->read = 0;
                                    $flag = $flag && $informs->save();
                                }
                                $flag && ($transaction->commit() || true)
                                || $transaction->rollBack();

                                return $this->redirect(
                                    $model->isMunicipalTask
                                        ? ['/personal/organization-municipal-task']
                                        : ['/personal/organization-programs']
                                );
                            }
                        } catch (\Exception $e) {
                            Yii::trace($e->getMessage());
                            $transaction->rollBack();
                        }
                    }
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'file' => $file,
            'modelsYears' => (empty($modelsYears)) ? [new ProgrammeModule] : $modelsYears
        ]);
    }

    public function actionVerificate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)
            && count(array_filter($model->informs, function ($val) {
                /**@var $val Informs */
                return $val->status === Programs::VERIFICATION_DENIED;
            })) > 0
        ) {
            Yii::$app->session->setFlash(
                'danger',
                $this->renderPartial(
                    'informers/list_of_reazon',
                    [
                        'dataProvider' => new ActiveDataProvider(
                            [
                                'query' => $model->getInforms()
                                    ->andWhere(['status' => $model::VERIFICATION_DENIED]),
                                'sort' => ['defaultOrder' => ['date' => SORT_DESC]]
                            ]
                        )
                    ]
                )
            );
        }
        if ($model->verification !== Programs::VERIFICATION_WAIT
            && $model->verification !== Programs::VERIFICATION_DONE
        ) {
            $model->verification = Programs::VERIFICATION_WAIT;
            $model->save();
        }

        return $this->render('verificate/verificate', [
            'model' => $model,
        ]);
    }

    public function actionSave($id)
    {
        $model = $this->findModel($id);

        if ($model->directivity == 'Техническая (робототехника)') {
            $model->limit = Yii::$app->coefficient->data->blimrob * $model->year;
        }
        if ($model->directivity == 'Техническая (иная)') {
            $model->limit = Yii::$app->coefficient->data->blimtex * $model->year;
        }
        if ($model->directivity == 'Естественнонаучная') {
            $model->limit = Yii::$app->coefficient->data->blimest * $model->year;
        }
        if ($model->directivity == 'Физкультурно-спортивная') {
            $model->limit = Yii::$app->coefficient->data->blimfiz * $model->year;
        }
        if ($model->directivity == 'Художественная') {
            $model->limit = Yii::$app->coefficient->data->blimxud * $model->year;
        }
        if ($model->directivity == 'Туристско-краеведческая') {
            $model->limit = Yii::$app->coefficient->data->blimtur * $model->year;
        }
        if ($model->directivity == 'Социально-педагогическая') {
            $model->limit = Yii::$app->coefficient->data->blimsoc * $model->year;
        }

        $model->verification = Programs::VERIFICATION_DONE;
        array_map(
            function (ProgrammeModule $module) {
                $module->verification = ProgrammeModule::VERIFICATION_DONE;
                $module->save(false);
            },
            $model->modules
        );
        //return var_dump($model->limit);
        if ($model->save()) {
            $informs = new Informs();
            $informs->program_id = $model->id;
            $informs->prof_id = $model->organization_id;
            $informs->text = 'Сертифицированна программа';
            $informs->from = 3;
            $informs->date = date("Y-m-d");
            $informs->read = 0;
            if ($informs->save()) {
                return $this->redirect('/personal/operator-programs');
            }
        }
    }

    public function actionCertificate($id)
    {
        $model = $this->findModel($id);
        $modelsYears = $model->years;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsYears, 'id', 'id');
            $modelsYears = Model::createMultiple(ProgrammeModule::classname(), $modelsYears);
            Model::loadMultiple($modelsYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {

                return $this->asJson(ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsYears),
                    ActiveForm::validate($model)
                ));
            }

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            ProgrammeModule::deleteAll(['id' => $deletedIDs]);
                        }
                        /**
                         * @var $modelYears ProgrammeModule
                         */
                        foreach ($modelsYears as $modelYears) {


                            if ($model->p3z == 1) {
                                $p3r = 'p3v';
                            }
                            if ($model->p3z == 2) {
                                $p3r = 'p3s';
                            }
                            if ($model->p3z == 3) {
                                $p3r = 'p3n';
                            }

                            $p3 = Yii::$app->coefficient->data->$p3r;

                            $mun = (new \yii\db\Query())
                                ->select(['pc', 'zp', 'cozp', 'stav', 'costav', 'dop', 'codop', 'uvel', 'couvel', 'otch', 'cootch', 'otpusk', 'cootpusk', 'polezn', 'copolezn', 'nopc', 'conopc', 'rob', 'corob', 'tex', 'cotex', 'est', 'coest', 'fiz', 'cofiz', 'xud', 'coxud', 'tur', 'cotur', 'soc', 'cosoc'])
                                ->from('mun')
                                ->where(['id' => $model->mun])
                                ->one();

                            if ($model->ground == 1) {
                                $p5 = $mun['pc'];
                                $p6 = $mun['zp'];
                                $p12 = $mun['stav'];
                                $p7 = $mun['dop'];
                                $p8 = $mun['uvel'];
                                $p9 = $mun['otch'];
                                $p10 = $mun['otpusk'];
                                $p11 = $mun['polezn'];
                                $p4 = $mun['nopc'];
                                if ($model->directivity == 'Техническая (робототехника)') {
                                    $p1 = $mun['rob'];
                                }
                                if ($model->directivity == 'Техническая (иная)') {
                                    $p1 = $mun['tex'];
                                }
                                if ($model->directivity == 'Естественнонаучная') {
                                    $p1 = $mun['est'];
                                }
                                if ($model->directivity == 'Физкультурно-спортивная') {
                                    $p1 = $mun['fiz'];
                                }
                                if ($model->directivity == 'Художественная') {
                                    $p1 = $mun['xud'];
                                }
                                if ($model->directivity == 'Туристско-краеведческая') {
                                    $p1 = $mun['tur'];
                                }
                                if ($model->directivity == 'Социально-педагогическая') {
                                    $p1 = $mun['soc'];
                                }
                            }

                            if ($model->ground == 2) {
                                $p5 = $mun['pc'];
                                $p6 = $mun['cozp'];
                                $p12 = $mun['costav'];
                                $p7 = $mun['codop'];
                                $p8 = $mun['couvel'];
                                $p9 = $mun['cootch'];
                                $p10 = $mun['cootpusk'];
                                $p11 = $mun['copolezn'];
                                $p4 = $mun['conopc'];
                                if ($model->directivity == 'Техническая (робототехника)') {
                                    $p1 = $mun['corob'];
                                }
                                if ($model->directivity == 'Техническая (иная)') {
                                    $p1 = $mun['cotex'];
                                }
                                if ($model->directivity == 'Естественнонаучная') {
                                    $p1 = $mun['coest'];
                                }
                                if ($model->directivity == 'Физкультурно-спортивная') {
                                    $p1 = $mun['cofiz'];
                                }
                                if ($model->directivity == 'Художественная') {
                                    $p1 = $mun['coxud'];
                                }
                                if ($model->directivity == 'Туристско-краеведческая') {
                                    $p1 = $mun['cotur'];
                                }
                                if ($model->directivity == 'Социально-педагогическая') {
                                    $p1 = $mun['cosoc'];
                                }
                            }

                            $p14 = Yii::$app->coefficient->data->weekmonth;
                            $p16 = Yii::$app->coefficient->data->norm;
                            $p15 = Yii::$app->coefficient->data->pk;
                            $p13 = Yii::$app->coefficient->data->weekyear;

                            if ($modelYears->p21z == 1) {
                                $p1y = 'p21v';
                            }
                            if ($modelYears->p21z == 2) {
                                $p1y = 'p21s';
                            }
                            if ($modelYears->p21z == 3) {
                                $p1y = 'p21o';
                            }
                            $p21 = Yii::$app->coefficient->data->$p1y;

                            if ($modelYears->p22z == 1) {
                                $p2y = 'p22v';
                            }
                            if ($modelYears->p22z == 2) {
                                $p2y = 'p22s';
                            }
                            if ($modelYears->p22z == 3) {
                                $p2y = 'p22o';
                            }
                            $p22 = Yii::$app->coefficient->data->$p2y;

                            $childrenAverage = $modelYears->getChildrenAverage() ? $modelYears->getChildrenAverage() : ($modelYears->maxchild + $modelYears->minchild) / 2;
                            $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / ($childrenAverage)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * ($childrenAverage)) / ($p11 * ($childrenAverage))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * ($childrenAverage)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * ($childrenAverage))) * $p5;

                            $modelYears->normative_price = round($nprice);


                            if (!($flag = $modelYears->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->redirect(['certificate', 'id' => $id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        } else {
            return $this->render('cert', [
                'model' => $model,
                'modelsYears' => (empty($modelsYears)) ? [new ProgrammeModule] : $modelsYears
            ]);
        }
    }

    public function actionNewnormprice($id)
    {
        $model = $this->findModel($id);
        $modelsYears = $model->years;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsYears, 'id', 'id');
            $modelsYears = Model::createMultiple(ProgrammeModule::classname(), $modelsYears);
            Model::loadMultiple($modelsYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {

                return $this->asJson(ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsYears),
                    ActiveForm::validate($model)
                ));
            }

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            ProgrammeModule::deleteAll(['id' => $deletedIDs]);
                        }
                        /**
                         * @var $modelYears ProgrammeModule
                         */
                        foreach ($modelsYears as $modelYears) {

                            if ($model->p3z == 1) {
                                $p3r = 'p3v';
                            }
                            if ($model->p3z == 2) {
                                $p3r = 'p3s';
                            }
                            if ($model->p3z == 3) {
                                $p3r = 'p3n';
                            }
                            $p3 = Yii::$app->coefficient->data->$p3r;

                            $mun = (new \yii\db\Query())
                                ->select(['pc', 'zp', 'cozp', 'stav', 'costav', 'dop', 'codop', 'uvel', 'couvel', 'otch', 'cootch', 'otpusk', 'cootpusk', 'polezn', 'copolezn', 'nopc', 'conopc', 'rob', 'corob', 'tex', 'cotex', 'est', 'coest', 'fiz', 'cofiz', 'xud', 'coxud', 'tur', 'cotur', 'soc', 'cosoc'])
                                ->from('mun')
                                ->where(['id' => $model->mun])
                                ->one();

                            if ($model->ground == 1) {
                                $p5 = $mun['pc'];
                                $p6 = $mun['zp'];
                                $p12 = $mun['stav'];
                                $p7 = $mun['dop'];
                                $p8 = $mun['uvel'];
                                $p9 = $mun['otch'];
                                $p10 = $mun['otpusk'];
                                $p11 = $mun['polezn'];
                                $p4 = $mun['nopc'];
                                if ($model->directivity == 'Техническая (робототехника)') {
                                    $p1 = $mun['rob'];
                                }
                                if ($model->directivity == 'Техническая (иная)') {
                                    $p1 = $mun['tex'];
                                }
                                if ($model->directivity == 'Естественнонаучная') {
                                    $p1 = $mun['est'];
                                }
                                if ($model->directivity == 'Физкультурно-спортивная') {
                                    $p1 = $mun['fiz'];
                                }
                                if ($model->directivity == 'Художественная') {
                                    $p1 = $mun['xud'];
                                }
                                if ($model->directivity == 'Туристско-краеведческая') {
                                    $p1 = $mun['tur'];
                                }
                                if ($model->directivity == 'Социально-педагогическая') {
                                    $p1 = $mun['soc'];
                                }
                            }

                            if ($model->ground == 2) {
                                $p5 = $mun['pc'];
                                $p6 = $mun['cozp'];
                                $p12 = $mun['costav'];
                                $p7 = $mun['codop'];
                                $p8 = $mun['couvel'];
                                $p9 = $mun['cootch'];
                                $p10 = $mun['cootpusk'];
                                $p11 = $mun['copolezn'];
                                $p4 = $mun['conopc'];
                                if ($model->directivity == 'Техническая (робототехника)') {
                                    $p1 = $mun['corob'];
                                }
                                if ($model->directivity == 'Техническая (иная)') {
                                    $p1 = $mun['cotex'];
                                }
                                if ($model->directivity == 'Естественнонаучная') {
                                    $p1 = $mun['coest'];
                                }
                                if ($model->directivity == 'Физкультурно-спортивная') {
                                    $p1 = $mun['cofiz'];
                                }
                                if ($model->directivity == 'Художественная') {
                                    $p1 = $mun['coxud'];
                                }
                                if ($model->directivity == 'Туристско-краеведческая') {
                                    $p1 = $mun['cotur'];
                                }
                                if ($model->directivity == 'Социально-педагогическая') {
                                    $p1 = $mun['cosoc'];
                                }
                            }

                            $p14 = Yii::$app->coefficient->data->weekmonth;
                            $p16 = Yii::$app->coefficient->data->norm;
                            $p15 = Yii::$app->coefficient->data->pk;
                            $p13 = Yii::$app->coefficient->data->weekyear;

                            if ($modelYears->p21z == 1) {
                                $p1y = 'p21v';
                            }
                            if ($modelYears->p21z == 2) {
                                $p1y = 'p21s';
                            }
                            if ($modelYears->p21z == 3) {
                                $p1y = 'p21o';
                            }

                            $p21 = Yii::$app->coefficient->data->$p1y;

                            if ($modelYears->p22z == 1) {
                                $p2y = 'p22v';
                            }
                            if ($modelYears->p22z == 2) {
                                $p2y = 'p22s';
                            }
                            if ($modelYears->p22z == 3) {
                                $p2y = 'p22o';
                            }

                            $p22 = Yii::$app->coefficient->data->$p2y;

                            $childAverage = $modelYears->getChildrenAverage() ? $modelYears->getChildrenAverage() : ($modelYears->maxchild + $modelYears->minchild) / 2;
                            $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / ($childAverage)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * ($childAverage)) / ($p11 * ($childAverage))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * ($childAverage)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * ($childAverage))) * $p5;

                            $modelYears->normative_price = round($nprice);

                            if (!($flag = $modelYears->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->redirect(['newnormprice', 'id' => $id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        } else {
            return $this->render('newnormprice', [
                'model' => $model,
                'modelsYears' => (empty($modelsYears)) ? [new ProgrammeModule] : $modelsYears
            ]);
        }
    }


    public function actionNormpricesave($id)
    {

        $model = ProgrammeModule::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();

            $programs = (new \yii\db\Query())
                ->select(['verification'])
                ->from('programs')
                ->where(['id' => $model->program_id])
                ->one();

            if ($programs['verification'] == 1) {
                return $this->redirect(['certificate', 'id' => $model->program_id]);
            } else {
                return $this->redirect(['newnormprice', 'id' => $model->program_id]);
            }
        }

        return $this->render('newnormpricesave', [
            'title' => null,
            'model' => $model,
        ]);
    }

    public function actionCertificateold($id)
    {
        $model = $this->findModel($id);


        $params = (new \yii\db\Query())
            ->select(['id', 'hours', 'hoursindivid', 'hoursdop', 'minchild', 'maxchild', 'p21z', 'p22z'])
            ->from('years')
            ->where(['program_id' => $model->id])
            ->all();

        $year = [];
        $i = 0;
        foreach ($params as $param) {
            $year[$i] = ProgrammeModule::findOne($param['id']);

            if ($year[$i]->load(Yii::$app->request->post())) {
                if ($i == 0) {
                    return var_dump($year);
                }
                $year[$i]->save();
            }
            $i++;
        }

        return $this->render('certificate', [
            'model' => $model,
            'year' => $year,
        ]);
    }

    public function actionDecertificate($id)
    {
        $model = $this->findModel($id);
        $informs = new Informs();

        if ($informs->load(Yii::$app->request->post())) {
            $model->verification = Programs::VERIFICATION_DENIED;

            if ($model->save()) {
                $informs->program_id = $model->id;
                $informs->prof_id = $model->organization_id;
                //$informs->text = 'Программа не сертифицированна. Причина: '.$informs->dop;
                $informs->text = $informs->dop;
                $informs->from = UserIdentity::ROLE_OPERATOR_ID;
                $informs->status = Programs::VERIFICATION_DENIED;
                $informs->date = date("Y-m-d");
                $informs->read = 0;
                $informs->save();

                return $this->redirect($model->getIsMunicipalTask() ? '/personal/payer-municipal-task' : '/personal/operator-programs');
            }
        }

        return $this->render('/informs/comment', [
            'informs' => $informs,
            'model' => $model,
        ]);
    }

    public function actionCertificateok($year)
    {
        $id = 1;

        return $this->render('/programs/viewprice', ['year' => $year, 'id' => $id]);
    }

    public function actionProperty($id)
    {
        $model = $this->findModel($id);
        $modelsYears = $model->years;

        $oldIDs = ArrayHelper::map($modelsYears, 'id', 'id');
        $modelYears = Model::createMultiple(ProgrammeModule::classname(), $modelsYears);
        Model::loadMultiple($modelsYears, Yii::$app->request->post());

        if (Yii::$app->request->isPost) {
            if ($model->verification == Programs::VERIFICATION_WAIT) {
                Yii::$app->session->setFlash('error', 'Редактирование недоступно, программа проходит сертификацию.');

                return $this->redirect(['/personal/organization-programs']);
            }

            foreach ($modelsYears as $modelYears) {
                $modelYears->save();
            }

            return $this->redirect(['/personal/organization-programs']);

        } else {
            return $this->render('open', [
                'modelsYears' => (empty($modelsYears)) ? [new ProgrammeModule] : $modelsYears,
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Programs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modelOrigin = $this->findModel($id);
        $model = ProgramViewDecorator::decorate($modelOrigin);
        if (!$model->isActive) {
            throw new NotFoundHttpException();
        }
        $modelYears = $model->years;
        if ($model->isMunicipalTask && !$model->asDraft) {
            foreach ($modelYears as $index => $item) {
                $modelYears[$index]->scenario = ProgrammeModule::SCENARIO_MUNICIPAL_TASK;
            }
        } elseif ($model->asDraft) {
            foreach ($modelYears as $index => $item) {
                $modelYears[$index]->scenario = ProgrammeModule::SCENARIO_DRAFT;
            }
        }

        $file = new ProgramsFile();
        /** @var $organisation Organization */
        $model->zab = explode(',', $model->zab);
        $organization = Yii::$app->user->identity->organization;
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
            && $organization->id !== $model->organization_id
        ) {
            throw new ForbiddenHttpException('Нет доступа');
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->verification == Programs::VERIFICATION_WAIT) {
                Yii::$app->session->setFlash('error', 'Редактирование недоступно, программа проходит сертификацию.');

                return $this->redirect(['/personal/organization-programs']);
            }
            $file->docFile = UploadedFile::getInstance($file, 'docFile');
            if ($file->docFile) {
                $datetime = microtime(true); // date("Y-m-d-G-i-s");
                $filename = 'program-' . $organization['id'] . '-' . $datetime . '.' . $file->docFile->extension;
                $model->link = $filename;
                $file->upload($filename);
            }
            if ($model->asDraft) {
                $model->setScenario(Programs::SCENARIO_DRAFT);
                $model->verification = Programs::VERIFICATION_DRAFT;
            } else {
                $model->verification = Programs::VERIFICATION_UNDEFINED;
            }
            $model->open = 0;
            if ($model->zab) {
                $model->zab = implode(',', $model->zab);
            }

            $oldIDs = ArrayHelper::map($modelYears, 'id', 'id');
            $modelYears = Model::createMultiple(
                ProgrammeModule::classname(),
                $modelYears,
                $model->asDraft
                    ? ProgrammeModule::SCENARIO_DRAFT :
                    ($model->isMunicipalTask ? ProgrammeModule::SCENARIO_MUNICIPAL_TASK : null)
            );
            Model::loadMultiple($modelYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                return $this->asJson(ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelYears),
                    ActiveForm::validate($model)
                ));
            }

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            ProgrammeModule::deleteAll(['id' => $deletedIDs]);
                        }
                        $i = 1;
                        foreach ($modelYears as $modelYear) {
                            $modelYear->program_id = $model->id;
                            $modelYear->year = $i;

                            if (!($flag = $modelYear->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                            $i++;
                        }
                    }
                    if ($flag) {
                        if (!$model->isADraft()) {
                            $informs = new Informs();
                            $informs->program_id = $model->id;
                            $informs->text = 'Отредактирована программа для сертификации';
                            $informs->from = 1;
                            $informs->date = date("Y-m-d");
                            $informs->read = 0;
                            $flag = $flag && $informs->save();
                        }
                        ($flag && ($transaction->commit() || true))
                        || $transaction->rollBack();

                        return $this->redirect(
                            $model->isMunicipalTask
                                ? ['/personal/organization-municipal-task']
                                : ['/personal/organization-programs']
                        );
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'file' => $file,
                'modelYears' => (empty($modelYears))
                    ? [new ProgrammeModule(['scenario' => ProgrammeModule::SCENARIO_CREATE])]
                    : $modelYears,
                'strictAction' => null,
            ]);
        }
    }

    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $modelYears = $model->years;
        $file = new ProgramsFile();
        $model->zab = explode(',', $model->zab);

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelYears, 'id', 'id');
            $modelYears = Model::createMultiple(ProgrammeModule::classname(), $modelYears);
            Model::loadMultiple($modelYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {

                return $this->asJson(ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelYears),
                    ActiveForm::validate($model)
                ));
            }

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            ProgrammeModule::deleteAll(['id' => $deletedIDs]);
                        }
                        $i = 1;
                        foreach ($modelYears as $modelYears) {
                            $modelYears->program_id = $model->id;
                            $modelYears->year = $i;
                            if (!($flag = $modelYears->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                            $i++;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->redirect(['/personal/operator-programs']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        } else {
            return $this->render('edit', [
                'model' => $model,
                'file' => $file,
                'modelYears' => (empty($modelYears)) ? [new ProgrammeModule] : $modelYears
            ]);
        }
    }

    public function actionNewlimit($id)
    {
        $model = $this->findModel($id);

        if ($model->rating < Yii::$app->coefficient->data->ngrp) {
            $coef_raiting = 0;
        }
        if ($model->rating == null) {
            $coef_raiting = 1;
        }

        // return var_dump($coef_raiting);
        if ($model->rating >= Yii::$app->coefficient->data->ngrp and $model->rating < Yii::$app->coefficient->data->sgrp) {
            $coef_raiting = ($model->rating - Yii::$app->coefficient->data->ppchr1) / Yii::$app->coefficient->data->ppzm1;
        }
        if ($model->rating >= Yii::$app->coefficient->data->sgrp and $model->rating < Yii::$app->coefficient->data->vgrp) {
            $coef_raiting = 1;
        }
        if ($model->rating >= Yii::$app->coefficient->data->vgrp) {
            $coef_raiting = ($model->rating - Yii::$app->coefficient->data->ppchr2) / Yii::$app->coefficient->data->ppzm2;
        }


        if ($model->directivity == 'Техническая (робототехника)') {
            $model->limit = round((Yii::$app->coefficient->data->blimrob * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Техническая (иная)') {
            $model->limit = round((Yii::$app->coefficient->data->blimtex * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Естественнонаучная') {
            $model->limit = round((Yii::$app->coefficient->data->blimest * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Физкультурно-спортивная') {
            $model->limit = round((Yii::$app->coefficient->data->blimfiz * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Художественная') {
            $model->limit = round((Yii::$app->coefficient->data->blimxud * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Туристско-краеведческая') {
            $model->limit = round((Yii::$app->coefficient->data->blimtur * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Социально-педагогическая') {
            $model->limit = round((Yii::$app->coefficient->data->blimsoc * $coef_raiting) * $model->year, 0);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['/programs/view', 'id' => $model->id]);
        }

        return $this->render('newlimit', [
            'model' => $model,
        ]);

    }

    public function actionAlllimit()
    {
        $programs = (new \yii\db\Query())
            ->select(['`programs`.id'])
            ->from('programs')
            ->join('INNER JOIN', 'mun', '`mun`.id = `programs`.mun')
            ->andWhere('`mun`.operator_id = ' . Yii::$app->operator->identity->id)
            ->column();

        foreach ($programs as $program_id) {

            $model = $this->findModel($program_id);


            if ($model->rating < Yii::$app->coefficient->data->ngrp) {
                $coef_raiting = 0;
            }
            if ($model->rating == null) {
                $coef_raiting = 1;
            }
            if ($model->rating >= Yii::$app->coefficient->data->ngrp and $model->rating < Yii::$app->coefficient->data->sgrp) {
                $coef_raiting = ($model->rating - Yii::$app->coefficient->data->ppchr1) / Yii::$app->coefficient->data->ppzm1;
            }
            if ($model->rating >= Yii::$app->coefficient->data->sgrp and $model->rating < Yii::$app->coefficient->data->vgrp) {
                $coef_raiting = 1;
            }
            if ($model->rating >= Yii::$app->coefficient->data->vgrp) {
                $coef_raiting = ($model->rating - Yii::$app->coefficient->data->ppchr2) / Yii::$app->coefficient->data->ppzm2;
            }


            if ($model->directivity == 'Техническая (робототехника)') {
                $model->limit = round((Yii::$app->coefficient->data->blimrob * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Техническая (иная)') {
                $model->limit = round((Yii::$app->coefficient->data->blimtex * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Естественнонаучная') {
                $model->limit = round((Yii::$app->coefficient->data->blimest * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Физкультурно-спортивная') {
                $model->limit = round((Yii::$app->coefficient->data->blimfiz * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Художественная') {
                $model->limit = round((Yii::$app->coefficient->data->blimxud * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Туристско-краеведческая') {
                $model->limit = round((Yii::$app->coefficient->data->blimtur * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Социально-педагогическая') {
                $model->limit = round((Yii::$app->coefficient->data->blimsoc * $coef_raiting) * $model->year, 0);
            }

            $model->save();
        }

        return $this->redirect(['/personal/operator-programs']);
    }

    public function actionRaiting($id)
    {
        $model = $this->findModel($id);

        /* 'ocsootv' => 'Значимость оценки соответствия заявленных при включении образовательной программы в Реестр образовательных программ ожидаемых результатов ее освоения фактическому направлению развития ребенка при освоении образовательной программы',
            'ocku' => 'Значимость оценки кадровых условий реализации образовательной программы и соблюдения при реализации программы заявленных характеристик наполняемости',
            'ocmt' => 'Значимость оценки материально-технических условий реализации образовательной программы',
            'obsh' => 'Значимость общей удовлетворенности образовательной программы',
            'ktob' => 'Значимость коэффициента текучести обучающихся',
            'vgs' => 'Верхняя граница соотношения расторгнутых договоров',
            'sgs' => 'Средняя граница соотношения расторгнутых договоров',
            'pchsrd' => 'Параметр числителя соотношения расторгнутых договоров',
            'pzmsrd' => 'Параметр знаменателя соотношения расторгнутых договоров',
            'minraiting' => 'Минимальная доля оценок для определения рейтинга программы, %', */


        if ($model->quality_control >= ((Yii::$app->coefficient->data->minraiting * $model->last_contracts) / 100) && $model->quality_control > 0) {
            if (($model->last_s_contracts_rod / $model->last_contracts) >= Yii::$app->coefficient->data->vgs / 100) {
                $coef_tek = 0;
            }
            if (($model->last_s_contracts_rod / $model->last_contracts) >= Yii::$app->coefficient->data->sgs / 100 and ($model->last_s_contracts_rod / $model->last_contracts) < Yii::$app->coefficient->data->vgs / 100) {
                $coef_tek = (Yii::$app->coefficient->data->pchsrd - ($model->last_s_contracts_rod / $model->last_contracts)) / Yii::$app->coefficient->data->pzmsrd;
            }
            if (($model->last_s_contracts_rod / $model->last_contracts) < Yii::$app->coefficient->data->sgs / 100) {
                $coef_tek = 1;
            }

            $model->rating = ((Yii::$app->coefficient->data->ocsootv * $model->ocen_fact)) + ((Yii::$app->coefficient->data->ocku * $model->ocen_kadr)) + ((Yii::$app->coefficient->data->ocmt * $model->ocen_mat)) + ((Yii::$app->coefficient->data->obsh * $model->ocen_obch)) + ((Yii::$app->coefficient->data->ktob * $coef_tek) * 100);
        } else {
            $model->rating = null;
        }

        if ($model->save()) {
            return $this->redirect(['/programs/view', 'id' => $model->id]);
        }
    }

    public function actionAllraiting()
    {
        $programs = (new \yii\db\Query())
            ->select(['`programs`.id'])
            ->from('programs')
            ->join('INNER JOIN', 'mun', '`mun`.id = `programs`.mun')
            ->andWhere('`mun`.operator_id = ' . Yii::$app->operator->identity->id)
            ->column();

        foreach ($programs as $program_id) {

            $model = $this->findModel($program_id);

            if ($model->last_contracts > 0 && $model->quality_control >= ((Yii::$app->coefficient->data->minraiting * $model->last_contracts) / 100) && $model->quality_control > 0) {
                if (($model->last_s_contracts_rod / $model->last_contracts) >= Yii::$app->coefficient->data->vgs / 100) {
                    $coef_tek = 0;
                }
                if (($model->last_s_contracts_rod / $model->last_contracts) >= Yii::$app->coefficient->data->sgs / 100 and ($model->last_s_contracts_rod / $model->last_contracts) < Yii::$app->coefficient->data->vgs / 100) {
                    $coef_tek = (Yii::$app->coefficient->data->pchsrd - ($model->last_s_contracts_rod / $model->last_contracts)) / Yii::$app->coefficient->data->pzmsrd;
                }
                if (($model->last_s_contracts_rod / $model->last_contracts) < Yii::$app->coefficient->data->sgs / 100) {
                    $coef_tek = 1;
                }

                $model->rating = ((Yii::$app->coefficient->data->ocsootv * $model->ocen_fact)) + ((Yii::$app->coefficient->data->ocku * $model->ocen_kadr)) + ((Yii::$app->coefficient->data->ocmt * $model->ocen_mat)) + ((Yii::$app->coefficient->data->obsh * $model->ocen_obch)) + ((Yii::$app->coefficient->data->ktob * $coef_tek) * 100);
            } else {
                $model->rating = null;
            }

            $model->save();
        }

        return $this->redirect(['personal/operator-programs']);
    }

    /**
     * Ставит статус верификации "в архиве" программе если нет активных контрактов
     *
     * @param integer $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        /** @var $identity UserIdentity */
        $identity = Yii::$app->user->getIdentity();
        $user = $identity->getUser()->setShortLoginScenario();
        if ($user->load(Yii::$app->request->post())) {
            if ($user->validate()) {
                $model = $this->findModel($id);
                if ($model->setIsArchive()) {
                    Yii::$app->session->setFlash('success',
                        sprintf('Программа %s отправлена в архив', $model->name));

                    return $this->redirect(['/personal/organization-programs']);
                } else {
                    Yii::$app->session->setFlash('warning',
                        sprintf('Удалить программу %s нельзя. Есть заявки или договоры на обучение', $model->name));
                }

            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');


            }

            return $this->redirect(['/programs/view', 'id' => $id]);
        }

        throw new ForbiddenHttpException();
    }

    /**
     * отображение списка программ для автопролонгации
     */
    public function actionProgramListForAutoProlongation()
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            throw new ForbiddenHttpException('Нет доступа');
        }

        $autoProlongation = AutoProlongation::make(\Yii::$app->user->identity->organization->id);

        if (count($autoProlongation->getProgramIdList()) < 1) {
            return $this->redirect(Url::to(['/personal/organization-contracts']));
        }

        $program = new ProgramsSearch(['idList' => $autoProlongation->getProgramIdList() ?: 0]);
        $programDataProvider = $program->search([]);

        return $this->render('program-list-for-auto-prolongation', [
            'programDataProvider' => $programDataProvider,
        ]);
    }

    /**
     * отображение списка договоров для автопролонгации
     */
    public function actionContractListForAutoProlongation()
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson(false);
        }

        /** @var \app\models\OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $autoProlongation = AutoProlongation::make(\Yii::$app->user->identity->organization->id);

        if (count($autoProlongation->getContractIdList()) < 1) {
            return $this->redirect(Url::to(['/programs/program-list-for-auto-prolongation']));
        }

        $contractsSearch = new ContractsSearch(['idList' => $autoProlongation->getContractIdList() ?: 0]);
        $contractDataProvider = $contractsSearch->search([]);

        return $this->render('contract-list-for-auto-prolongation', [
            'operatorSettings' => $operatorSettings,
            'contractDataProvider' => $contractDataProvider
        ]);
    }

    /**
     * запустить автопролонгацию договоров
     */
    public function actionAutoProlongationInit()
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson(false);
        }
        $autoProlongation = AutoProlongation::make(\Yii::$app->user->identity->organization->id);
        $contractToAutoProlongationCount = count(array_diff($autoProlongation->getContractIdList(true), $autoProlongation->getProcessedContractIdListFromRegistry()));

        if (\Yii::$app->request->isAjax) {
            if (1 == \Yii::$app->request->post('getRegistry')) {
                return $this->redirect('/programs/auto-prolonged-registry');
            }

            $autoProlongation->init(null, 10, \Yii::$app->request->post('isNew') == 1 ? true : false);

            if ($autoProlongation->errorMessage) {
                \Yii::$app->session->addFlash('error', $autoProlongation->errorMessage);

                return $this->redirect(Url::to(['/personal/organization-contracts']));
            }

            if ($autoProlongation->remainCount === 0) {
                return $this->asJson(['status' => 'processed']);
            }

            return $this->asJson(['status' => 'created', 'remainCount' => $contractToAutoProlongationCount]);
        }

        return $this->redirect(Url::to(['/personal/organization-contracts']));
    }

    public function actionAutoProlongedRegistry()
    {
        $autoProlongation = AutoProlongation::make(Yii::$app->user->identity->organization->id);

        if ($filePath = $autoProlongation->getRegistryPath()) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            return $this->redirect(Url::to(['/personal/organization-contracts']));
        }
    }

    /**
     * @param integer $id
     *
     * @return Response
     */
    public function actionChangeAutoProlongation($id = null)
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson(false);
        }

        if (isset(\Yii::$app->request->post()['change-auto-prolongation-for-all-programs'])) {
            $autoProlongation = AutoProlongation::make(\Yii::$app->user->identity->organization->id);

            $success = $autoProlongation->changeAutoProlongationForAllProgramsWithActiveCooperate(\Yii::$app->request->post()['change-auto-prolongation-for-all-programs']);

            return $this->asJson(['changed' => $success, 'value' => \Yii::$app->request->post()['change-auto-prolongation-for-all-programs']]);
        }

        $program = $this->findModel($id);

        if (\Yii::$app->request->isAjax && $program && $program->load(\Yii::$app->request->post()) && $program->save(true, ['auto_prolongation_enabled'])) {
            return $this->asJson($program->auto_prolongation_enabled);
        }

        return $this->asJson(ActiveForm::validate($program, ['auto_prolongation_enabled']));
    }

    /**
     * @param integer $id
     *
     * @return Response
     */
    public function actionChangeAutoProlongationForContract($id = null)
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson(false);
        }

        if (isset(\Yii::$app->request->post()['change-auto-prolongation-for-all-contracts'])) {
            $autoProlongation = AutoProlongation::make(\Yii::$app->user->identity->organization->id);

            $success = $autoProlongation->changeAutoProlongationForAllContractsWithActiveCooperate(\Yii::$app->request->post()['change-auto-prolongation-for-all-contracts']);

            return $this->asJson(['changed' => $success, 'value' => \Yii::$app->request->post()['change-auto-prolongation-for-all-contracts']]);
        }

        $contract = Contracts::findOne($id);

        if (\Yii::$app->request->isAjax && $contract && $contract->load(\Yii::$app->request->post()) && $contract->save(true, ['auto_prolongation_enabled'])) {
            return $this->asJson($contract->auto_prolongation_enabled);
        }

        return $this->asJson(ActiveForm::validate($contract, ['auto_prolongation_enabled']));
    }

    /**
     * автопролонгация договора в новой группе
     */
    public function actionNewGroupAutoProlongation()
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson('Недостаточно прав');
        }

        $organizationId = \Yii::$app->user->identity->organization->id;
        $group = Groups::findOne(\Yii::$app->request->post('groupId'));

        if (!$group && AutoProlongation::canGroupBeAutoProlong($organizationId, $group->id)) {
            return $this->asJson('Невозможно перевести детей из этой группы');
        }

        if (!\Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('Страница не найдена.');
        }

        $autoProlongation = AutoProlongation::make($organizationId, null, null, $group->id);

        $contractIdList = Contracts::findAll(['id' => $autoProlongation->getContractIdListForAutoProlongationToNewGroup()]);

        $certificatesDataProvider = $dataProvider = new ActiveDataProvider([
            'query' => Contracts::find()->where(['id' => $contractIdList]),
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => 100,
            ],
        ]);

        $moduleIdList = $autoProlongation->getModuleIdList($group->program_id);

        $modules = ProgrammeModule::findAll(['id' => $moduleIdList]);
        $moduleNameList = [];
        /** @var ProgrammeModule $module */
        foreach ($modules as $module) {
            $moduleNameList += [$module->id => $module->getFullname()];
        }

        return $this->renderAjax('view/_auto-prolongation-to-new-group', ['moduleNameList' => $moduleNameList, 'certificatesDataProvider' => $certificatesDataProvider, 'group' => $group]);
    }

    /**
     * список контрактов для автопролонгации в новую группу
     */
    public function actionContractListForAutoProlongationToNewGroup()
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson('Недостаточно прав!');
        }

        $organizationId = \Yii::$app->user->identity->organization->id;
        $autoProlongFromGroupId = \Yii::$app->request->post('autoProlongFromGroupId');
        $autoProlongToYearId = \Yii::$app->request->post('autoProlongToYearId', null);

        if (!Groups::find()->where(['id' => $autoProlongFromGroupId, 'status' => Groups::STATUS_ACTIVE])->exists()
        ) {
            return $this->asJson('Группа не найдена!');
        }


        $autoProlongation = AutoProlongation::make($organizationId, null, null, $autoProlongFromGroupId);

        $contractIdList = Contracts::findAll(['id' => $autoProlongation->getContractIdListForAutoProlongationToNewGroup($autoProlongToYearId)]);

        $certificatesDataProvider = $dataProvider = new ActiveDataProvider([
            'query' => Contracts::find()->where(['id' => $contractIdList]),
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => 100,
            ],
        ]);

        return $this->renderAjax('view/_contract-list-for-auto-prolongation-to-new-group', ['certificatesDataProvider' => $certificatesDataProvider]);
    }

    /**
     * получить список групп для автопролонгации с переводом в другую группу
     */
    public function actionGetGroupListForAutoProlongation()
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson(false);
        }

        $groupIdList = AutoProlongation::getGroupIdList(\Yii::$app->request->post('depdrop_parents'), \Yii::$app->request->get('groupId'));

        return $this->asJson(['output' => $groupIdList, 'selected' => '']);
    }

    /**
     * получить информацию о группе для автопролонгации
     */
    public function actionGetGroupInfoForAutoProlongation()
    {
        $groupId = \Yii::$app->request->post('groupId');

        if (!$group = Groups::findOne($groupId)) {
            return $this->asJson(null);
        }

        return $this->asJson([
            'countToAutoProlong' => $group->getFreePlaces(),
            'group' => [
                'name' => $group->name,
                'moduleFullName' => $group->module->getFullname(),
                'programName' => $group->program->name,
                'dateStart' => \Yii::$app->formatter->asDate($group->datestart),
                'dateStop' => \Yii::$app->formatter->asDate($group->datestop),
            ]
        ]);
    }

    /**
     * запустить автопролонгацию договоров в новую группу
     */
    public function actionAutoProlongationToNewGroupInit()
    {
        if (!\Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            return $this->asJson('Недостаточно прав');
        }

        $organizationId = \Yii::$app->user->identity->organization->id;

        $group = Groups::findOne(\Yii::$app->request->post('fromGroupId'));

        if (!$group || !AutoProlongation::canGroupBeAutoProlong($organizationId, $group->id)) {
            return $this->asJson('Невозможно перевести детей из этой группы');
        }

        $contractIdList = ArrayHelper::getColumn(\Yii::$app->request->post('contractIdList'), 'value');
        $toGroupId = \Yii::$app->request->post('toGroupId');

        $autoProlongation = AutoProlongation::make($organizationId, null, null, $group->id);
        if (!$autoProlongation->init($toGroupId, null, true, $contractIdList) && $autoProlongation->errorMessage != '') {
            \Yii::$app->session->addFlash('error', $autoProlongation->errorMessage);

            return $this->redirect(Url::to(['/programs/view', 'id' => $group->program_id]));
        }

        return $this->redirect('/programs/auto-prolonged-registry');
    }
}
