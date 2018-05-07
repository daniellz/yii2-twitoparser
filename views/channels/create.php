<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Channels */

$this->title = 'Create Channels';
$this->params['breadcrumbs'][] = ['label' => 'Channels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="channels-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
