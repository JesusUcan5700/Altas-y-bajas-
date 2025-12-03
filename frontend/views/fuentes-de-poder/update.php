<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Editar Fuente de Poder';
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="fuentes-de-poder-update">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'MARCA')->textInput() ?>
    <?= $form->field($model, 'MODELO')->textInput() ?>
    <?= $form->field($model, 'TIPO')->dropDownList([
        'ATX' => 'ATX',
        'SFX' => 'SFX',
        'TFX' => 'TFX',
        'Flex ATX' => 'Flex ATX',
        'Redonda' => 'Redonda',
        'Laptop' => 'Laptop',
        'Servidor' => 'Servidor',
        'Otro' => 'Otro',
    ], ['prompt'=>'Seleccionar tipo']) ?>
    <?= $form->field($model, 'VOLTAJE')->textInput() ?>
    <?= $form->field($model, 'AMPERAJE')->textInput() ?>
    <?= $form->field($model, 'POTENCIA_WATTS')->textInput() ?>
    <?= $form->field($model, 'NUMERO_SERIE')->textInput() ?>
    <?= $form->field($model, 'NUMERO_INVENTARIO')->textInput() ?>
    <?= $form->field($model, 'DESCRIPCION')->textarea() ?>
    <?= $form->field($model, 'ESTADO')->dropDownList([
        'Activo' => 'Activo',
        'Inactivo(Sin Asignar)' => 'Inactivo(Sin Asignar)',
        'dañado(Proceso de baja)' => 'dañado(Proceso de baja)',
        'En Mantenimiento' => 'En Mantenimiento',
        'BAJA' => 'BAJA',
    ], ['prompt'=>'Seleccionar estado']) ?>
    <?= $form->field($model, 'ubicacion_edificio')->dropDownList(array_combine(range('A','Z'), range('A','Z')), ['prompt'=>'Seleccionar edificio']) ?>
    <?= $form->field($model, 'ubicacion_detalle')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton('Guardar Cambios', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
