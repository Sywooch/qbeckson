<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Favorites;

/**
 * FavoritesSearch represents the model behind the search form about `app\models\Favorites`.
 */
class PreviusSearch extends Previus
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'certificate_id', 'year_id', 'organization_id', 'program_id', 'actual'], 'integer'],
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
        $query = Previus::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }



        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'certificate_id' => $this->certificate_id,
            'year_id' => $this->year_id,
            'organization_id' => $this->organization_id,
            'program_id' => $this->program_id,
            'actual' => $this->actual,
        ]);

        return $dataProvider;
    }
}
