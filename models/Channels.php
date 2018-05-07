<?php

namespace app\models;

use Yii;
use vova07\console\ConsoleRunner;

/**
 * This is the model class for table "channels".
 *
 * @property int $id
 * @property string $url
 * @property bool $active
 * @property string $name
 *
 * @property BreakGroups[] $breakGroups
 * @property Twits[] $twits
 */
class Channels extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channels';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url'], 'required'],
            [['active'], 'boolean'],
            [['url', 'name'], 'string', 'max' => 255],
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
            'active' => 'Active',
            'name' => 'Label',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBreakGroups()
    {
        return $this->hasMany(BreakGroups::className(), ['channel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwits()
    {
        return $this->hasMany(Twits::className(), ['channel_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert)
        {
            $cr = new ConsoleRunner(['file' => Yii::getAlias('@app').'/yii']);
            $cr->run('twitter/history '.$this->primaryKey);
        }
    }
}
