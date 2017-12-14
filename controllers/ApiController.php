<?php
/**
 * pfdo
 * @file        ApiController.php
 *
 * @author      Sergey Sudnichnuikov <se@sdew.ru>
 *
 * @created 06.11.2017
 */

namespace app\controllers;

use app\models\Operators;
use app\models\OrganizationAddress;
use app\models\Programs;
use app\models\statics\DirectoryProgramDirection;
use Yii;
use yii\base\Controller;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\web\Response;


class ApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],// домены с которых разрешено отправлять запросы
            ],
        ];

        //Оставляем только формат json
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }


    public function actionProgram()
    {
        $id = Yii::$app->request->get('id');
        $program = Programs::getProgramData($id);
        return $program;
    }

    public function actionDirections()
    {
        return DirectoryProgramDirection::find()->all();
    }

    public function actionRegions()
    {
        return Operators::find()->select('DISTINCT(region)')->all();
    }

    public function actionMarks()
    {
        $result = OrganizationAddress::getAllMarks();
        return $result;
    }
}