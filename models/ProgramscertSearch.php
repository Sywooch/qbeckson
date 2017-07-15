<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Programs;

/**
 * ProgramsSearch represents the model behind the search form about `app\models\Programs`.
 */
class ProgramscertSearch extends Programs
{
     public $organization;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'verification', 'rating', 'limit', 'study', 'open', 'ovz', 'quality_control'], 'integer'],
            [['name', 'task', 'annotation', 'link', 'certification_date', 'organization'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Programs::find()
            ->joinWith(['municipality'])
            ->where('`mun`.operator_id = ' . Yii::$app->operator->identity->id);

       // $query->joinWith(['organization']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);
        
        //$dataProvider->sort->attributes['organization'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
        //    'asc' => ['organization.name' => SORT_ASC],
        //    'desc' => ['organization.name' => SORT_DESC],
        //];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'organization_id' => $organization['id'],
            'verification' => 2,
            'rating' => $this->rating,
            'limit' => $this->limit,
            'study' => $this->study,
            'open' => $this->open,
            'ovz' => $this->ovz,
            'quality_control' => $this->quality_control,
            'certification_date' => $this->certification_date,
        ]);

        $query->andFilterWhere(['like', '`programs`.name', $this->name])
            ->andFilterWhere(['like', 'directivity', $this->directivity])
            ->andFilterWhere(['like', 'task', $this->task])
            ->andFilterWhere(['like', 'annotation', $this->annotation])
            ->andFilterWhere(['like', 'link', $this->link]);
           // ->andFilterWhere(['like', 'organization.name', $this->organization]);

        return $dataProvider;
    }
}
