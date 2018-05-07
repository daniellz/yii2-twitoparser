<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "twit_text".
 *
 * @property int $id
 * @property int $twit_id
 * @property string $txt
 *
 * @property Twits $twit
 */
class TwitText extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'twit_text';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['twit_id'], 'default', 'value' => null],
            [['twit_id'], 'integer'],
            [['txt'], 'string'],
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
            'txt' => 'Txt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwit()
    {
        return $this->hasOne(Twits::className(), ['id' => 'twit_id']);
    }
}
