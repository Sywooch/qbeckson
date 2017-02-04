<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mun".
 *
 * @property integer $id
 * @property string $name
 * @property integer $ground
 * @property integer $nopc
 * @property integer $pc
 * @property integer $zp
 * @property integer $dop
 * @property integer $uvel
 * @property integer $otch
 * @property integer $otpusk
 * @property integer $polezn
 * @property integer $stav
 * @property integer $deystv
 * @property integer $lastdeystv
 * @property integer $countdet
 */
class Mun extends \yii\db\ActiveRecord
{
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
            [['nopc', 'conopc', 'pc', 'copc', 'zp', 'cozp', 'dop', 'codop', 'uvel', 'couvel', 'otch', 'cootch', 'otpusk', 'cootpusk', 'polezn', 'copolezn', 'stav', 'costav'], 'number'],
            [['rob', 'corob', 'tex', 'cotex', 'est', 'coest', 'fiz', 'cofiz', 'xud', 'coxud', 'tur', 'cotur', 'soc', 'cosoc', 'deystv', 'countdet', 'lastdeystv'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'countdet' => 'Общее число детей в возрасте от 5-ти до 18-ти лет, проживающее на территории муниципального района (городского округа)'
        ];
    }
}
