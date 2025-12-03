<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
$this->title = 'Detalle Fuente de Poder';
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= Html::a('Editar', ['update', 'id' => $model->idFuentePoder], ['class' => 'btn btn-primary']) ?>
<?= Html::a('Eliminar', ['delete', 'id' => $model->idFuentePoder], [
    'class' => 'btn btn-danger',
    'data' => [
        'confirm' => '¿Estás seguro de eliminar esta fuente de poder?',
        'method' => 'post',
    ],
]) ?></p>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'idFuentePoder',
        'MARCA',
        'MODELO',
        'TIPO',
        'VOLTAJE',
        'AMPERAJE',
        'POTENCIA_WATTS',
        'NUMERO_SERIE',
        'NUMERO_INVENTARIO',
        'DESCRIPCION',
        'ESTADO',
        'ubicacion_edificio',
        'ubicacion_detalle',
        'fecha_creacion',
        'fecha_ultima_edicion',
        'ultimo_editor',
    ],
]);
?>
