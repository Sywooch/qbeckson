<?php

namespace app\models;

use app\components\behaviors\ResizeImageAfterSaveBehavior;
use app\models\statics\DirectoryProgramActivity;
use app\models\statics\DirectoryProgramDirection;
use trntv\filekit\behaviors\UploadBehavior;
use voskobovich\linker\LinkerBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "municipal_task".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $verification
 * @property string $name
 * @property string $vid
 * @property integer $mun
 * @property integer $ground
 * @property string $groundName
 * @property integer $price
 * @property integer $normative_price
 * @property integer $rating
 * @property integer $limit
 * @property integer $study
 * @property integer $open
 * @property string $colse_date
 * @property string $task
 * @property string $annotation
 * @property integer $year
 * @property string $kvfirst
 * @property string $kvdop
 * @property integer $both_teachers
 * @property string $fullness
 * @property string $photo_base_url
 * @property string $photo_path
 * @property string $complexity
 * @property string $norm_providing
 * @property integer $ovz
 * @property integer $zab
 * @property string $age_group
 * @property integer $quality_control
 * @property string $link
 * @property string $certification_date
 * @property array $activity_ids
 * @property integer $direction_id
 * @property integer $age_group_min
 * @property integer $age_group_max
 * @property integer $is_municipal_task
 * @property string $zabAsString
 *
 * @property string $iconClass
 * @property string $defaultPhoto
 * @property bool $isActive
 *
 *
 * @property Contracts[] $contracts
 * @property Contracts[] $currentActiveContracts
 * @property Favorites[] $favorites
 * @property Groups[] $groups
 * @property Informs[] $informs
 * @property Organization $organization
 * @property ProgrammeModule[] $years
 * @property DirectoryProgramActivity[]|null $activities
 * @property DirectoryProgramDirection|null $direction
 * @property string $directivity
 * @property mixed $countMonths
 * @property mixed $organizationProgram
 * @property mixed $organizationWaitProgram
 * @property mixed $organizationNoProgram
 * @property Mun $municipality
 * @property mixed $cooperateProgram
 * @property mixed $countHours
 * @property string $commonActivities
 * @property ProgrammeModule[] $modules
 * @property OrganizationAddress[] $addresses
 * @property OrganizationAddress $mainAddress
 * @property ProgramAddressAssignment[] $addressAssignments
 * @property ProgramAddressAssignment[] $mainAddressAssignments
 */
class MunicipalTask extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'municipal_task';
    }

    /**
     * проверка на возможность участия в муниципальном задании
     * @return boolean
     */
    public function canCreateMunicipalTaskContract($certificate)
    {
        if (MunicipalTaskContract::findByProgram($this->id, $certificate)) {
            return false;
        }

        $matrix = MunicipalTaskPayerMatrixAssignment::findByPayerId($certificate->payer_id, $certificate->certGroup->is_special > 0 ? MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_AC : MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_PF);

        $arrayCanBeChosen = ArrayHelper::map($matrix, 'matrix_id', 'can_be_chosen');
        $arrayLimits = ArrayHelper::map($matrix, 'matrix_id', 'number');

        $tasksCount = MunicipalTaskContract::getCountContracts($certificate, $this->municipal_task_matrix_id);

        if ($arrayCanBeChosen[$this->municipal_task_matrix_id] < 1 || $arrayLimits[$this->municipal_task_matrix_id] - $tasksCount < 1) {
            return false;
        }

        return true;
    }
}
