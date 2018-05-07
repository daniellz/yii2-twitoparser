<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "twit_translates".
 *
 * @property int $id
 * @property int $twit_id
 * @property string $txt
 *
 * @property Twits $twit
 */
class TwitTranslates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'twit_translates';
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

    /**
     * @inheritdoc
     * @return TwitTranslatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TwitTranslatesQuery(get_called_class());
    }
}
