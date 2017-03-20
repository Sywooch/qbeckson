<?php

namespace app\controllers;

use Yii;
use app\models\Programs;
use app\models\ProgramsallSearch;
use app\models\ProgramsPreviusSearch;
use app\models\AllProgramsSearch;
use app\models\Certificates;
use app\models\Years;
use app\models\Groups;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\models\Informs;
use app\models\Organization;
use app\models\Cooperate;
use yii\web\UploadedFile;
use app\models\ProgramsFile;
use app\models\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Contracts;
use app\models\User;

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
        if (isset($_GET['org'])) { $searchModel->organization_id = $_GET['org']; }
        if (isset($_GET['name'])) { $searchModel->name = $_GET['name']; }
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
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        //$model->zab = explode(',', $model->zab);
        
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        
        if (isset($roles['organizations'])) {
            $organizations = new Organization();
            $organization = $organizations->getOrganization();
            
            if ($organization['id'] != $model->organization_id) {
                throw new ForbiddenHttpException('Нет доступа');
            }
        }
        
            
            $year = (new \yii\db\Query())
                ->select(['id'])
                ->from('years')
                ->where(['program_id' => $id])
                ->all();
        
            $i = 0;
            foreach ($year as $value) {
                $years[$i] = Years::findOne($value['id']);
                $i ++;
            }
        
            if (isset($roles['certificate'])) {
                $certificates = new Certificates();
                $certificate = $certificates->getCertificates();

                $cont = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['program_id' => $id])
                    ->andWhere(['certificate_id' => $certificate->id])
                    ->andWhere(['status' => 0])
                    ->one();

                $rows = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('cooperate')
                    ->where(['payer_id'=> $certificate['payer_id']])
                    ->andWhere(['organization_id' => $model['organization_id']])
                    ->andWhere(['status'=> 1])
                    ->count();
            
                if ($rows == 0) {
                Yii::$app->session->setFlash('warning', 'К сожалению, на данный момент Вы не можете записаться на обучение в организацию, реализующую выбранную программу. Уполномоченная организация пока не заключила с ней необходимое соглашение.');
                }
            
                return $this->render('view', [
                'model' => $this->findModel($model['id']),
                'years' => $years,
                'cont' => $cont,
                'cooperate' => $rows,
            ]);
        }
        
        return $this->render('view', [
            'model' => $model,
            'years' => $years,
        ]);
    }

    /**
     * Creates a new Programs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Programs();
        $file = new ProgramsFile();
        $modelsYears = [new Years];

        if ($model->load(Yii::$app->request->post())) {
            $modelsYears = Model::createMultiple(Years::classname());
            Model::loadMultiple($modelsYears, Yii::$app->request->post());

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsYears),
                    ActiveForm::validate($model)
                );
            }
                        
            
            $organizations = new Organization();
            $organization = $organizations->getOrganization();
            $model->organization_id = $organization['id'];
//            $model->payer_id = $organization['payer_id'];

            $model->verification = 0;
            //$model->rating = 0;
            //$model->limit = 0;
            //$model->study = 0;
            $model->open = 0;
            //$model->quality_control = 0;
            if ($model->ovz == 2) {
                if (!empty($model->zab)) {
                    $model->zab = implode(',', $model->zab);
                }
            }

            if (Yii::$app->request->isPost) {
                
                $file->docFile = UploadedFile::getInstance($file, 'docFile');
                
                if (empty($file->docFile)) {
                    Yii::$app->session->setFlash('error', 'Программа не добавлена.');
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

                                    $rows = (new \yii\db\Query())
                                        ->select('p3v')
                                        ->from('coefficient')
                                        ->one();
                                    $p3 = $rows['p3v'];    

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

                                    $coef = (new \yii\db\Query())
                                        ->select(['weekmonth', 'norm', 'pk', 'weekyear'])
                                        ->from('coefficient')
                                        ->one();
                                    $p14 = $coef['weekmonth'];
                                    $p16 = $coef['norm'];
                                    $p15 = $coef['pk'];
                                    $p13 = $coef['weekyear'];

                                    $rows = (new \yii\db\Query())
                                            ->select('p21v')
                                            ->from('coefficient')
                                            ->one();
                                    $p21 = $rows['p21v'];

                                    $rows = (new \yii\db\Query())
                                            ->select('p21v')
                                            ->from('coefficient')
                                            ->one();
                                    $p22 = $rows['p21v'];

                                    //return var_dump($p3);

                                    $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / (($modelYears->maxchild + $modelYears->minchild) / 2)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) / ($p11 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * $p5;        

                                    $modelYears->normative_price = round($nprice);
                                    $modelYears->previus = 1;
                                    //$modelYears->verification = 0;
                                    $i++;
                                    if (! ($flag = $modelYears->save(false))) {
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
                                return $this->redirect(['/personal/organization-programs']);
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
            'modelsYears' => (empty($modelsYears)) ? [new Years] : $modelsYears
        ]);
    }
    
   

    public function actionVerificate($id)
    {
        $model = $this->findModel($id);
        
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('years')
            ->where(['program_id' => $id])
            ->all();
        
        $i = 0;
        foreach ($rows as $value) {
            $years[$i] = Years::findOne($value['id']);
            $i++;
        }

        $model->verification = 1;
        //$model->zab = explode(',', $model->zab);
        
        if ($model->save()) {
             return $this->render('verificate', [
                 'model' => $model,
                 'years' => $years,
             ]);   
        }
    }
    
    public function actionSave($id)
    {   
        $model = $this->findModel($id);
         
        
        
         $coefficient = (new \yii\db\Query())
            ->select(['blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc'])
            ->from('coefficient')
            ->one();   
        
        if ($model->directivity == 'Техническая (робототехника)') {
            $model->limit = $coefficient['blimrob'] * $model->year;
        }
        if ($model->directivity == 'Техническая (иная)') {
            $model->limit = $coefficient['blimtex'] * $model->year;
        }
        if ($model->directivity == 'Естественнонаучная') {
            $model->limit = $coefficient['blimest'] * $model->year;
        }
        if ($model->directivity == 'Физкультурно-спортивная') {
            $model->limit = $coefficient['blimfiz'] * $model->year;
        }
        if ($model->directivity == 'Художественная') {
            $model->limit = $coefficient['blimxud'] * $model->year;
        }
        if ($model->directivity == 'Туристско-краеведческая') {
            $model->limit = $coefficient['blimtur'] * $model->year;
        }
        if ($model->directivity == 'Социально-педагогическая') {
            $model->limit = $coefficient['blimsoc'] * $model->year;
        }

        $model-> verification = 2;
         
        //return var_dump($model->limit);
        if ($model->save())  {
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
            $modelsYears = Model::createMultiple(Years::classname(), $modelsYears);
            Model::loadMultiple($modelsYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsYears),
                    ActiveForm::validate($model)
                );
            }
            
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            Years::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsYears as $modelYears) {
                            
                            
                            
                            if ($model->p3z == 1) { $p3r = 'p3v'; }
                            if ($model->p3z == 2) { $p3r = 'p3s'; }
                            if ($model->p3z == 3) { $p3r = 'p3n'; }
                            $rows = (new \yii\db\Query())
                                ->select([$p3r])
                                ->from('coefficient')
                                ->one();
                            $p3 = $rows[$p3r];    

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

                            $coef = (new \yii\db\Query())
                                ->select(['weekmonth', 'norm', 'pk', 'weekyear'])
                                ->from('coefficient')
                                ->one();
                            $p14 = $coef['weekmonth'];
                            $p16 = $coef['norm'];
                            $p15 = $coef['pk'];
                            $p13 = $coef['weekyear'];

                            if ($modelYears->p21z == 1) { $p1y = 'p21v'; }
                            if ($modelYears->p21z == 2) { $p1y = 'p21s'; }
                            if ($modelYears->p21z == 3) { $p1y = 'p21o'; }
                            $rows = (new \yii\db\Query())
                                    ->select([$p1y])
                                    ->from('coefficient')
                                    ->one();
                            $p21 = $rows[$p1y];

                            if ($modelYears->p22z == 1) { $p2y = 'p22v'; }
                            if ($modelYears->p22z == 2) { $p2y = 'p22s'; }
                            if ($modelYears->p22z == 3) { $p2y = 'p22o'; }
                            $rows = (new \yii\db\Query())
                                    ->select([$p2y])
                                    ->from('coefficient')
                                    ->one();
                            $p22 = $rows[$p2y];
                            
                            //return var_dump($p3);

                            $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / (($modelYears->maxchild + $modelYears->minchild) / 2)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) / ($p11 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * $p5;        
                
                            $modelYears->normative_price = round($nprice); 
                
                                                    
                            
                            if (! ($flag = $modelYears->save(false))) {
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
                 'modelsYears' => (empty($modelsYears)) ? [new Years] : $modelsYears
            ]);
        }
    }
    
    public function actionNewnormprice($id)
    {
        $model = $this->findModel($id);
        $modelsYears = $model->years;

        if ($model->load(Yii::$app->request->post())) {
            
            $oldIDs = ArrayHelper::map($modelsYears, 'id', 'id');
            $modelsYears = Model::createMultiple(Years::classname(), $modelsYears);
            Model::loadMultiple($modelsYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelsYears),
                    ActiveForm::validate($model)
                );
            }
            
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            Years::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsYears as $modelYears) {      
                            
                            if ($model->p3z == 1) { $p3r = 'p3v'; }
                            if ($model->p3z == 2) { $p3r = 'p3s'; }
                            if ($model->p3z == 3) { $p3r = 'p3n'; }
                            $rows = (new \yii\db\Query())
                                ->select([$p3r])
                                ->from('coefficient')
                                ->one();
                            $p3 = $rows[$p3r];    

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

                            $coef = (new \yii\db\Query())
                                ->select(['weekmonth', 'norm', 'pk', 'weekyear'])
                                ->from('coefficient')
                                ->one();
                            $p14 = $coef['weekmonth'];
                            $p16 = $coef['norm'];
                            $p15 = $coef['pk'];
                            $p13 = $coef['weekyear'];

                            if ($modelYears->p21z == 1) { $p1y = 'p21v'; }
                            if ($modelYears->p21z == 2) { $p1y = 'p21s'; }
                            if ($modelYears->p21z == 3) { $p1y = 'p21o'; }
                            $rows = (new \yii\db\Query())
                                    ->select([$p1y])
                                    ->from('coefficient')
                                    ->one();
                            $p21 = $rows[$p1y];

                            if ($modelYears->p22z == 1) { $p2y = 'p22v'; }
                            if ($modelYears->p22z == 2) { $p2y = 'p22s'; }
                            if ($modelYears->p22z == 3) { $p2y = 'p22o'; }
                            $rows = (new \yii\db\Query())
                                    ->select([$p2y])
                                    ->from('coefficient')
                                    ->one();
                            $p22 = $rows[$p2y];
                            
                            //return var_dump($p3);

                            $nprice = $p6 * (((($p21 * ($modelYears->hours - $modelYears->hoursindivid) + $p22 * $modelYears->hoursdop) / (($modelYears->maxchild + $modelYears->minchild) / 2)) + $p21 * $modelYears->hoursindivid) / ($p12 * $p16 * $p14)) * $p7 * (1 + $p8) * $p9 * $p10 + ((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) / ($p11 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * ($p1 * $p3 + $p4) + (((($modelYears->hours - $modelYears->hoursindivid) + $modelYears->hoursdop + $modelYears->hoursindivid * (($modelYears->maxchild + $modelYears->minchild) / 2)) * $p10 * $p7) / ($p15 * $p13 * $p12 * $p16 * (($modelYears->maxchild + $modelYears->minchild) / 2))) * $p5;        
                
                            $modelYears->normative_price = round($nprice);             
                            
                            if (! ($flag = $modelYears->save(false))) {
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
                 'modelsYears' => (empty($modelsYears)) ? [new Years] : $modelsYears
            ]);
        }
    }

    public function actionNormpricesave($id)
    {
        
        $model = Years::findOne($id);
        
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
            
            $year = array();
            $i = 0;
            foreach ($params as $param) {
                $year[$i] = Years::findOne($param['id']);
                
                if ($year[$i]->load(Yii::$app->request->post())) {
                    if ($i == 0 ) {return var_dump($year);}
                    $year[$i]->save();
                }
                
                 
                //$year[$i]->save();
                $i++;
            }
            //return var_dump($year);
            
            //return $this->render('/programs/viewprice', ['year' => $year, 'id' => $id]);
            //return $this->redirect('/personal/operator-programs');
            
            //$model-> verification = 2;
            
            /*if ($model->save())  {
                $informs = new Informs();
                $informs->program_id = $model->id;
                $informs->prof_id = $model->organization_id;
                $informs->text = 'Сертифицированна программа';
                $informs->from = 3;
                $informs->date = date("Y-m-d");
                $informs->read = 0;
                $informs->save(); 
                return $this->render('certificate', [
                    'model' => $model,
                    'year' => $year,
                ]);
            } */
        
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
            $model->verification = 3;

            if ($model->save())  {
                $informs->program_id = $model->id;
                $informs->prof_id = $model->organization_id;
                //$informs->text = 'Программа не сертифицированна. Причина: '.$informs->dop;
                $informs->text = $informs->dop;
                $informs->from = 3;
                $informs->status = 3;
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
        $modelYears = Model::createMultiple(Years::classname(), $modelsYears);
        Model::loadMultiple($modelsYears, Yii::$app->request->post());
        $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsYears, 'id', 'id')));
        
        if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validateMultiple($modelsGroups);
        }
        
        if (Yii::$app->request->isPost) {      
            if ($model->verification == 1) {
                Yii::$app->session->setFlash('error', 'Редактирование недоступно, программа проходит сертификацию.');
                return $this->redirect(['/personal/organization-programs']);
            }

                        foreach ($modelsYears as $modelYears) {
                            
                            $modelYears->save();
                            }
            return $this->redirect(['/personal/organization-programs']);
                            
        } else {
            return $this->render('open', [
                'modelsYears' => (empty($modelsYears)) ? [new Years] : $modelsYears
            ]);
        }
    }
    
    /**
     * Updates an existing Programs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelYears = $model->years;
        $file = new ProgramsFile();
        $model->zab = explode(',', $model->zab);
        
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        
        if (isset($roles['organizations'])) {
            $organizations = new Organization();
            $organization = $organizations->getOrganization();
            
            if ($organization['id'] != $model->organization_id) {
                throw new ForbiddenHttpException('Нет доступа');
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->verification == 1) {
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
             $model->verification = 0;
            $model->open = 0;
            if ($model->zab) {
            $model->zab = implode(',', $model->zab);
            }
            
            $oldIDs = ArrayHelper::map($modelYears, 'id', 'id');
            $modelYears = Model::createMultiple(Years::classname(), $modelYears);
            Model::loadMultiple($modelYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelYears),
                    ActiveForm::validate($model)
                );
            }
            
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            Years::deleteAll(['id' => $deletedIDs]);
                        }
                        $i = 1;
                        foreach ($modelYears as $modelYears) {
                            $modelYears->program_id = $model->id;
                            $modelYears->year = $i;
                            
                            if (! ($flag = $modelYears->save(false))) {
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
                 'modelYears' => (empty($modelYears)) ? [new Years] : $modelYears
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
            $modelYears = Model::createMultiple(Years::classname(), $modelYears);
            Model::loadMultiple($modelYears, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelYears, 'id', 'id')));

            // ajax validation
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($modelYears),
                    ActiveForm::validate($model)
                );
            }
            
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelYears) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (! empty($deletedIDs)) {
                            Years::deleteAll(['id' => $deletedIDs]);
                        }
                        $i = 1;
                        foreach ($modelYears as $modelYears) {
                            $modelYears->program_id = $model->id;
                            $modelYears->year = $i;
                            if (! ($flag = $modelYears->save(false))) {
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
                 'modelYears' => (empty($modelYears)) ? [new Years] : $modelYears
            ]);
        }
    }
    
    public function actionNewlimit($id)
    {
        $model = $this->findModel($id);
               
        $coefficient = (new \yii\db\Query())
            ->select(['ngrp', 'sgrp', 'vgrp', 'ppchr1', 'ppchr2', 'ppzm1', 'ppzm2', 'blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc'])
            ->from('coefficient')
            ->one();   
        
        if ($model->rating < $coefficient['ngrp']) {  $coef_raiting = 0; }
        if ($model->rating == NULL) { $coef_raiting = 1; }
        
       // return var_dump($coef_raiting);
        if ($model->rating >= $coefficient['ngrp'] and $model->rating < $coefficient['sgrp']) {
            $coef_raiting = ($model->rating - $coefficient['ppchr1']) / $coefficient['ppzm1'];
        }
        if ($model->rating >= $coefficient['sgrp'] and $model->rating < $coefficient['vgrp']) {
            $coef_raiting = 1;
        }
        if ($model->rating >= $coefficient['vgrp']) {
            $coef_raiting = ($model->rating - $coefficient['ppchr2']) / $coefficient['ppzm2'];
        }
           
        
        if ($model->directivity == 'Техническая (робототехника)') {
            $model->limit = round(($coefficient['blimrob'] * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Техническая (иная)') {
            $model->limit = round(($coefficient['blimtex'] * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Естественнонаучная') {
            $model->limit = round(($coefficient['blimest'] * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Физкультурно-спортивная') {
            $model->limit = round(($coefficient['blimfiz'] * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Художественная') {
            $model->limit = round(($coefficient['blimxud'] * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Туристско-краеведческая') {
            $model->limit = round(($coefficient['blimtur'] * $coef_raiting) * $model->year, 0);
        }
        if ($model->directivity == 'Социально-педагогическая') {
            $model->limit = round(($coefficient['blimsoc'] * $coef_raiting) * $model->year, 0);
        }     
        
        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['/programs/view', 'id' => $model->id]);
        }

        return $this->render('newlimit', [
            'model' => $model,
        ]);

    }

    public function actionAlllimit()
    {
        $programs = (new \yii\db\Query())
            ->select(['id'])
            ->from('programs')
            ->column();
        
        $coefficient = (new \yii\db\Query())
            ->select(['ngrp', 'sgrp', 'vgrp', 'ppchr1', 'ppchr2', 'ppzm1', 'ppzm2', 'blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc'])
            ->from('coefficient')
            ->one(); 
        
        foreach ($programs as $program_id) {
            
            $model = $this->findModel($program_id);


            if ($model->rating < $coefficient['ngrp']) {  $coef_raiting = 0; }
            if ($model->rating == NULL) { $coef_raiting = 1; }
            if ($model->rating >= $coefficient['ngrp'] and $model->rating < $coefficient['sgrp']) {
                $coef_raiting = ($model->rating - $coefficient['ppchr1']) / $coefficient['ppzm1'];
            }
            if ($model->rating >= $coefficient['sgrp'] and $model->rating < $coefficient['vgrp']) {
                $coef_raiting = 1;
            }
            if ($model->rating >= $coefficient['vgrp']) {
                $coef_raiting = ($model->rating - $coefficient['ppchr2']) / $coefficient['ppzm2'];
            }


            if ($model->directivity == 'Техническая (робототехника)') {
                $model->limit = round(($coefficient['blimrob'] * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Техническая (иная)') {
                $model->limit = round(($coefficient['blimtex'] * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Естественнонаучная') {
                $model->limit = round(($coefficient['blimest'] * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Физкультурно-спортивная') {
                $model->limit = round(($coefficient['blimfiz'] * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Художественная') {
                $model->limit = round(($coefficient['blimxud'] * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Туристско-краеведческая') {
                $model->limit = round(($coefficient['blimtur'] * $coef_raiting) * $model->year, 0);
            }
            if ($model->directivity == 'Социально-педагогическая') {
                $model->limit = round(($coefficient['blimsoc'] * $coef_raiting) * $model->year, 0);
            }     
        
            $model->save();
        }

        return $this->redirect(['/personal/operator-programs']);
    }

    
    public function actionRaiting($id)
    {
        $model = $this->findModel($id);
        
        $coefficient = (new \yii\db\Query())
            ->select(['ocsootv', 'ocku', 'ocmt', 'obsh', 'ktob', 'vgs', 'sgs', 'pchsrd', 'pzmsrd', 'minraiting'])
            ->from('coefficient')
            ->one(); 
        
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
        
        
        if ($model->quality_control >= (($coefficient['minraiting'] * $model->last_contracts) / 100) && $model->quality_control > 0) {
            if (($model->last_s_contracts_rod / $model->last_contracts) >= $coefficient['vgs'] / 100) { $coef_tek = 0; }
            if (($model->last_s_contracts_rod / $model->last_contracts) >= $coefficient['sgs'] / 100 and ($model->last_s_contracts_rod / $model->last_contracts) < $coefficient['vgs'] / 100) { 
                $coef_tek = ($coefficient['pchsrd'] - ($model->last_s_contracts_rod / $model->last_contracts)) / $coefficient['pzmsrd']; 
            }
            if (($model->last_s_contracts_rod / $model->last_contracts) < $coefficient['sgs'] / 100) {
                $coef_tek = 1;
            }
            
           $model->rating = (($coefficient['ocsootv'] * $model->ocen_fact)) + (($coefficient['ocku'] * $model->ocen_kadr)) + (($coefficient['ocmt'] * $model->ocen_mat)) + (($coefficient['obsh'] * $model->ocen_obch)) + (($coefficient['ktob'] * $coef_tek) * 100); 
        }
        else {
            $model->rating = NULL;
        }
        
       if ($model->save()) {
           return $this->redirect(['/programs/view', 'id' => $model->id]);
       }
    }

    public function actionAllraiting()
    {
        $programs = (new \yii\db\Query())
            ->select(['id'])
            ->from('programs')
            ->column();
        
        $coefficient = (new \yii\db\Query())
            ->select(['ocsootv', 'ocku', 'ocmt', 'obsh', 'ktob', 'vgs', 'sgs', 'pchsrd', 'pzmsrd', 'minraiting'])
            ->from('coefficient')
            ->one(); 
        
        foreach ($programs as $program_id) {
            
            $model = $this->findModel($program_id);

            if ($model->quality_control >=  (($coefficient['minraiting'] * $model->last_contracts) / 100) && $model->quality_control > 0) {
                if (($model->last_s_contracts_rod / $model->last_contracts) >= $coefficient['vgs'] / 100) { $coef_tek = 0; }
                if (($model->last_s_contracts_rod / $model->last_contracts) >= $coefficient['sgs'] / 100 and ($model->last_s_contracts_rod / $model->last_contracts) < $coefficient['vgs'] / 100) { 
                    $coef_tek = ($coefficient['pchsrd'] - ($model->last_s_contracts_rod / $model->last_contracts)) / $coefficient['pzmsrd']; 
                }
                if (($model->last_s_contracts_rod / $model->last_contracts) < $coefficient['sgs'] / 100) {
                    $coef_tek = 1;
                }

               $model->rating = (($coefficient['ocsootv'] * $model->ocen_fact)) + (($coefficient['ocku'] * $model->ocen_kadr)) + (($coefficient['ocmt'] * $model->ocen_mat)) + (($coefficient['obsh'] * $model->ocen_obch)) + (($coefficient['ktob'] * $coef_tek) * 100); 
            }
            else {
                $model->rating = NULL;
            }

            $model->save();
       }
        return $this->redirect(['personal/operator-programs']);
    }
    /**
     * Deletes an existing Programs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
         $user = User::findOne(Yii::$app->user->id);

        if($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                    $model = $this->findModel($id);

                    $contarcts = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['program_id' => $model->id])
                        ->andWhere(['status' => [0,2,3,4]])
                        ->column();
                    
                    foreach ($contarcts as $contarct) {
                        $cont  = Contracts::findOne($contarct);
                        $cont->delete();
                    }
                
                    $model->delete();

                    return $this->redirect(['/personal/organization-programs']);
                }
            else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');
                 return $this->redirect(['/personal/operator-payers']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
            'title' => 'Удалить программу',
        ]);
    }

    /**
     * Finds the Programs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
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
}
