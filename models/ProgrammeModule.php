<?php

namespace app\models;

use Yii;
use app\models\Programs;

/**
 * This is the model class for table "years".
 *
 * @property integer $id
 * @property integer $program_id
 * @property integer $year
 * @property integer $month
 *
 * @property Programs $program
 */
class ProgrammeModule extends \yii\db\ActiveRecord
{
    public $selectyear1;
    public $selectyear2;
    public $selectyear3;
    public $selectyear4;
    public $selectyear5;
    public $selectyear6;
    public $selectyear7;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'years';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'hours', 'hoursindivid', 'hoursdop', 'kvfirst', 'kvdop'], 'required'],
            [[ 'hours', 'program_id', 'year', 'hoursdop', 'hoursindivid', 'minchild', 'maxchild',  'open', 'quality_control', 'p21z', 'p22z'], 'integer'],
            [['price','normative_price'], 'number'],
            [['month'], 'integer', 'max' => 12],
            [['kvfirst', 'kvdop'], 'string', 'max' => 255],
            [['minchild', 'maxchild'], 'integer', 'min' => 1],
            //['minchild', 'compare', 'compareAttribute' => 'maxchild', 'type' => 'number', 'operator' => '<='],
            //['maxchild', 'compare', 'compareAttribute' => 'minchild', 'type' => 'number', 'operator' => '>='],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Program ID',
            'year' => 'Год',
            'month' => 'Число месяцев реализации программы',
                        
            'hours' => 'Продолжительность реализации образовательной программы в часах',
            'kvfirst' => 'Квалификация педагогического работника непосредственно осуществляющего реализацию образовательной программы в группе детей',
            'hoursindivid' => 'Число часов работы педагогического работника, предусмотренное на индивидуальное сопровождение детей',
            'hoursdop' => 'Число часов сопровождения группы дополнительным педагогическим работником одновременно с педагогическим работником, непосредственно осуществляющим реализацию образовательной программы',
            'kvdop' => 'Квалификация педагогического работника, дополнительно привлекаемого для совместной реализации образовательной программы в группе',
            'minchild' => 'Ожидаемое минимальное число детей, обучающееся в одной группе',
            'maxchild' => 'Ожидаемое максимальное число детей, обучающееся в одной группе',
            'price' => 'Цена программы',
            'normative_price' => 'Нормативная стоимость',
            
            //'rating' => 'Рейтинг',
            //'limits' => 'Лимит зачисления',
            'open' => 'Зачисление',
            'previus' => 'Предварительная запись',
            'quality_control' => 'Число оценок качества',
            
            'p21z' => 'Квалификация педагогического работника непосредственно осуществляющего реализацию образовательной программы в группе детей',
            'p22z' => 'Квалификация педагогического работника, дополнительно привлекаемого для совместной реализации образовательной программы в группе',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }
    
    public function getYear($id)
    {
         $query = ProgrammeModule::find();

        $query->where(['id' => $id]);

        return $query->one();
    }
    
    public function getOpenYear()
    {
         $programs = new Programs();
        $program = $programs->getCooperateProgram();
        if (empty($program)) { $program = 0; }
        
         $rows = (new \yii\db\Query())
                ->select(['program_id'])
                ->from('years')
                ->where(['open' => 1])
                ->andWhere(['program_id' => $program])
              //  ->andWhere(['!=', 'id', 53])
           //  ->addParams([':id' => [51, 51]])
                ->column();
        return array_unique($rows);
    }
    
    public function getAllYear()
    {
         $programs = new Programs();
        $program = $programs->getCooperateProgram();
        if (empty($program)) { $program = 0; }
        
         $rows = (new \yii\db\Query())
                ->select(['program_id'])
                ->from('years')
                
                ->andWhere(['program_id' => $program])
              //  ->andWhere(['!=', 'id', 53])
           //  ->addParams([':id' => [51, 51]])
                ->column();
        return array_unique($rows);
    }
    
   /* public function getMyYear()
    {        
        $contracts = new Contracts();
        $contract = $contracts->getContractsYear();
        if (empty($contract)) { $contract = 0; }
        
         $rows = (new \yii\db\Query());
                $rows->select(['id']);
                $rows->from('years');
                $rows->where(['open' => 1]);
        //foreach ($contract as $value) {
                $rows->andWhere(['!=', 'id', 53]);
                $rows->column();
        return array_unique($rows);
    } */
    
}
