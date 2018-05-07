<?php

namespace app\models;

use app\components\Currency;
use app\components\TranslateTwit;
use Yii;

/**
 * This is the model class for table "twits".
 *
 * @property int $id
 * @property string $url
 * @property int $channel_id
 * @property bool $retweet
 * @property string $date
 *
 * @property Checked[] $checkeds
 * @property Checked $checked0
 * @property TwitText $twitText
 * @property Channels $channel
 * @property Breaks $breaks
 * @property BreakGroups $breakGroups
 * @property TwitCyrs[] $twitCyrs
 * @property TwitTranslates $twitTranslates
 */
class Twits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'twits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['channel_id'], 'default', 'value' => null],
            [['channel_id'], 'integer'],
            [['retweet'], 'boolean'],
            [['date', 'checked0', 'checkeds', 'twitTexts', 'channel', 'breaks', 'breakGroups'], 'safe'],
            [['url'], 'string', 'max' => 255],
            [['channel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Channels::class, 'targetAttribute' => ['channel_id' => 'id']],
            ['url', 'unique', 'targetAttribute' => ['url', 'channel_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'channel_id' => 'Channel ID',
            'retweet' => 'Retweet',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckeds()
    {
        return $this->hasMany(Checked::class, ['twit_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecked0()
    {
        return $this->hasOne(Checked::class, ['twit_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwitText()
    {
        return $this->hasOne(TwitText::class, ['twit_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwitTranslates()
    {
        return $this->hasOne(TwitTranslates::class, ['twit_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(Channels::class, ['id' => 'channel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBreaks()
    {
        return $this->hasMany(Breaks::class, ['id'=>'break_id'])->via('checkeds');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBreakGroups()
    {
        return $this->hasMany(BreakGroups::class, ['id'=>'group_id'])->via('breaks');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwitCyrs()
    {
        return $this->hasMany(TwitCyrs::class, ['twit_id' => 'id'])->orderBy([TwitCyrs::tableName().'.type'=>SORT_ASC]);
    }

    /**
     * @return string
     */
    //TODO: move to widget
    public function getCyrsField()
    {
        $cyrs = $this->twitCyrs;
        $str = '';
        if(count($cyrs)==0)
            return $str;

        $str = '<table class="table table-striped table-sm"><thead><tr><th></th>';
        foreach (Currency::$pairs as $pair) {
            $str.= '<th>'.str_replace('_', '/', $pair).'</th>';
        }
        $str.='</tr></thead><tbody>';

        foreach ($cyrs as $item) {
            $str.='<tr>';
            $cyr = $item->cyr;
            switch ($item->type) {
                case 0:
                    $str.='<td class="fc">0</td>';
                    break;
                case 1:
                    $str.='<td class="fc">30</td>';
                    break;
                case 2:
                    $str.='<td class="fc">h</td>';
                    break;
                case 3:
                    $str.='<td class="fc">d</td>';
                    break;
                case 4:
                    $str.='<td class="fc">w</td>';
                    break;
                default:
                    break;
            }
            $str.='<td>'.rtrim($cyr->btc_usd, '0').'</td>'.'<td>'.rtrim($cyr->eth_btc, '0').'</td>'.'<td>'.rtrim($cyr->eth_usd, '0').'</td>'.'</tr>';
        }
        $str.='</tbody></table>';
        return $str;
    }

    public function translate()
    {
        if($this->twitText && $this->twitText->txt!=='')
        {
            if($this->twitTranslates && $this->twitTranslates->txt)
                return $this->twitTranslates->txt;
            $trans = new TranslateTwit($this);
            $translate = new TwitTranslates();
            $translate->txt = $trans->translate();
            $translate->link('twit', $this);
            return $translate->txt;
        }
    }
}
