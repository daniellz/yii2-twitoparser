<?php
/**
 * Created by PhpStorm.
 * User: Daniellz
 * Date: 15.04.2018
 * Time: 13:27
 */

namespace app\components;


use app\models\Channels;
use app\models\Cyrs;
use app\models\TwitCyrs;
use DateTime;
use DateTimeZone;
use Yii;

class Currency
{


    private $url = 'https://min-api.cryptocompare.com/data/histominute?';
    static public $pairs = ['btc_usd', 'eth_btc', 'eth_usd'];
    /**
     * @var DateTime
     */
    private $ts;

    /**
     * @param $timestamp
     * @return Cyrs|false
     */
    public function get($timestamp)
    {
        $now = new DateTime(null, new DateTimeZone('UTC'));
        $this->ts = $timestamp;

        $curs = [];
        if(($now->getTimestamp() - $this->ts->getTimestamp()) <= 5*60)
        {
            echo 'get now'.PHP_EOL;
            $curs = $this->getnow();
        }
        else
        {
            if(($now->getTimestamp() - $this->ts->getTimestamp()) >= 7*24*60*60)
            {
                echo 'more than 7 days ago, get hourly'.PHP_EOL;
                $this->url = str_replace('minute', 'hour', $this->url);
            }

            echo 'get old'.PHP_EOL;
//            die;
            foreach (self::$pairs as $pair) {
                list($from, $to) = explode('_', $pair);
                $c = $this->getCur($from, $to);
                if($c)
                    $curs[$pair] = $c;
                else
                    return false;
            }
        }
        return $this->save($curs);
    }

    private function getnow()
    {
        $ans = file_get_contents('https://min-api.cryptocompare.com/data/pricemulti?fsyms=BTC,ETH&tsyms=USD,BTC');
        $curs = [];
        if($ans && $ans = json_decode($ans, true))
        {
            $curs['btc_usd'] = $ans['BTC']['USD'];
            $curs['eth_btc'] = $ans['ETH']['BTC'];
            $curs['eth_usd'] = $ans['ETH']['USD'];
            return $curs;
        }
        return false;
    }

    private function getCur($from, $to)
    {
        $params = [
            'fsym'=>mb_strtoupper($from),
            'tsym'=>mb_strtoupper($to),
            'limit'=>0,
            'aggregate'=>1,
            'toTs'=>$this->ts->format('U')
        ];
        $url = $this->url.http_build_query($params);
        echo 'sleep'.PHP_EOL;
        sleep(1);
        $ans = file_get_contents($url);
        if($ans && ($ans = json_decode($ans, true)) && array_key_exists('Response', $ans) && $ans['Response']=='Success' && count($ans['Data'])>0)
        {
            return ($ans['Data'][0]['high'] + $ans['Data'][0]['low']) / 2;
        }
        else
        {
            die('STOP!');
        }
        return false;
    }

    private function save($curs)
    {
        $cur = new Cyrs();
        $cur->timestamp = $this->ts->format('Y-m-d H:i:s');
        $cur->btc_usd = $curs['btc_usd'];
        $cur->eth_btc = $curs['eth_btc'];
        $cur->eth_usd = $curs['eth_usd'];
        $cur->save();
        return $cur;
    }

    /**
     * @param Channels $channel
     *
     * Find currency values for twit at +30min, +hour, +day, +week from posting time
     */
    public function setCyrs(Channels $channel)
    {
        $twits = $this->getTimeTwits($channel->primaryKey, TwitCyrs::TYPE_30);
        $this->runTwits($twits, TwitCyrs::TYPE_30);
        $twits = $this->getTimeTwits($channel->primaryKey, TwitCyrs::TYPE_HOUR);
        $this->runTwits($twits, TwitCyrs::TYPE_HOUR);
        $twits = $this->getTimeTwits($channel->primaryKey, TwitCyrs::TYPE_DAY);
        $this->runTwits($twits, TwitCyrs::TYPE_DAY);
        $twits = $this->getTimeTwits($channel->primaryKey, TwitCyrs::TYPE_WEEK);
        $this->runTwits($twits, TwitCyrs::TYPE_WEEK);
    }

    private function getTimeTwits($channel_id, $type)
    {
        $types = ['', '30 minutes', '1 hour', '1 day', '1 week'];
        $interval = $types[$type];
        return Yii::$app->db->createCommand('select tc.twit_id, c3.id, c2.timestamp + INTERVAL \''.$interval.'\' as time from twit_cyrs as tc
  left join twit_cyrs as tc2 on tc2.twit_id = tc.twit_id and tc2.type=:type
left join cyrs as c2 ON tc.cyr_id = c2.id
  left join cyrs as c3 on c3.timestamp = c2.timestamp + INTERVAL \''.$interval.'\'
  left join twits t ON tc.twit_id = t.id
where tc.type = 0 and t.channel_id = :cid and tc2.id is null', ['cid'=>$channel_id, 'type'=>$type])->queryAll();
    }

    private function runTwits($twits, $type)
    {
        foreach ($twits as $twit) {
            if($twit['id']!=null && $twit['id']>0)
            {
                $tc = new TwitCyrs();
                $tc->twit_id = $twit['twit_id'];
                $tc->cyr_id = $twit['id'];
                $tc->type = $type;
                $tc->save();
            }
            else
            {
                $t = new DateTime($twit['time'], new DateTimeZone('UTC'));
                if($t<=new DateTime(null, new DateTimeZone('UTC')))
                {
                    $cur = $this->get($t);
                    $tc = new TwitCyrs();
                    $tc->twit_id = $twit['twit_id'];
                    $tc->cyr_id = $cur->primaryKey;
                    $tc->type = $type;
                    $tc->save();
                }
            }
        }
    }
}