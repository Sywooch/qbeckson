<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\KeyStorageItem;

/**
 * KeyStorageItemSearch represents the model behind the search form about `common\models\KeyStorageItem`.
 */
class KeyStorageItemSearch extends KeyStorageItem
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value', 'type', 'comment'], 'string'],
            [['operator_id', 'type'], 'integer'],
        ];
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
        $query = KeyStorageItem::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['operator_id' => $this->operator_id])
            ->andFilterWhere(['type' => $this->type])
        ;

        return $dataProvider;
    }
}
