<?php

namespace app\controllers;

use app\components\GoogleCoordinates;
use app\models\forms\SelectModuleMainAddressForm;
use app\models\Model;
use app\models\ProgrammeModule;
use Yii;
use app\models\ProgramModuleAddress;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class ProgramModuleAddressController
 * @package app\controllers
 */
class ProgramModuleAddressController extends Controller
{
    /**
     * @param integer $moduleId
     * @return mixed
     */
    public function actionCreate($moduleId)
    {
        $programModuleModel = $this->findModuleModel($moduleId);
        if (count($programModuleModel->addresses) > 0) {
            return $this->redirect(['update', 'moduleId' => $programModuleModel->id]);
        }
        $addressModels = [new ProgramModuleAddress];
        if (Yii::$app->request->post()) {
            $addressModels = Model::createMultiple(ProgramModuleAddress::class);
            Model::loadMultiple($addressModels, Yii::$app->request->post());

            if (Model::validateMultiple($addressModels)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($addressModels as $addressModel) {
                        /** @var ProgramModuleAddress $addressModel */
                        $addressModel->program_module_id = $programModuleModel->id;
                        if ($addressModel->status) {
                            $googleCoordinatesComponent = new GoogleCoordinates($addressModel->address);
                            $addressModel->lat = $googleCoordinatesComponent->getLat();
                            $addressModel->lng = $googleCoordinatesComponent->getLng();
                        }
                        if (!($flag = $addressModel->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->redirect(['update', 'moduleId' => $moduleId]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'addressModels' => $addressModels,
            'programModuleModel' => $programModuleModel,
        ]);
    }

    /**
     * @param integer $moduleId
     * @return mixed
     */
    public function actionUpdate($moduleId)
    {
        $programModuleModel = $this->findModuleModel($moduleId);
        $addressModels = $programModuleModel->addresses;

        if (Yii::$app->request->post()) {
            $oldIDs = ArrayHelper::map($addressModels, 'id', 'id');
            $addressModels = Model::createMultiple(ProgramModuleAddress::class, $addressModels);
            Model::loadMultiple($addressModels, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($addressModels, 'id', 'id')));

            if (Model::validateMultiple($addressModels)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!empty($deletedIDs)) {
                        ProgramModuleAddress::deleteAll(['id' => $deletedIDs]);
                    }
                    foreach ($addressModels as $addressModel) {
                        /** @var ProgramModuleAddress $addressModel */
                        $addressModel->program_module_id = $programModuleModel->id;
                        if ($addressModel->status) {
                            $googleCoordinatesComponent = new GoogleCoordinates($addressModel->address);
                            $addressModel->lat = $googleCoordinatesComponent->getLat();
                            $addressModel->lng = $googleCoordinatesComponent->getLng();
                        }
                        if (!($flag = $addressModel->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->refresh();
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'addressModels' => $addressModels,
            'programModuleModel' => $programModuleModel,
        ]);
    }

    /**
     * @param $moduleId
     * @return ProgrammeModule|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    private function findModuleModel($moduleId)
    {
        $model = ProgrammeModule::find()
            ->joinWith(['program.organization'])
            ->andWhere([
                'years.id' => $moduleId,
                'organization.user_id' => Yii::$app->user->getId()
            ])->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param integer $id
     * @return ProgramModuleAddress the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgramModuleAddress::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
