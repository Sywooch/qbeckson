<?php

namespace app\controllers;

use Yii;
use app\models\ProgrammeModule;
use app\models\ProgrammeModuleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Groups;
use app\models\Model;
use app\models\Organization;
use app\models\Completeness;
use app\models\Programs;

/**
 * YearsController implements the CRUD actions for ProgrammeModule model.
 */
class YearsController extends Controller
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
     * Lists all ProgrammeModule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgrammeModuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgrammeModule model.
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
     * Creates a new ProgrammeModule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProgrammeModule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProgrammeModule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            $contracts = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['program_id' => $model->program_id])
                            ->andWhere(['status' => [0,1,3]])
                            ->count();
            
            if ($contracts == 0) {
                $model->save();
                return $this->redirect(['programs/view', 'id' => $model->program_id]);
            }
            else {
                Yii::$app->session->setFlash('error', 'Нельзя изменить цену программы, есть заявку на эту программу.');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProgrammeModule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionStart($id)
    {
        $model = $this->findModel($id);
        
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('groups')
            ->where(['year_id' => $model->id])
            ->count();
        
        if ($rows > 0) {
            $model->open = 1;
            if ($model->save()) {
                return $this->redirect(['programs/view', 'id' => $model->program_id]);
            }
        }
        else {
           /* $modelsGroups = [new Groups];

            $modelsGroups = Model::createMultiple(Groups::classname());
            Model::loadMultiple($modelsGroups, Yii::$app->request->post());

                // ajax validation
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validateMultiple($modelsGroups);
                }
                if (Yii::$app->request->isPost) {           
                            $organizations = new Organization();
                            $organization = $organizations->getOrganization();
                                
                            $i = 1;
                            $count = count($modelsGroups);
                            foreach ($modelsGroups as $modelGroups) {
                                $modelGroups->program_id = $model->program_id;
                                $modelGroups->year_id = $model->id;
                                $modelGroups->organization_id = $organization['id'];
                                $modelGroups->save();
                                if ($count == $i) {
                                    $model->open = 1;
                                    $model->save();
                                }
                                $i++;
                            }
                            return $this->redirect(['/personal/organization-programs']);      
                }
            */    
            $modelsGroups = new Groups();
                if ($modelsGroups->load(Yii::$app->request->post())) {
                    $organizations = new Organization();
                    $organization = $organizations->getOrganization();
                    //return var_dump ($organization);
                    $modelsGroups->organization_id = $organization['id'];
                    $modelsGroups->program_id = $model->program_id;
                    $modelsGroups->year_id = $model->id;
                    if ($modelsGroups->save()) {

                       /* $completeness = new Completeness();
                        $completeness->group_id = $modelsGroups->id;
                        $completeness->month = date(m);
                        $completeness->year = date(Y);
                        $completeness->preinvoice = 0;
                        $completeness->completeness = 100;
                        if ($completeness->save()) {
                            $preinvoice = new Completeness();
                            $preinvoice->group_id = $model->id;
                            $preinvoice->month = date(m)+1;
                            $preinvoice->year = date(Y);
                            $preinvoice->preinvoice = 1;
                            $preinvoice->completeness = 80;
                            if ($preinvoice->save()) {*/
                                $model->open = 1;
                                if ($model->save()) {
                                    return $this->redirect(['programs/view', 'id' => $model->program_id]);
                                }
                           // }
                        //}
                    }
                }
            
             return $this->render('/groups/new', [
                'modelsGroups' => $modelsGroups,
            ]);
        }
       
    }
    
    public function actionStop($id)
    {
        $model = $this->findModel($id);
        $model->open = 0;
        if ($model->save()) {
            return $this->redirect(['programs/view', 'id' => $model->program_id]);
        }
    }
    
    /*
    public function actionNormprice($id)
    {
        $modelYears = $this->findModel($id);
        $model = Programs::findOne($modelYears->program_id);
        
        if($modelYears->load(Yii::$app->request->post())) {
        
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
            
            $modelYears->save();
        
            return $this->redirect(['/programs/view', 'id' => $model->id]);
        }

        return $this->render('newnormprice', [
            'modelYears' => $modelYears,
        ]);

    } */

    public function actionAllnormprice()
    {
        $years = (new \yii\db\Query())
            ->select(['id'])
            ->from('years')
            ->column();
        
        //return var_dump($years);
        foreach ($years as $year_id) {
            
            $modelYears = $this->findModel($year_id);
        $model = Programs::findOne($modelYears->program_id);
        
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
            
            $modelYears->save();
        }

        return $this->redirect(['personal/operator-programs']);
    }
    
    public function actionPrevstart($id)
    {
        $model = $this->findModel($id);
        $model->previus = 1;
        if ($model->save()) {
            return $this->redirect(['programs/view', 'id' => $model->program_id]);
        }
    }
    
    public function actionPrevstop($id)
    {
        $model = $this->findModel($id);
        $model->previus = 0;
        if ($model->save()) {
            return $this->redirect(['programs/view', 'id' => $model->program_id]);
        }
    }
    
    public function actionImport()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        $inputFile = "uploads/years.xlsx";
        
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);

        
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();
        
        
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowDada = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row,NULL,TRUE,FALSE);
            
            if($row == 1) {
                continue;
            }

            $years = ProgrammeModule::findOne($rowDada[0][0]);
            $years->price = $rowDada[0][1];
            $years->open = 1;
            
            $years->save();
            
            print_r($years->getErrors());
            
            echo $years->id;
        }
        echo "ОК!";
        
    }

    /**
     * Finds the ProgrammeModule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProgrammeModule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgrammeModule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
