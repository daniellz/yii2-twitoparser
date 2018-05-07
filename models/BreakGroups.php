<?php

namespace app\models;

use vova07\console\ConsoleRunner;
use Yii;

/**
 * This is the model class for table "break_groups".
 *
 * @property int $id
 * @property int $channel_id
 * @property string $color
 * @property string $words_list
 * @property array $words_list_arr
 *
 * @property Channels $channel
 * @property Breaks[] $breaks
 * @property int $markedCount
 */
class BreakGroups extends \yii\db\ActiveRecord
{
    public $words_list;
    private $words_list_arr;
    private $old_words_list_arr;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'break_groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['color'], 'default', 'value' => null],
            [['channel_id'], 'integer'],
            [['color'], 'string', 'max' => 255],
            [['words_list', 'channel_id'], 'required'],
            [['channel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Channels::className(), 'targetAttribute' => ['channel_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'channel_id' => 'Channel ID',
            'color' => 'Color',
            'words_list' => 'Markers',
            'markedCount'=> 'Marked twits'

        ];
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
        return $this->hasMany(Breaks::class, ['group_id' => 'id']);
    }

    public function getCheckeds()
    {
        return $this->hasMany(Checked::class, ['break_id', 'id'])->via('breaks');
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->words_list!='')
        {
            $this->makeWordsListFromStr();
            if($insert)
            {
                foreach ($this->words_list_arr as $item) {
                    $break = new Breaks();

                    $break->word = $item;
                    $break->group_id = $this->primaryKey;
                    $break->save();
                }
            }
            else
            {
                sort($this->old_words_list_arr);
                sort($this->words_list_arr);
                if($this->old_words_list_arr!==$this->words_list_arr)
                {
                    $for_del = array_diff($this->old_words_list_arr, $this->words_list_arr);
                    Breaks::deleteAll([
                        'and',
                        'group_id = :gid',
                        ['in', 'word', $for_del]],
                        [':gid'=>$this->primaryKey]
                    );

                    $for_ins = array_diff($this->words_list_arr, $this->old_words_list_arr);
                    foreach ($for_ins as $item) {
                        $break = new Breaks();
                        $break->start_filter = true;
                        $break->word = $item;
                        $break->group_id = $this->primaryKey;
                        $break->save();
                    }
                }
            }
        }

        if($insert)
        {
            $cr = new ConsoleRunner(['file' => Yii::getAlias('@app').'/yii']);
            $cr->run('twitter/filter '.$this->channel_id);
        }
    }

    public function afterFind()
    {
        parent::afterFind();

        if(!$this->isNewRecord && $this->words_list==null)
        {
            $this->collectWordList();
        }
    }

    private function makeWordsListFromStr()
    {
        $this->words_list_arr = array_map('trim', explode(',', $this->words_list));
    }

    private function makeWordsListFromArr()
    {
        $this->words_list = implode(', ', $this->words_list_arr);
    }

    private function collectWordList()
    {
        /** @var Breaks[] $breaks */
        $breaks = $this->getBreaks()->all();
        foreach ($breaks as $break) {
            $this->words_list_arr[] = $break->word;
        }
        $this->makeWordsListFromArr();
        $this->old_words_list_arr = $this->words_list_arr;
    }

    public function getMarkedCount()
    {
        return Yii::$app->db->createCommand('select count(c2.id) from break_groups as bg
  left join breaks b ON bg.id = b.group_id
  left join checked c2 ON b.id = c2.break_id
where bg.id = :bgid;', [':bgid' => $this->primaryKey])
            ->queryScalar();
    }

//    private function getOldWordList()
//    {
//        $out = [];
//        $breaks = $this->getBreaks()->all();
//        foreach ($breaks as $break) {
//            $this->words_list_arr[] = $break->word;
//        }
//    }
}
