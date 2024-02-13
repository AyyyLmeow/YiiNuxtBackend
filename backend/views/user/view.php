<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\web\UrlManager;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Set Profile Image', ['upload', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    $url = Url::to('/images/' . $model->userInfo->photo_url);
    
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            [   
                'attribute' => 'images',                   
                'format' => ['image',['width'=>'100','height'=>'100']],
                'value' => ($url),
            ],
            'id',
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email:email',
            'status',
            'created_at',
            'updated_at',
            'verification_token',
            [                      
                'label' => 'Surname',
                'value' => $model->userInfo->surname,
            ],
            [                      
                'label' => 'Name',
                'value' => $model->userInfo->name,
            ],
            [                      
                'label' => 'Patronymic',
                'value' => $model->userInfo->patronymic,
            ],
            [                      
                'label' => 'iin',
                'value' => $model->userInfo->iin,
            ],
            [                      
                'label' => 'Date of birth',
                'value' => $model->userInfo->birth_data,
            ],
        ],
    ]) ?>

</div>
