<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "years".
 *
 * @property integer $id
 * @property string $name
 * @property integer $program_id
 * @property integer $year
 * @property integer $month
 * @property integer $hours
 * @property string $kvfirst
 * @property string $kvdop
 * @property integer $hoursindivid
 * @property integer $hoursdop
 * @property integer $minchild
 * @property integer $maxchild
 * @property double $price
 * @property double $normative_price
 * @property integer $rating
 * @property integer $limits
 * @property integer $open
 * @property integer $previus
 * @property integer $quality_control
 * @property integer $p21z
 * @property integer $p22z
 * @property string $results
 *
 * @property Contracts[] $contracts
 * @property Groups[] $groups
 * @property Previus[] $previuses
 * @property Programs $program
 */
class Years extends \yii\db\ActiveRecord
{
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
            [['program_id', 'year', 'month', 'hours', 'kvfirst', 'kvdop', 'hoursindivid', 'hoursdop', 'minchild', 'maxchild', 'price', 'normative_price', 'rating', 'limits', 'open', 'quality_control'], 'required'],
            [['program_id', 'year', 'month', 'hours', 'hoursindivid', 'hoursdop', 'minchild', 'maxchild', 'rating', 'limits', 'open', 'previus', 'quality_control', 'p21z', 'p22z'], 'integer'],
            [['price', 'normative_price'], 'number'],
            [['results'], 'string'],
            [['name', 'kvfirst', 'kvdop'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'program_id' => 'Program ID',
            'year' => 'Year',
            'month' => 'Month',
            'hours' => 'Hours',
            'kvfirst' => 'Kvfirst',
            'kvdop' => 'Kvdop',
            'hoursindivid' => 'Hoursindivid',
            'hoursdop' => 'Hoursdop',
            'minchild' => 'Minchild',
            'maxchild' => 'Maxchild',
            'price' => 'Price',
            'normative_price' => 'Normative Price',
            'rating' => 'Rating',
            'limits' => 'Limits',
            'open' => 'Open',
            'previus' => 'Previus',
            'quality_control' => 'Quality Control',
            'p21z' => 'P21z',
            'p22z' => 'P22z',
            'results' => 'Results',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contracts::className(), ['year_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Groups::className(), ['year_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreviuses()
    {
        return $this->hasMany(Previus::className(), ['year_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }
}
