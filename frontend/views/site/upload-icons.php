<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Subir iconos';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $msg): ?>
    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?>"><?= $msg ?></div>
<?php endforeach; ?>

<!-- Previsualización de iconos actuales -->
<div class="icon-preview" style="display:flex;gap:20px;margin-bottom:20px;">
    <?php
    $labels = [
        'total' => 'Total',
        'uso' => 'En Uso',
        'disponibles' => 'Disponibles',
        'mantenimiento' => 'Mantenimiento',
        'danados' => 'Dañados',
        'pct' => 'Disponibilidad',
    ];
    foreach ($labels as $key => $label):
        $src = (isset($icons[$key]) && $icons[$key]) ? $icons[$key] : null;
    ?>
        <div style="text-align:center;width:110px;">
            <?php if ($src): ?>
                <?= Html::img($src, ['style' => 'width:64px;height:64px;border-radius:8px;border:1px solid #eee;']) ?>
            <?php else: ?>
                <div style="width:64px;height:64px;border-radius:8px;border:1px dashed #ccc;display:flex;align-items:center;justify-content:center;color:#999;">No</div>
            <?php endif; ?>
            <div style="margin-top:6px;font-size:12px;color:#555;"><?= Html::encode($label) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<p>Sube los archivos (png/jpg). Si no quieres reemplazar alguno, déjalo vacío.</p>

<?= $form->field($model, 'total')->fileInput()->label('Total (total.png)') ?>
<?= $form->field($model, 'uso')->fileInput()->label('En Uso (en_uso.png)') ?>
<?= $form->field($model, 'disponibles')->fileInput()->label('Disponibles (disponibles.png)') ?>
<?= $form->field($model, 'mantenimiento')->fileInput()->label('Mantenimiento (mantenimiento.png)') ?>
<?= $form->field($model, 'danados')->fileInput()->label('Dañados (danados.png)') ?>
<?= $form->field($model, 'pct')->fileInput()->label('Disponibilidad (porcentaje.png)') ?>

<div class="form-group">
    <?= Html::submitButton('Subir iconos', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>