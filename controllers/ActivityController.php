<?php

namespace app\controllers;

use app\models\statics\DirectoryProgramActivity;
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
            $id = end($_POST['depdrop_parents']);
            $list = DirectoryProgramActivity::findAllActiveActivitiesByDirection($id);
            if (null !== $id && null !== $list) {
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
            $model->name = Yii::$app->request->post('name');
            $model->direction_id = Yii::$app->request->post('directionId');
            $model->user_id = Yii::$app->user->id;

            return $model->save() ? $model->id : null;
        }

        return Json::encode(['output' => '']);
    }
}
