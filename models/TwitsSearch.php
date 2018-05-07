<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Twits;
use yii\db\ActiveQuery;

/**
 * TwitsSearch represents the model behind the search form of `app\models\Twits`.
 */
class TwitsSearch extends Twits
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'channel_id'], 'integer'],
            [['url', 'date'], 'safe'],
            [['retweet'], 'boolean'],
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
        $query = Twits::find();

        // add conditions that should always apply here

        $query->with(['twitText', 'channel', 'checked0.break.group', 'twitCyrs.cyr']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date'=>SORT_DESC]
            ]
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
            'retweet' => $this->retweet,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['ilike', 'url', $this->url]);

        return $dataProvider;
    }

    public function searchBreaks(BreakGroups $breaks, $params)
    {
        $query = Twits::find();

        // add conditions that should always apply here
        $query->with([
            'channel',
            'twitText',
            'checkeds',
            'breaks',
            'twitCyrs.cyr'
//            'breakGroups'
//            'checked0.break.group' => function(ActiveQuery $query)use($breaks){
//                $query->andWhere('id='.$breaks->primaryKey);
//            }
//            'breakGroups'
        ]);

        $query->leftJoin(Checked::tableName().' c', self::tableName().'.id = c.twit_id');
        $query->leftJoin(Breaks::tableName().' b', 'c.break_id = b.id');
        $query->leftJoin(BreakGroups::tableName().' bg', 'b.group_id = bg.id');
//        $query->joinWith('breaks b');
//        $query->joinWith('breakGroups bg');

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
            'retweet' => $this->retweet,
            'date' => $this->date,
            'bg.id'=>$breaks->primaryKey
//            'channel.id'=>1
//            'break_groups.id'=>$breaks->primaryKey
//            'checked.break.group.id' => $breaks->primaryKey
        ]);

        $query->andFilterWhere(['ilike', 'url', $this->url]);

        return $dataProvider;
    }
}
