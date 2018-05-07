<?php

use app\models\Channels;
use app\models\Twits;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\BreakGroups */
/* @var $searchModel \app\models\TwitsSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Marker list', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="break-groups-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label'=>'Channel',
                'value'=>$model->channel->name,
            ],
            [
                'attribute'=>'color',
                'format' => 'raw',
                'value' => function($model){
                    return '<div class="colorbox" style="background: '.$model->color.'"></div>';
                }
            ],
            [
                'label'=>'Words',
                'value'=>function($model){
                    return implode(', ', ArrayHelper::map($model->breaks, 'id', 'word'));
                },
            ],
            [
                'label'=>'Marked twits',
                'value'=>$dataProvider->totalCount
            ]
        ],
    ]) ?>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
//    echo '<pre>'.PHP_EOL;
//    var_dump($dataProvider, $searchModel);
//    echo '</pre>'.PHP_EOL;
//    die;
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            ['attribute' => 'url', 'format'=>'raw', 'value' => function($data){
//                echo '<pre>'.PHP_EOL;
//                var_dump($data);
//                echo '</pre>'.PHP_EOL;
//                die;
                return '<a href="https://twitter.com/'.$data->channel->name.'/status/'.$data->url.'">Link</a>';
            }],
            'retweet:boolean',
            'date',
            //            ['attribute'=>'txt', 'value' => 'twitTexts.txt'],
            //            ['attribute' => 'twitText.txt:html'],
            'twitText.txt:html',
            [
                'label'=>'Value',
                'format' => 'html',
                'value' => function(Twits $data){
                    return '<div class="values">'.$data->getCyrsField().'</div>';
                }
            ]
            //            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    <style>
        tr.hascheck{border-left: 4px solid #00b000;}
        .colorbox{
            width: 80px;
            height: 20px;
        }
    </style>
</div>
