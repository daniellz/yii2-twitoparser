<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BreakGroups */

$this->title = 'Update Break Groups: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Break Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="break-groups-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
