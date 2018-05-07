<?php

use app\models\Channels;
use app\models\Twits;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TwitsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Twits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="twits-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            ['attribute' => 'url', 'format'=>'raw', 'headerOptions' => ['style' => 'min-width:70px'], 'value' => function(Twits $data){
                $translate = ($data->twitTranslates && $data->twitTranslates->txt)?'':'<button title="Translate twit" class="btn btn-default btn-xs translate-twit" data-id="'.$data->primaryKey.'"><span class="glyphicon glyphicon-globe" aria-hidden="true"></span></button>';
                return '<a href="https://twitter.com/'.$data->channel->name.'/status/'.$data->url.'">Link</a><br>'
                    .'<button title="Send twit to Telegram" class="btn btn-default btn-xs send-tg" data-id="'.$data->primaryKey.'"><span class="glyphicon glyphicon-send" aria-hidden="true"></span></button> '.$translate;
            }],
            ['attribute' => 'channel.name', 'label' => 'Channel', 'filter' => Html::activeDropDownList($searchModel, 'channel_id', ArrayHelper::map(Channels::find()->asArray()->all(), 'id', 'name'),['class'=>'form-control','prompt' => 'All']),],
            'retweet:boolean',
            'date',
//            ['attribute'=>'txt', 'value' => 'twitTexts.txt'],
//            ['attribute' => 'twitText.txt:html'],
            ['label' => 'Text', 'format' => 'html', 'value' => function(Twits $twit){
                return ($twit->twitTranslates && $twit->twitTranslates->txt)?$twit->twitTranslates->txt:$twit->twitText->txt;
            }],
            [
                'label'=>'Value',
                'format' => 'html',
                'value' => function(Twits $data){
                    return '<div class="values">'.$data->getCyrsField().'</div>';
                }
            ]
//            ['class' => 'yii\grid\ActionColumn'],
        ],
        'rowOptions' => function($model){
            $data = [];
            /** @var Twits $model */

            if($model->checked0)
            {
                $data['class']='hascheck';
                if($model->checked0->break->group)
                {
                    $data['style'] = 'background-color:'.$model->checked0->break->group->color;
                }
            }
            return $data;
        }
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<style>
    tr.hascheck{border-left: 4px solid #00b000;}
    tr .values .table{
        background: none;
    }
    tr .values .table-striped > tbody > tr:nth-of-type(2n+1){
        background-color: rgba(0,0,0,0.05);
    }
</style>
