<?php


namespace app\components;

use app\models\Channels;
use app\models\Checked;
use app\models\Twits;
use app\models\TwitText;
use DateTime;
use TwitterAPIExchange;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: Daniellz
 * Date: 09.04.2018
 * Time: 18:39
 */

class Twitter
{
    private $settings;

    public $max_id;
    public $limit = 3200;

    public $since_id;

    /**
     * @var Channels
     */
    private $channel;

    private $ans=[];

    public $filter = true;


    public function __construct(Channels $channel)
    {
        $this->channel = $channel;
        $this->settings = Yii::$app->params['twitter-settings'];
    }


    public function getTimeline()
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $requestMethod = 'GET';

        $postfields = [
            'screen_name' => $this->channel->url,
            'count'=>$this->limit,
            'trim_user'=>1,
            'exclude_replies'=>1,
            'tweet_mode'=>'extended',
            'include_rts'=>1
        ];
        if($this->filter==false)
        {
            $postfields['trim_user'] = false;
            $postfields['exclude_replies'] = false;
        }
        if($this->max_id)
        {
            $postfields['max_id'] = $this->max_id;
        }

        if($this->since_id)
        {
            $postfields['since_id'] = $this->since_id;
        }
//        var_dump(http_build_query($postfields));
        $twitter = new TwitterAPIExchange($this->settings);
        $ans = $twitter->setGetfield('?'.http_build_query($postfields))
            ->buildOauth($url, $requestMethod)
            ->performRequest();
        $this->ans = json_decode($ans, true);
    }

    /**
     * @return Twits[]
     */
    public function saveTwits()
    {
        $twits = [];
        foreach ($this->ans as $an) {
            $twit = new Twits();
            $twit->channel_id = $this->channel->primaryKey;
            $twit->date = (new DateTime($an['created_at']))->format('Y-m-d H:i:s');
            $twit->retweet = (array_key_exists('retweeted_status', $an));
            $twit->url = $an['id_str'];
            if($twit->save())
            {
                $txt = new TwitText();
//                $txt->twit_id = $twit->primaryKey;
                $txt->txt = ($twit->retweet)?$an['retweeted_status']['full_text']:$an['full_text'];
//                $txt->save();
                $txt->link('twit', $twit);
            }
            $twits[$twit->primaryKey] = $twit;
            $this->max_id = $an['id_str'];
            echo 'save twit '.$this->max_id.' with date '.$twit->date.PHP_EOL;
        }
        return $twits;
    }

    public function getHistory()
    {
        $i=0;
        $getNext = true;
        while($getNext)
        {
            echo 'parsing '.$i.PHP_EOL;
            $i++;
            $this->getTimeline();
            echo ' count '.count($this->ans).PHP_EOL;
            echo ' max_id = '.$this->max_id.PHP_EOL;

            if(count($this->ans)==1 && $this->ans[0]['id_str']==$this->max_id)
            {
                $this->filter = false;
                echo 'get one twit with max id, parsing without filters'.PHP_EOL;
                $this->getTimeline();
                $this->max_id = end($this->ans)['id_str'];
                echo ' count '.count($this->ans).PHP_EOL;
                echo ' max_id = '.$this->max_id.PHP_EOL;
                $this->filter = true;
            }
            else
            {
                $this->saveTwits();
            }



            $getNext = count($this->ans)>0;
        }
    }

    /**
     * @return Twits[]
     * @throws \yii\db\Exception
     */
    public function getNew()
    {
        $this->since_id = Yii::$app->db->createCommand('select max(url) from twits where channel_id = :cid', [':cid' => $this->channel->primaryKey])->queryScalar();
//        var_dump($this->since_id);
        $this->getTimeline();
        echo ' count '.count($this->ans).PHP_EOL;
        echo ' max_id = '.$this->max_id.PHP_EOL;
        return $this->saveTwits();
    }

    /**
     * @param $twits Twits[]
     * @return Twits[]
     */
    public function filter($twits)
    {
        $twits_id = ArrayHelper::getColumn($twits, 'id');
        $out = [];
        $query = new Query();
        $breaks = $query
            ->select(['t.id as tid', 'b.id as bid'])
            ->from('twit_text')
            ->leftJoin('twits t', 'twit_text.twit_id = t.id')
            ->leftJoin('break_groups bg', 'bg.channel_id = t.channel_id')
            ->join('JOIN', 'breaks b', 'bg.id = b.group_id AND twit_text.txt ILIKE concat(\'%\', b.word, \'%\')')
            ->leftJoin('checked c2',  'b.id = c2.break_id AND c2.twit_id = t.id')
            ->where(['t.channel_id'=>$this->channel->primaryKey, 'c2.id'=> NULL, 't.id'=>$twits_id])->all();
        foreach ($breaks as $break) {
            $checked = new Checked();
            $checked->twit_id = $break['tid'];
            $checked->break_id = $break['bid'];
            $checked->save();
            $out[$break['tid']] = $twits[$break['tid']];
        }
        return $out;
    }
}