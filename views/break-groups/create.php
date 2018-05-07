<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\BreakGroups */

$this->title = 'Create Break Groups';
$this->params['breadcrumbs'][] = ['label' => 'Break Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="break-groups-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
