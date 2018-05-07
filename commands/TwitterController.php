<?php
/**
 * Created by PhpStorm.
 * User: Daniellz
 * Date: 09.04.2018
 * Time: 18:47
 */

namespace app\commands;


use app\components\Currency;
use app\components\Telegram;
use app\components\TelegramSettings;
use app\components\Twitter;
use app\models\Channels;
use app\models\Cyrs;
use app\models\TwitCyrs;
use DateTime;
use DateTimeZone;
use Yii;
use yii\console\Controller;
use yii\db\Query;

class TwitterController extends Controller
{

    public function actionTest()
    {
//        $query = new Query();
//        $query
//            ->select(['t.id as tid', 'b.id as bid'])
//            ->from('twit_text')
//            ->leftJoin('twits t', 'twit_text.twit_id = t.id')
//            ->leftJoin('break_groups bg', 'bg.channel_id = t.channel_id')
//            ->join('JOIN', 'breaks b', 'bg.id = b.group_id AND twit_text.txt ILIKE concat(\'%\', b.word, \'%\')')
//            ->leftJoin('checked c2',  'b.id = c2.break_id AND c2.twit_id = t.id')
//            ->where(['t.channel_id'=>10, 'c2.id'=> NULL, 't.id'=>[1,2,3,4]]);
//        var_dump($query->all());
//        var_dump($twit->twitText->txt);
//        $tg = new Telegram(TelegramSettings::marked());
//        $tg->checkRaw();
//        $tg->sendTwit();

    }

    /**
     * @param $channel_id
     *
     * Collect all twits from twitter
     */
    public function actionHistory($channel_id)
    {
        $channel = Channels::findOne($channel_id);
        $tw = new Twitter($channel);
        $tw->getHistory();
    }

    /**
     * @param $channel_id
     * @throws \yii\db\Exception
     *
     * Check twitter statusline
     */
    public function actionNew($channel_id)
    {
        $channel = Channels::findOne($channel_id);
        $tw = new Twitter($channel);
        $tg = new Telegram(TelegramSettings::marked());
        $stg = new Telegram(TelegramSettings::scum());

        //find new twits
        $twits = $tw->getNew();
//        die;
        if(count($twits)>0)
        {
            //find twits with mark words
            $ftwits = $tw->filter($twits);
            foreach ($ftwits as $ftwit) {
                $ftwit->translate();
                $tg->sendTwit($ftwit);
                unlink($twits[$ftwit->primaryKey]);
            }
        }
        foreach ($twits as $twit) {
            $stg->sendTwit($twit);
        }
    }

    /**
     * Check twitter statusline for all channels
     */
    public function actionNews()
    {
        $channels = Channels::find()->orderBy('id')->all();
        foreach ($channels as $channel) {
            Yii::$app->runAction('twitter/new', [$channel->primaryKey]);
            $cur = new Currency();
            $cur->setCyrs($channel);
        }
    }

    /**
     * Collect current currency (launch each minute from cron)
     */
    public function actionCollectCurrency()
    {
        Cyrs::getCyrByTs(new DateTime(null, new DateTimeZone('UTC')));
    }

    /**
     * @param $channel_id
     * @throws \yii\db\Exception
     *
     * Collect currency values on twitter post time
     */
    public function actionCollectInitialCyrs($channel_id)
    {
        $empty = Yii::$app->db->createCommand('select t.id, t.date from checked as c
left join twits t ON c.twit_id = t.id
  left join twit_cyrs t2 ON t.id = t2.twit_id
where t2.id is null and t.channel_id = :cid', [':cid' => $channel_id])->queryAll();
        foreach ($empty as $item) {
            $dt = new DateTime($item['date'], new DateTimeZone('UTC'));
            $dt = new DateTime($dt->format('Y-m-d H:i:00'), new DateTimeZone('UTC'));
            $cyr = Cyrs::getCyrByTs($dt);
            if($cyr)
            {
                $tc = new TwitCyrs();
                $tc->type = 0;
                $tc->twit_id = $item['id'];
                $tc->cyr_id = $cyr->primaryKey;
                $tc->save();
            }
        }
    }

    public function actionCheckTg()
    {
        $tg = new Telegram(TelegramSettings::marked());
        $tg->checkUpdates();
        $tg = new Telegram(TelegramSettings::scum());
        $tg->checkUpdates();
    }
}