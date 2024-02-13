<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php /*if(isset($model->userInfo)){
        echo $form->field($model->userInfo, 'photo_url')->fileInput();
     }  else {
        echo $form->field($userInfo, 'photo_url')->fileInput();
     }  */?>

    <?= $form->field($model, 'username')->textInput() ?>

    <?= $form->field($model, 'auth_key')->textInput() ?>

    <?= $form->field($model, 'password_hash')->textInput() ?>

    <?= $form->field($model, 'password_reset_token')->textInput() ?>

    <?= $form->field($model, 'email') ?>

    <?php if(isset($model->userInfo)){
        echo $form->field($model->userInfo, 'surname')->textInput();
     }  else {
        echo $form->field($userInfo, 'surname')->textInput();
     } ?>

    <?php if(isset($model->userInfo)){
        echo $form->field($model->userInfo, 'name')->textInput();
     }  else {
        echo $form->field($userInfo, 'name')->textInput();
     }  ?>

    <?php if(isset($model->userInfo)){
        echo $form->field($model->userInfo, 'patronymic')->textInput();
     }  else {
        echo $form->field($userInfo, 'patronymic')->textInput();
     }  ?>

    <?php if(isset($model->userInfo)){
        echo $form->field($model->userInfo, 'iin')->textInput();
     }  else {
        echo $form->field($userInfo, 'iin')->textInput();
     }  ?>

    <?php if(isset($model->userInfo)){
        echo $form->field($model->userInfo, 'birth_data')->textInput();
     }  else {
        echo $form->field($userInfo, 'birth_data')->textInput();
     }  ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
