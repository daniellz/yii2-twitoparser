<?php
/**
 * Created by PhpStorm.
 * User: Daniellz
 * Date: 02.05.2018
 * Time: 17:10
 */

namespace app\components;


use app\models\Twits;
use Google\Cloud\Translate\TranslateClient;
use Yii;

class TranslateTwit
{
    /**
     * @var Twits
     */
    private $twit;

    public function __construct(Twits $twit)
    {
        $this->twit = $twit;
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.Yii::getAlias('@app').'/config/'.Yii::$app->params['google-auth-file']);
    }

    public function translate()
    {
        $projectId = Yii::$app->params['google-project-id'];
        $translate = new TranslateClient([
            'projectId' => $projectId
        ]);
        $text = $this->twit->twitText->txt;
        # The target language
        $target = 'ru';
        $translation = $translate->translate($text, [
            'target' => $target
        ]);

        return $translation['text'];
    }


}