<?php

namespace app\controllers;

use app\models\Certificates;
use app\models\Contracts;
use app\models\Invoices;
use app\models\invoices\InvoiceBuilder;
use app\models\invoices\PreInvoiceBuilder;
use app\models\InvoicesSearch;
use app\models\Organization;
use mPDF;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * InvoicesController implements the CRUD actions for Invoices model.
 */
class InvoicesController extends Controller
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
     * Lists all Invoices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoices model.
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
     * Creates a new Invoices model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoices();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionNew($payer)
    {
        $builder = InvoiceBuilder::createInstance(['payer_id' => $payer]);

        if ($builder->load(Yii::$app->request->post()) && $builder->save()) {
            return $this->redirect(['/invoices/view', 'id' => $builder->invoice->id]);
        }

        if (mb_strlen($builder->contractsData['contracts']) < 1) {
            Yii::$app->session->setFlash('danger', 'Не обнаружено ни одного договора');
        }

        return $this->render('number', [
            'model' => $builder,
        ]);
    }

    public function actionDec($payer)
    {
        $builder = InvoiceBuilder::createInstance([
            'payer_id' => $payer,
            'isDecember' => true,
        ]);

        if ($builder->load(Yii::$app->request->post()) && $builder->save()) {
            return $this->redirect(['/invoices/view', 'id' => $builder->invoice->id]);
        }

        if (mb_strlen($builder->contractsData['contracts']) < 1) {
            Yii::$app->session->setFlash('danger', 'Не обнаружено ни одного договора');
        }

        return $this->render('number', [
            'model' => $builder,
        ]);
    }


    public function actionPreinvoice($payer)
    {

        $builder = PreInvoiceBuilder::createInstance(['payer_id' => $payer]);
        if ($builder->load(Yii::$app->request->post()) && $builder->save()) {
            return $this->redirect(['/invoices/view', 'id' => $builder->invoice->id]);
        }

        if (mb_strlen($builder->contractsData['contracts']) < 1) {
            Yii::$app->session->setFlash('danger', 'Не обнаружено ни одного договора');
        }

        return $this->render('prenumber', [
            'model' => $builder,
        ]);
    }

    /**
     * Updates an existing Invoices model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionTerminate($id)
    {
        $model = $this->findModel($id);
        $model->status = Invoices::STATUS_REMOVED;
        $model->save() || Yii::$app->session->setFlash('danger', 'Ошибка! сохранить новое состояние счета неудалось!');

        return $this->redirect(['/personal/organization-invoices']);

    }

    public function actionRollBack($id)
    {
        $model = $this->findModel($id);
        if ($model->status !== Invoices::STATUS_IN_THE_WORK) {

            throw new ForbiddenHttpException('Можно отменить только счет в "обработке"');
        }
        $model->status = Invoices::STATUS_NOT_VIEWED;
        $model->save() || Yii::$app->session->setFlash('danger', 'Ошибка! сохранить новое состояние счета неудалось!');

        return $this->redirect(['view', 'id' => $model->id]);

    }

    public function actionWork($id)
    {
        $model = $this->findModel($id);
        $model->status = Invoices::STATUS_IN_THE_WORK;
        $model->save() || Yii::$app->session->setFlash('danger', 'Ошибка! сохранить новое состояние счета не удалось!');

        return $this->redirect(['view', 'id' => $model->id]);

    }

    public function actionComplete($id)
    {
        $model = $this->findModel($id);
        $model->setAsPaid();

        if ($model->save()) {
            $model->refoundMoney();
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }


    /**
     * Deletes an existing Invoices model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionCountpayer()
    {
        if (isset($_POST['keylist'])) {
            $keylist = $_POST['keylist'];

            $i = 0;
            foreach ($keylist as $contract_id) {
                $contract = Contracts::findOne($contract_id);

                $payers[$i] = $contract['payer_id'];
                $i++;
            }

            $payers = array_unique($payers);
            $total = count($payers);

            return Json::encode([
                'total' => $total
            ]);
        }
    }

    /**
     * @deprecated
     * @param $id
     */
    public function actionMpdf($id)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $model = $this->findModel($id);

        //$organizations = new Organization();
        //$organization = $organizations->getOrganization();
        $organization = Organization::findOne($model->organization_id);


        $cooperate = (new \yii\db\Query())
            ->select(['number', 'date'])
            ->from('cooperate')
            ->where(['payer_id' => $model->payers_id])
            ->andWhere(['organization_id' => $model->organization_id])
            ->one();

        $date_invoice = explode("-", $model->date);
        $date_cooperate = explode("-", $cooperate['date']);

        $html = '<p style="text-align: center;">Приложение к счету от ' . $date_invoice[2] . '.' . $date_invoice[1] . '.' . $date_invoice[0] . ' №' . $model->number . '</p>';
        $html = $html . '<p style="text-align: center;">по договору ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . '</p>';


        switch ($model->month) {
            case 1:
                $m = 'январь';
                break;
            case 2:
                $m = 'февраль';
                break;
            case 3:
                $m = 'март';
                break;
            case 4:
                $m = 'апрель';
                break;
            case 5:
                $m = 'май';
                break;
            case 6:
                $m = 'июнь';
                break;
            case 7:
                $m = 'июль';
                break;
            case 8:
                $m = 'август';
                break;
            case 9:
                $m = 'сентябрь';
                break;
            case 10:
                $m = 'октябрь';
                break;
            case 11:
                $m = 'ноябрь';
                break;
            case 12:
                $m = 'декабрь';
                break;
        }
        $html = $html . '<p>Месяц, за который сформирован аванс: ' . $m . ' ' . date('Y') . '</p>';
        $html = $html . '<p>Наименование поставщика образовательных услуг: ' . $organization->name . '</p>';
        $html = $html . '<p>ОГРН/ОГРНИП поставщика образовательных услуг:  ' . $organization->OGRN . '</p>';
        $html = $html . '<p>Всего подлежит к оплате: ' . round($model->sum, 2) . ' руб.</p>';


        $html = $html . '<table border="1"  cellpadding="1" cellspacing="0">';
        $html = $html . '<tr>
        <td style="text-align: center;">&nbsp;№ п.п.&nbsp;</td>
        <td style="text-align: center;">&nbsp;№ договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;Дата договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;&nbsp;Номер сертификата&nbsp;&nbsp;</td>
        <td style="text-align: center;">&nbsp;Объем оказания<br>услуги, %&nbsp;</td>
        <td style="text-align: center;">&nbsp;К оплате, руб.&nbsp;</td>
        </tr>';

        $i = 1;
        foreach (explode(',', $model['contracts']) as $contracts) {
            $contract = Contracts::findOne($contracts);
            if (isset($contract)) {
                $date_contract = explode("-", $contract->date);

                $cert = Certificates::findOne($contract->certificate_id);

                $completeness = (new \yii\db\Query())
                    ->select(['completeness', 'sum'])
                    ->from('completeness')
                    ->where(['contract_id' => $contract->id])
                    ->andWhere(['month' => $model->month])
                    ->andWhere(['preinvoice' => 1])
                    ->one();

                $html = $html . '<tr>
            <td style="text-align: center;">' . $i . '</td>
            <td style="text-align: center;">' . $contract->number . '</td>
            <td style="text-align: center;">' . $date_contract[2] . '.' . $date_contract[1] . '.' . $date_contract[0] . '</td>
            <td style="text-align: center;">' . $cert->number . '</td>
            <td style="text-align: center;">' . $completeness["completeness"] . '</td>
            <td style="text-align: center;">' . round($completeness["sum"], 2) . '</td>
            </tr>';
            }
            $i++;
        }

        $html = $html . '</table>';

        $html = $html . '<br>
        <table width="100%" border="0"  cellpadding="1" cellspacing="0">
        <tr>
            <td >' . $organization->name . '</td>
        </tr>
        <tr>
            <td ><br>Руководитель<br><br><br><br>_________________/_________________/<br>М.П.</td>
            <td >Главный бухгалтер<br><br><br><br>_________________/_________________/</td>
        </tr>
        </table>';


        $mpdf = new mPDF();
        $mpdf->WriteHtml($html);
        echo $mpdf->Output('prepaid-' . $model->number . '.pdf', 'D');
    }


    /**
     * @deprecated
     * @param $id
     */
    public function actionInvoice($id)
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);


        $model = $this->findModel($id);

        //  $organizations = new Organization();
        // $organization = $organizations->getOrganization();

        $organization = Organization::findOne($model->organization_id);


        /* if ($model->month == 12) {
             $prepaid = (new \yii\db\Query())
                             ->select(['sum'])
                             ->from('invoices')
                             ->where(['payers_id' => $model->payers_id])
                             ->andWhere(['organization_id' => $model->organization_id])
                             ->andWhere(['month' => 12])
                             ->andWhere(['prepayment' => 1])
                             ->andWhere(['status' => [0,1,2]])
                             ->one();
         }
         else { */
        $prepaid = (new \yii\db\Query())
            ->select(['sum'])
            ->from('invoices')
            ->where(['payers_id' => $model->payers_id])
            ->andWhere(['organization_id' => $model->organization_id])
            ->andWhere(['month' => $model->month])
            ->andWhere(['prepayment' => 1])
            ->andWhere(['status' => [0, 1, 2]])
            ->one();
        //}

        $cooperate = (new \yii\db\Query())
            ->select(['number', 'date'])
            ->from('cooperate')
            ->where(['payer_id' => $model->payers_id])
            ->andWhere(['organization_id' => $model->organization_id])
            ->one();

        $date_invoice = explode("-", $model->date);
        $date_cooperate = explode("-", $cooperate['date']);

        $html = '<p style="text-align: center;">Приложение к счету от ' . $date_invoice[2] . '.' . $date_invoice[1] . '.' . $date_invoice[0] . ' №' . $model->number . '</p>';
        $html = $html . '<p style="text-align: center;">по договору ' . $cooperate['number'] . ' от ' . $date_cooperate[2] . '.' . $date_cooperate[1] . '.' . $date_cooperate[0] . '</p>';

        //if ($model->month == 1) { $month = 12; } else {
        $month = $model->month;
        // }
        switch ($month) {
            case 1:
                $m = 'январь';
                break;
            case 2:
                $m = 'февраль';
                break;
            case 3:
                $m = 'март';
                break;
            case 4:
                $m = 'апрель';
                break;
            case 5:
                $m = 'май';
                break;
            case 6:
                $m = 'июнь';
                break;
            case 7:
                $m = 'июль';
                break;
            case 8:
                $m = 'август';
                break;
            case 9:
                $m = 'сентябрь';
                break;
            case 10:
                $m = 'октябрь';
                break;
            case 11:
                $m = 'ноябрь';
                break;
            case 12:
                $m = 'декабрь';
                break;
        }
        $html = $html . '<p>Месяц, за который сформирован счет: ' . $m . ' ' . date('Y') . '</p>';
        $html = $html . '<p>Наименование поставщика образовательных услуг: ' . $organization->full_name . '</p>';
        $html = $html . '<p>ОГРН/ОГРНИП поставщика образовательных услуг:  ' . $organization->OGRN . '</p>';

        $html = $html . '<p>Всего оказано услуг на сумму: ' . round($model->sum, 2) . ' руб.</p>';

        //return var_dump($prepaid);
        if ($prepaid['sum']) {
            $html = $html . '<p>Подлежит оплате: ' . round($model->sum - $prepaid['sum'], 2) . ' руб.</p>';
        } else {
            $html = $html . '<p>Подлежит оплате: ' . round($model->sum, 2) . ' руб.</p>';
        }


        $html = $html . '<table border="1"  cellpadding="1" cellspacing="0">';
        $html = $html . '<tr>
        <td style="text-align: center;">&nbsp;№ п.п.&nbsp;</td>
        <td style="text-align: center;">&nbsp;№ договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;Дата договора&nbsp;</td>
        <td style="text-align: center;">&nbsp;&nbsp;Номер сертификата&nbsp;&nbsp;</td>
        <td style="text-align: center;">&nbsp;Объем оказания<br>услуги, %&nbsp;</td>
        <td style="text-align: center;">&nbsp;К оплате, руб.&nbsp;</td>
        </tr>';

        $i = 1;
        foreach (explode(',', $model['contracts']) as $contracts) {
            $contract = Contracts::findOne($contracts);
            $date_contract = explode("-", $contract->date);

            $cert = Certificates::findOne($contract->certificate_id);

            /*if ($model->month == 12) {
                $completeness = (new \yii\db\Query())
                    ->select(['completeness', 'sum'])
                    ->from('completeness')
                    ->where(['contract_id' => $contract->id])
                    ->andWhere(['month' => 12])
                    ->andWhere(['preinvoice' => 0])
                    ->one();
                
                 $nopreinvoice = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('invoices')
                    ->where(['month' => 12])
                    ->andWhere(['prepayment' => 1])
                    ->one();

                $precompleteness = (new \yii\db\Query())
                        ->select(['sum'])
                        ->from('completeness')
                        ->where(['contract_id' => $contract->id])
                        ->andWhere(['preinvoice' => 1])
                        ->andWhere(['month' => 12])
                        ->one();

                if (!isset($nopreinvoice['id']) or empty($nopreinvoice['id'])) {
                    $sum = round($completeness['sum'] + $precompleteness['sum'], 2);
                }
                else { 
                    $sum = round($completeness['sum'], 2);
               // }
                
                
            /} else {*/

            $completeness = (new \yii\db\Query())
                ->select(['completeness', 'sum'])
                ->from('completeness')
                ->where(['contract_id' => $contract->id])
                ->andWhere(['month' => $model->month])
                ->andWhere(['preinvoice' => 0])
                ->one();

            /*$nopreinvoice = (new \yii\db\Query())
                ->select(['id'])
                ->from('invoices')
                ->where(['month' => $model->month])
                ->andWhere(['prepayment' => 1])
                ->one();

            $precompleteness = (new \yii\db\Query())
                    ->select(['sum'])
                    ->from('completeness')
                    ->where(['contract_id' => $contract->id])
                    ->andWhere(['preinvoice' => 1])
                    ->andWhere(['month' => $model->month])
                    ->one();

            if (!isset($nopreinvoice['id']) or empty($nopreinvoice['id'])) {
                $sum = round($completeness['sum'] + $precompleteness['sum'], 2);
            }
            else { */
            $sum = round($completeness['sum'], 2);
            // }
            //}

            $html = $html . '<tr>
            <td style="text-align: center;">' . $i . '</td>
            <td style="text-align: center;">' . $contract->number . '</td>
            <td style="text-align: center;">' . $date_contract[2] . '.' . $date_contract[1] . '.' . $date_contract[0] . '</td>
            <td style="text-align: center;">' . $cert->number . '</td>
            <td style="text-align: center;">' . $completeness["completeness"] . '</td>
            <td style="text-align: center;">' . $sum . '</td>
            </tr>';

            $i++;
        }

        $html = $html . '</table>';

        $html = $html . '<br>
        <table width="100%" border="0"  cellpadding="1" cellspacing="0">
        <tr>
            <td >' . $organization->name . '</td>
        </tr>
        <tr>
            <td ><br>Руководитель<br><br><br><br>_________________/_________________/<br>М.П.</td>
            <td >Главный бухгалтер<br><br><br><br>_________________/_________________/</td>
        </tr>
        </table>';

        $mpdf = new mPDF();
        $mpdf->WriteHtml($html);
        echo $mpdf->Output('invoice-' . $model->number . '.pdf', 'D');
    }

    /**
     * Finds the Invoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoices::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
