<?php

use app\models\Channels;
use kartik\color\ColorInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BreakGroups */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="break-groups-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'channel_id')->dropDownList(ArrayHelper::map(Channels::find()->asArray()->all(), 'id', 'name')) ?>


    <?= $form->field($model, 'color')->widget(ColorInput::classname(), [
    'options' => ['placeholder' => 'Select color ...'],
    ]);?>

    <?= $form->field($model, 'words_list')->textArea() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
