<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BreakGroups;

/**
 * BreakGroupsSearch represents the model behind the search form of `app\models\BreakGroups`.
 */
class BreakGroupsSearch extends BreakGroups
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'channel_id'], 'integer'],
            [['color'], 'safe'],
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
        $query = BreakGroups::find();
        $query->with('channel', 'breaks');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'channel_id' => $this->channel_id,
        ]);

        $query->andFilterWhere(['ilike', 'color', $this->color]);

        return $dataProvider;
    }
}
