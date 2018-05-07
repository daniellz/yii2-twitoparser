<?php

use app\models\Channels;
use kartik\color\ColorInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BreakGroupsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Marker list';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="break-groups-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Marker list', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            ['attribute' => 'channel.name', 'label' => 'Channel', 'filter' => Html::activeDropDownList($searchModel, 'channel_id', ArrayHelper::map(Channels::find()->asArray()->all(), 'id', 'name'),['class'=>'form-control','prompt' => 'All']),],
            [
                'attribute'=>'color',
                'format' => 'raw',
                'value' => function($data){
                    return '<div class="colorbox" style="background: '.$data->color.'"></div>';
                }
            ],
            [
                'attribute'=>'breaks.word',
                'label' => 'Words',
                'value'=>function($data){
                    return implode(', ', ArrayHelper::map($data->breaks, 'id', 'word'));
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    <style>
        .colorbox{
            width: 80px;
            height: 20px;
        }
    </style>
</div>
