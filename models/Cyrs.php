<?php

namespace app\models;

use app\components\Currency;
use DateInterval;
use DateTime;
use DateTimeZone;
use Yii;

/**
 * This is the model class for table "cyrs".
 *
 * @property int $id
 * @property string $timestamp
 * @property string $btc_usd
 * @property string $eth_btc
 * @property string $eth_usd
 *
 * @property TwitCyrs[] $twitCyrs
 */
class Cyrs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cyrs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timestamp', 'btc_usd', 'eth_btc', 'eth_usd'], 'required'],
            [['timestamp'], 'safe'],
            [['btc_usd', 'eth_btc', 'eth_usd'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timestamp' => 'Timestamp',
            'btc_usd' => 'Btc Usd',
            'eth_btc' => 'Eth Btc',
            'eth_usd' => 'Eth Usd',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTwitCyrs()
    {
        return $this->hasMany(TwitCyrs::className(), ['cyr_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return CyrsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CyrsQuery(get_called_class());
    }

    /**
     * @param DateTime $timestamp
     * @return Cyrs|false
     */
    public static function getCyrByTs(DateTime $timestamp)
    {
        //round seconds
        $timestamp->setTime($timestamp->format('H'), $timestamp->format('i'), 0);

        $now = new DateTime(null, new DateTimeZone("UTC"));
        if($timestamp>$now)
        {
            var_dump($now, $timestamp);
            echo 'ts from future'.PHP_EOL;
            return false;
        }

        $query = new CyrsQuery(get_called_class());
        $cyr = $query->where(['timestamp'=>$timestamp->format('Y.m.d H:i')])->one();
        if($cyr)
        {
            echo 'find in db'.PHP_EOL;
            return $cyr;
        }
        $api = new Currency();
        return $api->get($timestamp);
    }
}
