<?php
/**
 * Created by PhpStorm.
 * User: Daniellz
 * Date: 01.05.2018
 * Time: 10:30
 */

namespace app\components;


use app\models\Twits;
use GuzzleHttp\Client;
use Yii;

class Telegram
{
    private $users_file;
    private $last_id_file;
    private $secret;
    private $token;
    private $socket;

    /**
     * @var int
     */
    private $last_id;
    /**
     * @var Client
     */
    private $client;

    private $api_url;

    private $users=[];
    private $stopword;

    public function __construct(TelegramSettings $settings)
    {

        $this->fillSettings($settings);
        $params = ['proxy'=>$this->socket];
        $this->api_url = 'https://api.telegram.org/bot'.$this->token;
        $this->client = new Client($params);
        $this->last_id = file_get_contents(Yii::getAlias($this->last_id_file));
        $this->getUsers();
    }

    public function checkUpdates()
    {
        $result = $this->client->get($this->api_url.'/getUpdates', ['query'=>['offset'=>$this->last_id+1]]);
        $ans = json_decode($result->getBody(), true);
        if($ans && !empty($ans['ok']) && $ans['ok']==true)
        {
            foreach ($ans['result'] as $result) {
                if($result['update_id']>$this->last_id)
                    $this->last_id = (int)$result['update_id'];
                if($result['message']['text']===$this->secret)
                {
                    $this->registerUser($result['message']['from']['id'], $result['message']['from']['first_name']);
                }
                if($result['message']['text']===$this->stopword)
                {
                    $this->unsubscribeUser($result['message']['from']['id']);
                }
            }
            file_put_contents(Yii::getAlias($this->last_id_file), $this->last_id);
        }
    }

    public function checkRaw()
    {
        $result = $this->client->get($this->api_url.'/getUpdates', ['query'=>['offset'=>$this->last_id+1]]);
        $ans = json_decode($result->getBody(), true);
        var_dump($ans);
    }

    public function registerUser($id, $name)
    {
        if(!array_key_exists($id, $this->users))
        {
            $this->users[$id] = $name;
            file_put_contents(Yii::getAlias($this->users_file), json_encode($this->users));
            $this->sayHello($id);
        }
    }

    private function getUsers()
    {
        $this->users = json_decode(file_get_contents(Yii::getAlias($this->users_file)), true);
        if($this->users==null)
            $this->users = [];
    }

    public function sendTwit(Twits $twit)
    {
        foreach ($this->users as $user=>$name) {
            $link = 'https://twitter.com/'.$twit->channel->url;
            $txt = ($twit->twitTranslates && $twit->twitTranslates->txt)?$twit->twitTranslates->txt:$twit->twitText->txt;

            $message = '<a href="'.$link.'">'.$link.'</a>'.PHP_EOL
                .$txt.PHP_EOL
                .'<a href="'.$link.'/status/'.$twit->url.'">Twit</a>';
            $result = $this->client->post($this->api_url.'/sendMessage', ['form_params'=>['chat_id'=>$user, 'text'=>$message, 'parse_mode'=>'HTML']]);
        }
    }

    private function sayHello($id)
    {
        $message = 'Hello '.$this->users[$id].'! Now, you will get new twits as soon as possible!';
        $result = $this->client->post($this->api_url.'/sendMessage', ['form_params'=>['chat_id'=>$id, 'text'=>$message]]);
    }

    /**
     * @param TelegramSettings $settings
     */
    private function fillSettings(TelegramSettings $settings)
    {
        $this->users_file = $settings->users_file;
        $this->last_id_file = $settings->last_id_file;
        $this->secret = $settings->secret;
        $this->token = $settings->token;
        $this->socket = $settings->socket;
        $this->stopword = $settings->stopword;
    }

    private function unsubscribeUser($id)
    {
        if(array_key_exists($id, $this->users))
        {
            $this->sayGoodbye($id);
            unset($this->users[$id]);
            file_put_contents(Yii::getAlias($this->users_file), json_encode($this->users));
        }
    }

    private function sayGoodbye($id)
    {
        $message = 'Goodbye '.$this->users[$id].'! We don\'t bother you anymore!';
        $result = $this->client->post($this->api_url.'/sendMessage', ['form_params'=>['chat_id'=>$id, 'text'=>$message]]);
    }


}
