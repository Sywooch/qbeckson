<?php

namespace app\controllers;

use app\models\statics\DirectoryProgramActivity;
use app\models\statics\DirectoryProgramDirection;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * Class ActivityController
 * @package app\controllers
 */
class ActivityController extends Controller
{
    /**
     * @return string
     */
    public function actionLoadActivities()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $direction = end($_POST['depdrop_parents']);
            $list = DirectoryProgramActivity::findAllActiveActivitiesByDirection($direction);
            if (null !== $direction && null !== $list) {
                foreach ($list as $i => $activity) {
                    $out[] = ['id' => $activity->id, 'name' => $activity->name];
                }

                return Json::encode(['output' => $out]);
            }
        }

        return Json::encode(['output' => '']);
    }

    /**
     * @return string|null
     */
    public function actionAddActivity()
    {
        if (Yii::$app->request->post()) {
            $model = new DirectoryProgramActivity;
            $direction = DirectoryProgramDirection::findOne([
                'name' => Yii::$app->request->post('directionName')
            ]);
            $model->name = Yii::$app->request->post('name');
            $model->direction_id = $direction->id;
            $model->user_id = Yii::$app->user->id;
            $model->status = DirectoryProgramActivity::STATUS_NEW;

            return $model->save() ? $model->id : null;
        }

        return Json::encode(['output' => '']);
    }
}
