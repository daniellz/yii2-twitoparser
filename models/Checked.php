<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "checked".
 *
 * @property int $id
 * @property int $twit_id
 * @property int $break_id
 *
 * @property Breaks $break
 * @property Twits $twit
 */
class Checked extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checked';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['twit_id', 'break_id'], 'default', 'value' => null],
            [['twit_id', 'break_id'], 'integer'],
            [['break_id'], 'exist', 'skipOnError' => true, 'targetClass' => Breaks::className(), 'targetAttribute' => ['break_id' => 'id']],
            [['twit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Twits::className(), 'targetAttribute' => ['twit_id' => 'id']],
            ['twit_id', 'unique', 'targetAttribute' => ['twit_id', 'break_id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'twit_id' => 'Twit ID',
            'break_id' => 'Break ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBreak()
    {
        return $this->hasOne(Breaks::className(), ['id' => 'break_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwit()
    {
        return $this->hasOne(Twits::className(), ['id' => 'twit_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if($insert)
        {
            $tc = new TwitCyrs();
            $tc->twit_id = $this->twit_id;
            $tc->collectCyr(TwitCyrs::TYPE_NOW);
        }
    }

}
