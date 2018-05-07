<?php

namespace app\models;

use DateTime;
use DateTimeZone;
use Yii;

/**
 * This is the model class for table "twit_cyrs".
 *
 * @property int $id
 * @property int $twit_id
 * @property int $cyr_id
 * @property int $type
 *
 * @property Cyrs $cyr
 * @property Twits $twit
 */
class TwitCyrs extends \yii\db\ActiveRecord
{

    const TYPE_NOW = 0;
    const TYPE_30 = 1;
    const TYPE_HOUR = 2;
    const TYPE_DAY = 3;
    const TYPE_WEEK = 4;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'twit_cyrs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['twit_id', 'cyr_id'], 'required'],
            [['twit_id', 'cyr_id', 'type'], 'default', 'value' => null],
            [['twit_id', 'cyr_id', 'type'], 'integer'],
            [['cyr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cyrs::className(), 'targetAttribute' => ['cyr_id' => 'id']],
            [['twit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Twits::className(), 'targetAttribute' => ['twit_id' => 'id']],
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
            'cyr_id' => 'Cyr ID',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCyr()
    {
        return $this->hasOne(Cyrs::className(), ['id' => 'cyr_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwit()
    {
        return $this->hasOne(Twits::className(), ['id' => 'twit_id']);
    }

    /**
     * @inheritdoc
     * @return TwitCyrsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TwitCyrsQuery(get_called_class());
    }

    /**
     * @param $type int
     */
    public function collectCyr($type)
    {
        $ts = new DateTime($this->twit->date, new DateTimeZone('UTC'));
        $cyr = Cyrs::getCyrByTs($ts);
        $this->cyr_id = $cyr->primaryKey;
        $this->type = $type;
        $this->save();
        var_dump($this);
}
}
