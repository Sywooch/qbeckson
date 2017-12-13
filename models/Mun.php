<?php

namespace app\models;

use app\behaviors\UploadBehavior;
use app\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mun".
 *
 * @property integer        $id
 * @property string         $name
 * @property integer        $ground
 * @property integer        $nopc
 * @property integer        $pc
 * @property integer        $zp
 * @property integer        $dop
 * @property integer        $uvel
 * @property integer        $otch
 * @property integer        $otpusk
 * @property integer        $polezn
 * @property integer        $stav
 * @property integer        $deystv
 * @property integer        $lastdeystv
 * @property Payers         $payer
 * @property integer        $countdet
 * @property integer        $operator_id
 * @property integer        $mun_id
 * @property integer        $user_id
 * @property integer        $type
 * @property integer        $file
 * @property integer        $base_url
 *
 * @property Operators      $operator
 * @property Mun      $mun
 * @property Organization[] $organization
 */
class Mun extends ActiveRecord
{

    /**
     * Муниципалитет
     */
    const TYPE_MAIN = 1;

    /**
     * Заявка на изменение муниципалитета
     */
    const TYPE_APPLICATION = 2;

    /**
     * Отклоненная заявка на изменение
     */
    const TYPE_REJECTED = 3;

    /** Сценарий для подачи заявки на изменение муниципалитета */
    const SCENARIO_APPLICATION = 'application';

    public $confirmationFile;

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return [
            'uploadConfirmationFile' => [
                'class' => UploadBehavior::class,
                'attribute' => 'confirmationFile',
                'pathAttribute' => 'file',
                'baseUrlAttribute' => 'base_url',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mun';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'nopc', 'conopc', 'pc', 'copc', 'zp', 'cozp', 'dop', 'codop', 'uvel', 'couvel', 'otch', 'cootch', 'otpusk', 'cootpusk', 'polezn', 'copolezn', 'stav', 'costav', 'rob', 'corob', 'tex', 'cotex', 'est', 'coest', 'fiz', 'cofiz', 'xud', 'coxud', 'tur', 'cotur', 'soc', 'cosoc', 'deystv', 'countdet', 'lastdeystv'], 'required'],
            ['operator_id', 'integer'],
            [['nopc', 'conopc', 'pc', 'copc', 'zp', 'cozp', 'dop', 'codop', 'uvel', 'couvel', 'otch', 'cootch', 'otpusk', 'cootpusk', 'polezn', 'copolezn', 'stav', 'costav'], 'number'],
            [['rob', 'corob', 'tex', 'cotex', 'est', 'coest', 'fiz', 'cofiz', 'xud', 'coxud', 'tur', 'cotur', 'soc', 'cosoc', 'deystv', 'countdet', 'lastdeystv'], 'integer'],
            [['name', 'file', 'base_url'], 'string', 'max' => 255],
            [['confirmationFile'], 'safe', 'except' => self::SCENARIO_APPLICATION],
            [['confirmationFile'], 'required', 'on' => self::SCENARIO_APPLICATION],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'nopc' => 'Базовая потребность в приобретении услуг (кроме ПК)',
            'pc' => 'Базовая потребность в приобретении услуг ПК',
            'zp' => 'Средняя заработная плата педагогических работников в месяц на период',
            'dop' => 'Коэффициент привлечения дополнительных педагогических работников',
            'uvel' => 'Коэффициент увеличения на прочий персонал',
            'otch' => 'Коэффициент отчислений по оплате труда',
            'otpusk' => 'Коэффициент отпускных',
            'polezn' => 'Полезное использование помещений в неделю, часов',
            'stav' => 'Среднее количество ставок на одного педагога',
            'rob' => 'Техническая (робототехника)',
            'tex' => 'Техническая (иная)',
            'est' => 'Естественно-научная',
            'fiz' => 'Физкультурно-спортивная',
            'xud' => 'Художественная',
            'tur' => 'Туристско-краеведческая',
            'soc' => 'Социально-педагогическая',
            'conopc' => 'Базовая потребность в приобретении услуг (кроме ПК)',
            'copc' => 'Базовая потребность в приобретении услуг ПК',
            'cozp' => 'Средняя заработная плата педагогических работников в месяц на период',
            'codop' => 'Коэффициент привлечения дополнительных педагогических работников',
            'couvel' => 'Коэффициент увеличения на прочий персонал',
            'cootch' => 'Коэффициент отчислений по оплате труда',
            'cootpusk' => 'Коэффициент отпускных',
            'copolezn' => 'Полезное использование помещений в неделю, часов',
            'costav' => 'Среднее количество ставок на одного педагога',
            'corob' => 'Техническая (робототехника)',
            'cotex' => 'Техническая (иная)',
            'coest' => 'Естественно-научная',
            'cofiz' => 'Физкультурно-спортивная',
            'coxud' => 'Художественная',
            'cotur' => 'Туристско-краеведческая',
            'cosoc' => 'Социально-педагогическая',
            'deystv' => 'Число действующих в очередном учебном году сертификатов дополнительного образования',
            'lastdeystv' => 'Число действовавших в предыдущем учебном году сертификатов дополнительного образования',
            'countdet' => 'Общее число детей в возрасте от 5-ти до 18-ти лет, проживающее на территории муниципального района (городского округа)',
            'confirmationFile' => 'Файл-подтверждение',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Operators::className(), ['id' => 'operator_id'])->inverseOf('mun');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasMany(Organization::className(), ['mun' => 'id'])->inverseOf('municipality');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Payers::class, ['mun' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMun()
    {
        return $this->hasOne(self::className(), ['id' => 'mun_id']);
    }

    /**
     * @param string $attribute
     * @return null|string
     */
    public function getMunValue(string $attribute)
    {
        return ArrayHelper::getValue($this, ['mun', $attribute]);
    }

    /**
     * @param string $columns
     * @return static[]
     */
    public static function findAllRecords($columns = '*')
    {
        $query = static::find()
            ->select($columns)
            ->where(['operator_id' => Yii::$app->operator->identity->id]);

        return $query->all();
    }

    /**
     * Сравнивает значения атрибута заявки на изменение муниципалитета и самого муниципалитета
     * @param $attribute
     * @return bool
     */
    public function compareWithMunValue($attribute)
    {
        $mun = $this->mun;
        if (!$mun) {
            return false;
        } else {
            return ArrayHelper::getValue($mun, $attribute) == ArrayHelper::getValue($this, $attribute);
        }
    }

    /**
     * @return string
     */
    public function getFileUrl()
    {
        if ($this->base_url && $this->file) {
            return $this->base_url . '/' . $this->file;
        }

        return null;
    }
}
