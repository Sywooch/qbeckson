<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "coefficient".
 *
 * @property integer $id
 * @property integer $p21v
 * @property integer $p21s
 * @property integer $p21o
 * @property integer $p22v
 * @property integer $p22s
 * @property integer $p22o
 * @property integer $p3v
 * @property integer $p3s
 * @property integer $p3n
 * @property integer $weekyear
 * @property integer $weekmonth
 * @property integer $pk
 * @property integer $norm
 * @property integer $potenc
 * @property integer $ngr
 * @property integer $sgr
 * @property integer $vgr
 * @property integer $chr1
 * @property integer $zmr1
 * @property integer $chr2
 * @property integer $zmr2
 * @property integer $blimrob
 * @property integer $blimtex
 * @property integer $blimest
 * @property integer $blimfiz
 * @property integer $blimxud
 * @property integer   $blimtur
 * @property integer   $blimsoc
 * @property integer   $ngrp
 * @property integer   $sgrp
 * @property integer   $vgrp
 * @property integer   $ppchr1
 * @property integer   $ppzm1
 * @property integer   $ppchr2
 * @property integer   $ppzm2
 * @property integer   $ocsootv
 * @property integer   $ocku
 * @property integer   $ocmt
 * @property integer   $obsh
 * @property integer   $ktob
 * @property integer   $vgs
 * @property integer   $sgs
 * @property integer   $pchsrd
 * @property integer   $pzmsrd
 * @property integer   $operator_id
 *
 * @property Operators $operator
 */
class Coefficient extends ActiveRecord
{
    const PROVISION_HI = 1;
    const PROVISION_MEDIUM = 2;
    const PROVISION_LOW = 3;
    const QUALIFICATION_FIRST = 2;
    const QUALIFICATION_HI = 1;
    const QUALIFICATION_OTHER = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coefficient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['p21v', 'p21s', 'p21o', 'p22v', 'p22s', 'p22o', 'p3v', 'p3s', 'p3n', 'weekyear', 'weekmonth', 'pk', 'norm', 'potenc', 'ngr', 'sgr', 'vgr', 'chr1', 'zmr1', 'chr2', 'zmr2', 'blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc', 'ngrp', 'sgrp', 'vgrp', 'ppchr1', 'ppzm1', 'ppchr2', 'ppzm2', 'ocsootv', 'ocku', 'ocmt', 'obsh', 'ktob', 'vgs', 'sgs', 'pchsrd', 'pzmsrd'], 'required'],
            ['operator_id', 'integer'],
            [['p21v', 'p21s', 'p21o', 'p22v', 'p22s', 'p22o', 'p3v', 'p3s', 'p3n', 'blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc', 'minraiting', 'weekyear', 'weekmonth', 'pk', 'norm', 'potenc', 'ngr', 'sgr', 'vgr', 'chr1', 'zmr1', 'chr2', 'zmr2', 'ngrp', 'sgrp', 'vgrp', 'ppchr1', 'ppzm1', 'ppchr2', 'ppzm2', 'ocsootv', 'ocku', 'ocmt', 'obsh', 'ktob', 'vgs', 'sgs', 'pchsrd', 'pzmsrd'], 'number' , 'min' => 0.01],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'p21v' => 'Высшая',
            'p21s' => 'Первая',
            'p21o' => 'Иная',
            'p22v' => 'Высшая',
            'p22s' => 'Первая',
            'p22o' => 'Иная',
            'p3v' => 'Высокое обеспечение',
            'p3s' => 'Среднее обеспечение',
            'p3n' => 'Низкое обеспечение',
            'weekyear' => 'Число недель в году',
            'weekmonth' => 'Число недель в месяце',
            'pk' => 'Периодичность ПК',
            'norm' => 'Норма нагрузки на одного педагога',
            'potenc' => 'Коэффициент потенциала увеличения числа оказываемых услуг по реализации дополнительных общеобразовательных программ',
            'ngr' => 'Нижняя граница рейтинга организации',
            'sgr' => 'Средняя граница рейтинга организации',
            'vgr' => 'Верхняя граница рейтинга организации',
            'chr1' => 'Параметр числителя рейтинга (1)',
            'zmr1' => 'Параметр знаменателя рейтинга (1)',
            'chr2' => 'Параметр числителя рейтинга (2)',
            'zmr2' => 'Параметр знаменателя рейтинга (2)',
            'blimrob' => 'Техническая (робототехника)',
            'blimtex' => 'Техническая (иная)',
            'blimest' => 'Естественно-научная',
            'blimfiz' => 'Физкультурно-спортивная',
            'blimxud' => 'Художественная',
            'blimtur' => 'Туристско-краеведческая',
            'blimsoc' => 'Социально-педагогическая',
            'ngrp' => 'Нижняя граница рейтинга программы',
            'sgrp' => 'Средняя граница рейтинга программы',
            'vgrp' => 'Верхняя граница рейтинга программы',
            'ppchr1' => 'Параметр числителя рейтинга (1)',
            'ppzm1' => 'Параметр знаменателя рейтинга (1)',
            'ppchr2' => 'Параметр числителя рейтинга (2)',
            'ppzm2' => 'Параметр знаменателя рейтинга (2)',
            'ocsootv' => 'Значимость оценки соответствия заявленных при включении образовательной программы в Реестр образовательных программ ожидаемых результатов ее освоения фактическому направлению развития ребенка при освоении образовательной программы',
            'ocku' => 'Значимость оценки кадровых условий реализации образовательной программы и соблюдения при реализации программы заявленных характеристик наполняемости',
            'ocmt' => 'Значимость оценки материально-технических условий реализации образовательной программы',
            'obsh' => 'Значимость общей удовлетворенности образовательной программы',
            'ktob' => 'Значимость коэффициента текучести обучающихся',
            'vgs' => 'Верхняя граница соотношения расторгнутых договоров',
            'sgs' => 'Средняя граница соотношения расторгнутых договоров',
            'pchsrd' => 'Параметр числителя соотношения расторгнутых договоров',
            'pzmsrd' => 'Параметр знаменателя соотношения расторгнутых договоров',
            'minraiting' => 'Минимальная доля оценок для определения рейтинга программы, %',
        ];
    }

    /** @return \yii\db\ActiveQuery */
    public function getOperator()
    {
        return $this->hasOne(Operators::className(), ['id' => 'operator_id'])->inverseOf('coefficient');
    }

    /**
     * @param $qualification
     * @return int|null
     */
    public function getMainTeacherCoefficient($qualification)
    {
        switch ($qualification) {
            case self::QUALIFICATION_HI:
                return $this->p21v;
            case self::QUALIFICATION_FIRST:
                return $this->p21s;
            case self::QUALIFICATION_OTHER:
                return $this->p21o;
            default:
                return null;
        }
    }

    /**
     * @param $qualification
     * @return int|null
     */
    public function getAdditionalTeacherCoefficient($qualification)
    {
        switch ($qualification) {
            case self::QUALIFICATION_HI:
                return $this->p22v;
            case self::QUALIFICATION_FIRST:
                return $this->p22s;
            case self::QUALIFICATION_OTHER:
                return $this->p22o;
            default:
                return null;
        }
    }

    /**
     * @param $coefficient
     * @return int|null
     */
    public function getProvision($coefficient) {
        switch ($coefficient) {
            case self::PROVISION_HI:
                return $this->p3v;
            case self::PROVISION_MEDIUM:
                return $this->p3s;
            case self::PROVISION_LOW:
                return $this->p3n;
            default:
                return null;
        }
    }


}
