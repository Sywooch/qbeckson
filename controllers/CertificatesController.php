<?php

namespace app\controllers;

use app\models\Certificates;
use app\models\Payers;
use app\models\User;
use app\traits\AjaxValidationTrait;
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * CertificatesController implements the CRUD actions for Certificates model.
 */
class CertificatesController extends Controller
{
    use AjaxValidationTrait;

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
     * Displays a single Certificates model.
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
     * Creates a new Certificates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Certificates();
        $user = new User();

        /** @var $payer Payers */
        $payer = Yii::$app->user->identity->payer;
        $region = Yii::$app->operator->identity->region;
        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            $user->username = $region . $payer->code . $user->username;
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($user);
        }

        if ($user->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post()) && $model->validate()) {

            if (!$user->password) {
                $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            } else {
                $password = $user->password;
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            }

            if (!$user->username) {
                $username = Yii::$app->getSecurity()->generateRandomString($length = 6);
                $user->username = $region . $payer->code . $username;
            } else {
                $user->username = $region . $payer->code . $user->username;
                $username = $user->username;
            }

            /** @var \app\models\UserIdentity $identity */
            $identity = Yii::$app->user->getIdentity();
            $user->mun_id = $identity->payer->mun;

            if ($user->save()) {
                $userRole = Yii::$app->authManager->getRole('certificate');
                Yii::$app->authManager->assign($userRole, $user->id);

                $model->user_id = $user->id;
                $model->payer_id = $payer->id;
                $model->number = $username;
                $model->actual = 1;
                $model->rezerv_f = 0;
                $model->rezerv = 0;
                $model->fio_child = $model->soname . ' ' . $model->name . ' ' . $model->phname;
                if ($model->canUseGroup() && $model->setNominals() && $model->save()) {

                    return $this->render('/user/view', [
                        'model' => $user,
                        'password' => $password,
                    ]);
                } else {
                    Yii::$app->session->setFlash('danger', 'Невозможно установить данную группу, достигнут лимит');
                }
                $user->delete();
            }
        }
        $user->username = mb_substr($user->username, mb_strlen($region) + mb_strlen($payer->code));
        $user->password ='';

        return $this->render('create', [
            'model' => $model,
            'user' => $user,
            'region' => $region,
            'payer' => $payer,
        ]);
    }

    /**
     * Updates an existing Certificates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $user = User::findOne($model->user_id);

        if (Yii::$app->request->isAjax && $user->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($user);
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                $model->fio_child = $model->soname . ' ' . $model->name . ' ' . $model->phname;
                $model->nominal = $model['oldAttributes']['nominal'];
                ($model->canChangeGroup && $model->canUseGroup() && $model->setNominals() && $model->save()
                    && (Yii::$app->session->setFlash('success', 'Изменена группа и пересчитаны номиналы')) || true)
                || Yii::$app->session->setFlash('danger', 'Невозможно установить данную группу, достигнут лимит');
            }
            if ($user->load(Yii::$app->request->post())) {
                $password = null;
                if ($user->newlogin == 1 || $user->newpass == 1) {
                    if ($user->newpass == 1) {
                        if (!$user->password) {
                            $password = Yii::$app->getSecurity()->generateRandomString($length = 10);
                            $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                        } else {
                            $password = $user->password;
                            $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                        }
                    } else {
                        unset($user->password);
                    }

                    if ($user->save()) {
                        return $this->render('/user/view', [
                            'model' => $user,
                            'password' => $password,
                        ]);
                    }
                }
            }

            return $this->redirect(['/certificates/view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    public function actionEdit()
    {
        $model = Yii::$app->user->identity->certificate;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save(false, ['fio_parent'])) {
                Yii::$app->session->setFlash('success', 'Информация сохранена успешно.');

                return $this->redirect(['/personal/certificate-statistic', 'id' => $model->id]);
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);

    }

    public function actionGroupPdf()
    {
        $content = $this->renderPartial('group-pdf');

        $model = Yii::$app->user->identity->certificate;

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'options' => ['title' => 'Заявление на смену группы сертификата'],
            'methods' => [
                'SetHeader' => ['Заявление на смену группы сертификата'],
            ]
        ]);

        return $pdf->render();
    }

    public function actionPassword()
    {
        $certificate = Yii::$app->user->identity->certificate;

        $user = User::findOne($certificate['user_id']);

        if ($user->load(Yii::$app->request->post()) && $user->validate()) {
            if (Yii::$app->getSecurity()->validatePassword($user->oldpassword, $user->password)) {
                if ($user->newpassword == $user->confirm) {

                    $user->password = Yii::$app->getSecurity()->generatePasswordHash($user->newpassword);

                    if ($user->save()) {
                        return $this->redirect(['/personal/certificate-info']);
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Пароли не совпадают.');

                    return $this->redirect(['/certificates/password']);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Не правильно введен пароль.');

                return $this->redirect(['/certificates/password']);
            }
        }

        return $this->render('password', [
            'user' => $user,
        ]);
    }

    public function actionActual($id)
    {
        $model = $this->findModel($id);
        $eventMessage = null;
        if ($model->load(Yii::$app->request->post()) && $model->validate() && is_null($eventMessage = $model->unFreez())) {
            return $this->redirect(['/certificates/view', 'id' => $id]);
        }
        if ($eventMessage) {
            Yii::$app->session->setFlash('error', $eventMessage);
        }

        return $this->render('nominal', [
            'model' => $model,
        ]);
    }

    public function actionNoactual($id)
    {
        $model = $this->findModel($id);
        if ($eventMessage = $model->freez()) {
            Yii::$app->session->setFlash('error', $eventMessage);
        }

        return $this->redirect(['/certificates/view', 'id' => $id]);
    }

    /**
     * Deletes an existing Certificates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = User::findOne(Yii::$app->user->id);

        if ($user->load(Yii::$app->request->post())) {

            if (Yii::$app->getSecurity()->validatePassword($user->confirm, $user->password)) {
                $model = $this->findModel($id);
                if ($model->hasContracts) {
                    throw new ForbiddenHttpException('Невозможно удалить сертификат, так как он уже заключил договоры.');
                }
                User::findOne($model['user_id'])->delete();

                return $this->redirect(['/personal/payer-certificates']);
            } else {
                Yii::$app->session->setFlash('error', 'Неправильно введен пароль.');

                return $this->redirect(['/personal/payer-certificates']);
            }
        }

        return $this->render('/user/delete', [
            'user' => $user,
        ]);
    }

    public function actionAllnominal($id)
    {
        ini_set('memory_limit', '-1');

        $certificates = (new \yii\db\Query())
            ->select(['id'])
            ->from('certificates')
            ->where(['payer_id' => $id])
            ->column();

        foreach ($certificates as $certificate_id) {

            $model = $this->findModel($certificate_id);

            $nominal = (new \yii\db\Query())
                ->select(['nominal'])
                ->from('cert_group')
                ->where(['id' => $model->cert_group])
                ->one();

            $model->balance = $nominal['nominal'] - $model->nominal + $model->balance;
            $model->nominal = $nominal['nominal'];

            $model->save();
        }

        return $this->redirect(['/personal/payer-certificates']);
    }


    public function actionImport()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $inputFile = "uploads/certs.xlsx";

        $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFile);


        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        set_time_limit(0);

        for ($row = 1; $row <= $highestRow; $row++) {
            $rowDada = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);

            if ($row == 1) {
                continue;
            }

            $user = new User();
            //$user_id = $rowDada[0][0];
            $user->username = $rowDada[0][0];
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($rowDada[0][5]);
            $user->save();

            echo $user->id;

            $userRole = Yii::$app->authManager->getRole('certificate');

            Yii::$app->authManager->assign($userRole, $user->id);

            print_r($user->getErrors());

            $model = new Certificates();
            $model->user_id = $user->id;
            $model->number = $rowDada[0][0];
            $model->fio_child = $rowDada[0][1] . ' ' . $rowDada[0][2] . ' ' . $rowDada[0][3];
            $model->name = $rowDada[0][2];
            $model->soname = $rowDada[0][1];
            $model->phname = $rowDada[0][3];
            $model->fio_parent = $rowDada[0][4];
            $model->nominal = $rowDada[0][6];
            $model->balance = $rowDada[0][6];
            $model->cert_group = $rowDada[0][7];
            $model->payer_id = $rowDada[0][8];

            $model->actual = 1;

            $model->contracts = 0;
            $model->directivity1 = 0;
            $model->directivity2 = 0;
            $model->directivity3 = 0;
            $model->directivity4 = 0;
            $model->directivity5 = 0;
            $model->directivity6 = 0;
            $model->rezerv = 0;
            $model->save();

            print_r($model->getErrors());
        }
    }

    /**
     * Finds the Certificates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Certificates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Certificates::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
