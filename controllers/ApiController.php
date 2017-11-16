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
use app\models\ProgramAddressAssignment;
use app\models\Programs;
use app\models\statics\DirectoryProgramDirection;
use app\models\Years;
use Yii;
use yii\base\Controller;
use yii\db\Query;


const VERIFICATION_DONE = 2;

header("Access-Control-Allow-Origin: *");

class ApiController extends Controller
{
    public function actionProgram()
    {
        $query = new Query;
        $id = Yii::$app->request->get('id');
        $program = $query
            ->select([Programs::tableName() . '.*', 'SUM(' . Years::tableName() . '.month) as duration_month', 'SUM(' . Years::tableName() . '.hours) as duration_hours'])
            ->from(Programs::tableName())
            ->join('LEFT OUTER JOIN', Years::tableName(), Years::tableName() . '.program_id =  ' . Programs::tableName() . '.id')
            ->where([Programs::tableName() . '.verification' => VERIFICATION_DONE, Programs::tableName() . '.id' => $id])
            ->groupBy(Programs::tableName() . '.id')
            ->one();

        $this->send($program);
    }

    public function actionDirections()
    {

        $this->send(DirectoryProgramDirection::find()->all());

    }

    public function actionRegions()
    {

        $this->send(Operators::find()->select('DISTINCT(region)')->all());

    }

    public function actionMarks()
    {
        $query = new Query;

        $programsTable = Programs::tableName();
        $assignmentTable = ProgramAddressAssignment::tableName();
        $addressTable = OrganizationAddress::tableName();

        $query
            ->select(
                [
                    $programsTable . '.age_group_min as age_from',
                    $programsTable . '.age_group_max as age_to',
                    $programsTable . '.direction_id',
                    $programsTable . '.name as title',
                    $programsTable . '.annotation as description',

                    $assignmentTable . '.program_id',

                    $addressTable . '.lat',
                    $addressTable . '.lng',
                    $addressTable . '.address'
                ]
            )
            ->from($addressTable)
            ->innerJoin(
                $assignmentTable,
                "$assignmentTable.organization_address_id = $addressTable.organization_id"
            )
            ->innerJoin(
                $programsTable,
                "$programsTable.id = $assignmentTable.program_id"
            )
            ->where("$addressTable.status = 1 AND $addressTable.lng <> '' AND $addressTable.lat <> ''")->all();

        $command = $query->createCommand();
        $result = $command->queryAll();

        foreach ($result as $key => $mark) {
            $result[$key]['geo_code'] = [$mark['lat'], $mark['lng']];
        }
        $this->send($result);
    }

    public function send($data)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->data = $data;
        Yii::$app->response->send();
    }
}