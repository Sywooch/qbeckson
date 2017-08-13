<?php

namespace app\controllers\organization;

use app\components\GoogleCoordinates;
use app\models\OrganizationAddress;
use Yii;
use app\models\Model;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * AddressController implements the CRUD actions for OrganizationAddress model.
 */
class AddressController extends Controller
{
    /**
     * Lists all OrganizationAddress models.
     * @return mixed
     */
    public function actionIndex()
    {
        if ([] === ($models = $this->findModels())) {
            $models = [new OrganizationAddress];
            $scenario = 'create';
        } else {
            $scenario = 'update';
        }

        if (Yii::$app->request->post()) {
            if ($scenario === 'update') {
                $oldIDs = ArrayHelper::map($models, 'id', 'id');
                $models = Model::createMultiple(OrganizationAddress::class, $models);
                Model::loadMultiple($models, Yii::$app->request->post());
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($models, 'id', 'id')));
            } elseif ($scenario === 'create') {
                $models = Model::createMultiple(OrganizationAddress::class);
                Model::loadMultiple($models, Yii::$app->request->post());
            }

            $googleCoordinatesComponent = new GoogleCoordinates();
            foreach ($models as $model) {
                /** @var OrganizationAddress $model */
                $model->organization_id = Yii::$app->user->identity->organization->id;
                if ($model->isNewRecord || ($model->getOldAttribute('address') !== $model->address)) {
                    $googleCoordinatesComponent->setAddress($model->address);
                    $model->lat = $googleCoordinatesComponent->getLat();
                    $model->lng = $googleCoordinatesComponent->getLng();
                }
            }

            if (Model::validateMultiple($models)) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($scenario === 'update' && !empty($deletedIDs)) {
                        OrganizationAddress::deleteAll(['id' => $deletedIDs]);
                    }
                    foreach ($models as $model) {
                        if (!($flag = $model->save(false))) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

                        return $this->refresh();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('index', [
            'models' => $models,
        ]);
    }

    /**
     * @return OrganizationAddress[]|array|\yii\db\ActiveRecord[]
     */
    private function findModels()
    {
        return OrganizationAddress::find()
            ->andWhere(['organization_id' => Yii::$app->user->identity->organization->id])
            ->all();
    }
}
