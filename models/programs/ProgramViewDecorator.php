<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 06.12.17
 * Time: 16:25
 */

namespace app\models\programs;


use app\components\ModelDecorator;
use app\models\Contracts;
use app\models\Favorites;
use app\models\Groups;
use app\models\Informs;
use app\models\Mun;
use app\models\Organization;
use app\models\OrganizationAddress;
use app\models\ProgramAddressAssignment;
use app\models\ProgrammeModule;
use app\models\Programs;
use app\models\statics\DirectoryProgramActivity;
use app\models\statics\DirectoryProgramDirection;
use yii;
use yii\bootstrap\Alert;

/**
 * @property Programs $entity
 *  entity fields:
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
 * @property integer $p3z
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
 * @method  isADraft()
 *  ***
 */
class ProgramViewDecorator extends ModelDecorator
{
    public function getAlert(): string
    {
        if ($this->verification === Programs::VERIFICATION_DRAFT) {
            return Alert::widget([
                'options' => ['class' => 'alert-info'],
                'body' => 'Черновик'
            ]);
        }

        return '';
    }

    public function getHeadTemplate(): string
    {
        $headTemplate = '_base_head';
        if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
            $headTemplate = '_organisation_head';
        } elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_OPERATOR)) {
            $headTemplate = '_operator_head';
        } elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
            $headTemplate = '_certificate_head';
        }

        return $headTemplate;
    }
}
