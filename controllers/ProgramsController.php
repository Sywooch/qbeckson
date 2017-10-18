<?php

namespace app\controllers;

use app\assets\programsAsset\ProgramsAsset;
use app\models\AllProgramsSearch;
use app\models\Cooperate;
use app\models\forms\ProgramAddressesForm;
use app\models\Informs;
use app\models\Model;
use app\models\Organization;
use app\models\ProgrammeModule;
use app\models\Programs;
use app\models\ProgramsallSearch;
use app\models\ProgramsFile;
use app\models\ProgramsPreviusSearch;
use app\models\UserIdentity;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
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
        /** @var $user UserIdentity */
        $user = Yii::$app->user->identity;
        $model = $this->findModel($id);

        if (!$model->isActive) {
            throw new NotFoundHttpException();
        }

        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION) && $user->organization->id !== $model->organization_id) {

            throw new ForbiddenHttpException('Нет доступа');
        }
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
            || Yii::$app->user->can(UserIdentity::ROLE_OPERATOR)) {
            if ($model->verification === $model::VERIFICATION_DENIED) {
//                Yii::$app->session->setFlash('danger', sprintf('Причина отказа: %s',
//                    $model->getInforms()->andWhere(['status' => $model::VERIFICATION_DENIED])->one()->text));
                Yii::$app->session->setFlash('danger',
                    $this->renderPartial('informers/list_of_reazon',
                        [
                            'dataProvider' => new ActiveDataProvider([
                                    'query' => $model->getInforms()
                                        ->andWhere(['status' => $model::VERIFICATION_DENIED]),
                                    'sort' => ['defaultOrder' => ['date' => SORT_DESC]]
                                ]
                            )
                        ]
                    ));
            }
        }
        $cooperate = null;
        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            $cooperate = Cooperate::find()->where([
                Cooperate::tableName() . '.payer_id' => $user->getCertificate()->select('payer_id'),
                Cooperate::tableName() . '.organization_id' => $model->organization_id])->all();
            if (!count($cooperate)) {
                Yii::$app->session->setFlash('warning', 'К сожалению, на данный момент Вы не можете записаться на обучение в организацию, реализующую выбранную программу. Уполномоченная организация пока не заключила с ней необходимое соглашение.');
            }
        }

        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
            if ($model->verification === Programs::VERIFICATION_DENIED) {
                /**@var $inform Informs */
                $inform = array_pop(
                    array_filter($model->informs, function ($val)
                    {
                        /**@var $val Informs */
                        return $val->status === Programs::VERIFICATION_DENIED;

                    }
                    )
                );
                if ($inform) {
                    Yii::$app->session->setFlash('warning', 'Причина отказа: ' . $inform->text);
                }

            }
        }

        ProgramsAsset::register($this->view);

        return $this->render('view/view', ['model' => $model, 'cooperate' => $cooperate]);
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

        $file = new ProgramsFile();
        $modelsYears = [new ProgrammeModule(['scenario' => ProgrammeModule::SCENARIO_CREATE])];

        if ($model->load(Yii::$app->request->post())) {
            $modelsYears = Model::createMultiple(ProgrammeModule::classname());
            Model::loadMultiple($modelsYears, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {

                return $this->asJson(ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsYears),
                    ActiveForm::validate($model)
                ));
            }


            $organizations = new Organization();
            $organization = $organizations->getOrganization();
            $model->organization_id = $organization['id'];
            $model->verification = Programs::VERIFICATION_UNDEFINED;
            $model->open = 0;
            if ($model->ovz == 2) {
                if (!empty($model->zab)) {
                    $model->zab = implode(',', $model->zab);
                }
            }

            if (Yii::$app->request->isPost) {

                $file->docFile = UploadedFile::getInstance($file, 'docFile');

                if (empty($file->docFile)) {
                    Yii::$app->session->setFlash('error', 'Пожалуйста, добавьте файл образовательной программы.');

                    return $this->render('create', [
                        'model' => $model,
                        'file' => $file,
                        'modelsYears' => $modelsYears,
                    ]);
                }

                $datetime = microtime(true); // date("Y-m-d-G-i-s");
                $filename = 'programs/program-' . $organization['id'] . '-' . $datetime . '.' . $file->docFile->extension;
                $model->link = $filename;
                $model->year = count($modelsYears);

                if ($file->upload($filename)) {

                    $valid = $model->validate();
                    $valid = Model::validateMultiple($modelsYears) && $valid;

                    if ($valid) {
                        $transaction = \Yii::$app->db->beginTransaction();
                        try {
                            if ($flag = $model->save(false)) {
                                $i = 1;
                                foreach ($modelsYears as $modelYears) {
                                    $modelYears->program_id = $model->id;
                                    $modelYears->year = $i;

                                    $p3 = Yii::$app->coefficient->data->p3v;

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
                                    $p21 = Yii::$app->coefficient->data->p21v;
                                    $p22 = Yii::$app->coefficient->data->p22v;

                                    $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / (($modelYears->maxchild + $modelYears->minchild) / 2)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) / ($p11 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * $p5;

                                    $modelYears->normative_price = round($nprice);
                                    $modelYears->previus = 1;
                                    $i++;
                                    if (!($flag = $modelYears->save(false))) {
                                        $transaction->rollBack();
                                        break;
                                    }
                                }
                            }
                            if ($flag) {
                                $transaction->commit();

                                $informs = new Informs();
                                $informs->program_id = $model->id;
                                $informs->text = 'Поступила программа на сертификацию';
                                $informs->from = 1;
                                $informs->date = date("Y-m-d");
                                $informs->read = 0;
                                $informs->save();

                                return $this->redirect($model->isMunicipalTask ? ['/personal/organization-municipal-task'] : ['/personal/organization-programs']);
                            }
                        } catch (Exception $e) {
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
            && count($model->informs) > 0) {
            $message = array_pop(
                array_filter($model->informs, function ($val)
                {
                    /**@var $val Informs */
                    return $val->status === Programs::VERIFICATION_DENIED;
                })
            )
                ->text;

            Yii::$app->session->setFlash('info', 'Причина предыдущего отказа: ' . $message);
        }

        $model->verification = Programs::VERIFICATION_WAIT;

        if ($model->save()) {
            return $this->render('verificate', [
                'model' => $model,
            ]);
        }
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

                            $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / (($modelYears->maxchild + $modelYears->minchild) / 2)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) / ($p11 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * $p5;

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

                            $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / (($modelYears->maxchild + $modelYears->minchild) / 2)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) / ($p11 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * $p5;

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

                return $this->redirect('/personal/operator-programs');
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
        $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsYears, 'id', 'id')));

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validateMultiple($modelsGroups);
        }

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
                'modelsYears' => (empty($modelsYears)) ? [new ProgrammeModule] : $modelsYears
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
        $model = $this->findModel($id);
        if (!$model->isActive) {
            throw new NotFoundHttpException();
        }
        $modelYears = $model->years;
        $file = new ProgramsFile();
        /** @var $organisation Organization */
        $model->zab = explode(',', $model->zab);
        $organization = Yii::$app->user->identity->organization;
        if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)
            && $organization->id !== $model->organization_id) {
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
                $filename = 'programs/program-' . $organization['id'] . '-' . $datetime . '.' . $file->docFile->extension;
                $model->link = $filename;
                $file->upload($filename);
            }
            $model->verification = Programs::VERIFICATION_UNDEFINED;
            $model->open = 0;
            if ($model->zab) {
                $model->zab = implode(',', $model->zab);
            }

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
                        $transaction->commit();

                        $informs = new Informs();
                        $informs->program_id = $model->id;
                        $informs->text = 'Отредактирована программа для сертификации';
                        $informs->from = 1;
                        $informs->date = date("Y-m-d");
                        $informs->read = 0;
                        $informs->save();

                        return $this->redirect(['/personal/organization-programs']);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        } else {
            return $this->render('update', [
                'model' => $model,
                'file' => $file,
                'modelYears' => (empty($modelYears)) ? [new ProgrammeModule(['scenario' => ProgrammeModule::SCENARIO_CREATE])] : $modelYears
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
}
