<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Groups;

/**
 * GroupsSearch represents the model behind the search form about `app\models\Groups`.
 */
class GroupsSearch extends Groups
{
    public $studentsCount;
    public $requestsCount;
    public $placesCount;
    public $programName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'program_id', 'year_id'], 'integer'],
            [['studentsCount', 'requestsCount', 'placesCount', 'programName'], 'string'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'programName' => 'Программа',
            'studentsCount' => 'Обучающихся',
            'requestsCount' => 'Заявок',
            'placesCount' => 'Мест',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Groups::find()
            ->joinWith([
                'contracts',
                'module',
                'program'
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'groups.id' => $this->id,
            'groups.organization_id' => $this->organization_id,
            'groups.program_id' => $this->program_id,
            'groups.year_id' => $this->year_id,
        ]);

        $query
            ->andFilterWhere(['like', 'groups.name', $this->name])
            ->andFilterWhere(['like', 'programs.name', $this->programName])
        ;

        $query->groupBy(['groups.id']);

        return $dataProvider;
    }
}
