<?php

namespace app\commands;

use app\components\Operator;
use app\models\TemporaryMergerId;
use yii;
use yii\console\Controller;
use yii\console\Exception;
use app\models\User;
use app\models\Operators;
use app\models\Coefficient;
use app\models\Mun;
use app\models\Payers;
use app\models\Organization;

class MergerController extends Controller
{
    public $merged = [
        'mun:\app\models\Mun',
        'coefficient:\app\models\Coefficient',
        'user:\app\models\User' => ['mun_id' => 'mun'],
        'auth_assignment:\app\models\AuthAssignment' => ['user_id' => 'user'],
        'user_search_filters_assignment:\app\models\UserSearchFiltersAssignment' => ['user_id' => 'user'],
        'directory_program_activity:\app\models\statics\DirectoryProgramActivity' => ['user_id' => 'user'],
        'organization:\app\models\Organization' => [
            'user_id' => 'user',
            'mun' => 'mun',
        ],
        'organization_document:\app\models\OrganizationDocument' => [
            'organization_id' => 'organization',
        ],
        'payers:\app\models\Payers' => [
            'user_id' => 'user',
            'mun' => 'mun',
        ],
        'programs:\app\models\Programs' => [
            'organization_id' => 'organization',
            'mun' => 'mun',
        ],
        'program_activity_assignment:\app\models\ProgramActivityAssignment' => [
            'program_id' => 'programs',
            'activity_id' => 'directory_program_activity',
        ],
        'years:\app\models\Years' => [
            'program_id' => 'programs',
        ],
        'cert_group:\app\models\CertGroup' => [
            'payer_id' => 'payers',
        ],
        'certificates:\app\models\Certificates' => [
            'user_id' => 'user',
            'payer_id' => 'payers',
            'cert_group' => 'cert_group',
        ],
        'groups:\app\models\Groups' => [
            'organization_id' => 'organization',
            'program_id' => 'programs',
            'year_id' => 'years',
        ],
        'contracts:\app\models\Contracts' => [
            'organization_id' => 'organization',
            'program_id' => 'programs',
            'year_id' => 'years',
            'group_id' => 'groups',
            'payer_id' => 'payers',
            'certificate_id' => 'certificates',
        ],
        'completeness:\app\models\Completeness' => [
            'group_id' => 'groups',
            'contract_id' => 'contracts',
        ],
        'cooperate:\app\models\Cooperate' => [
            'organization_id' => 'organization',
            'payer_id' => 'payers',
        ],
        'disputes:\app\models\Disputes' => [
            'contract_id' => 'contracts',
            'user_id' => 'user',
        ],
        'favorites:\app\models\Favorites' => [
            'organization_id' => 'organization',
            'program_id' => 'programs',
            'certificate_id' => 'certificates',
        ],
        'informs:\app\models\Informs' => [
            'program_id' => 'programs',
            'contract_id' => 'contracts',
        ],
        'invoices:\app\models\Invoices' => [
            'organization_id' => 'organization',
            'payers_id' => 'payers',
        ],/**/
    ];

    public $blackListUsernames = ['admin', 'operator'];

    public $blackListRoles = ['admins', 'operators'];

    public function actionMerge()
    {
        ini_set('memory_limit', '-1');
        Yii::$app->db->createCommand('TRUNCATE TABLE `temporary_merger_id`')->execute();
        $handle = fopen('log2.txt', 'a');
        foreach ($this->merged as $table => $columns) {
            if (!is_array($columns)) {
                $table = $columns;
            }
            $arrayTable = explode(':', $table);
            $table = $arrayTable[0];
            $model = $arrayTable[1];

            $data = $this->getData($table);
            foreach ($data as $index => $value) {
                if (isset($value['username']) && in_array($value['username'], $this->blackListUsernames)) {
                    continue;
                }
                if (isset($value['item_name']) && in_array($value['item_name'], $this->blackListRoles)) {
                    continue;
                }
                if (is_array($columns)) {
                    foreach ($columns as $attribute => $parentTable) {
                        if (empty($value[$attribute])) {
                            continue;
                        }
                        $value[$attribute] = $this->getNewValue($parentTable, $value[$attribute]);
                    }
                }

                $model = new $model;
                $model->attributes = $value;
                if (!$model->validate()) {
                    $error = 'Error while saving data (' . $table . ' - ' . (!isset($value['id']) ?: $value['id']) . ')' . PHP_EOL;
                    echo $error;
                    fwrite($handle, $error);
                    continue;
                }
                if ($model->save(false)) {
                    if (isset($value['id'])) {
                        echo 'Adding (' . $table . ' - ' . $value['id'] . ')' . PHP_EOL;
                        $this->addOldId($table, $value['id'], $model->id);
                    } else {
                        echo 'Adding (' . $table . ')' . PHP_EOL;
                    }
                } else {
                    print_r($model->errors);exit;
                }
            }
        }

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionImportOperator($newLogin)
    {
        if (User::findOne(['username' => $newLogin])) {
            throw new Exception('This login already exists.');
        }

        $query = (new \yii\db\Query)
            ->select('*')
            ->from('operators');
        $operator = $query->createCommand(Yii::$app->db_merge)->queryOne();
        $newOperator = new Operators;
        $newOperator->attributes = $operator;

        $query = (new \yii\db\Query)
            ->select('*')
            ->from('user')
            ->where(['id' => $operator['user_id']]);
        $user = $query->createCommand(Yii::$app->db_merge)->queryOne();
        $newUser = new User;
        $newUser->attributes = $user;
        $newUser->username = $newLogin;
        if ($newUser->save()) {
            $newOperator->user_id = $newUser->id;
            if ($newOperator->save()) {
                $operatorRole = Yii::$app->authManager->getRole('operators');
                Yii::$app->authManager->assign($operatorRole, $newUser->id);

                return Controller::EXIT_CODE_NORMAL;
            } else {
                $newUser->delete();
            }
        }

        return Controller::EXIT_CODE_ERROR;
    }

    public function actionSetOperator($operatorId = 1, $region = 14)
    {
        $operator = $this->findOperator($operatorId);
        if (empty($operator->region)) {
            $operator->setRegion($region);
        }

        Coefficient::updateAll(['operator_id' => $operator->id], ['operator_id' => null]);
        Mun::updateAll(['operator_id' => $operator->id], ['operator_id' => null]);
        Payers::updateAll(['operator_id' => $operator->id], ['operator_id' => null]);

        $organizations = Organization::findWithoutOperator($operator->id);
        foreach ($organizations as $organization) {
            $operator->link('organizations', $organization);
        }

        return Controller::EXIT_CODE_ERROR;
    }

    protected function addOldId($table, $oldId, $newId)
    {
        $model = new TemporaryMergerId([
            'table_name' => $table,
            'old_id' => $oldId,
            'new_id' => $newId,
        ]);

        return $model->save();
    }

    protected function getNewValue($table, $oldId)
    {
        $query = (new \yii\db\Query)
            ->select('new_id')
            ->from('temporary_merger_id')
            ->where([
                'table_name' => $table,
                'old_id' => $oldId,
            ]);

        return $query->createCommand()->queryScalar();
    }

    protected function getData($table)
    {
        $query = (new \yii\db\Query)
            ->select('*')
            ->from($table);

        return $query->createCommand(Yii::$app->db_merge)->queryAll();
    }

    protected function findOperator($id)
    {
        if (($model = Operators::findOne($id)) !== null) {
            return $model;
        } else {
            throw new Exception('Operator does not exist.');
        }
    }
}
