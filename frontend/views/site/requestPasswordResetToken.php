<?php
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Recuperar Contraseña';
?>
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h4 class="fw-bold">Recuperar Contraseña</h4>
                        <p class="text-muted">Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña</p>
                    </div>

                    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                        <?= $form->field($model, 'email')->textInput([
                            'autofocus' => true,
                            'placeholder' => 'tu-email@ejemplo.com',
                            'class' => 'form-control'
                        ])->label('Correo Electrónico') ?>

                        <div class="d-grid gap-2">
                            <?= Html::submitButton('<i class="fas fa-paper-plane me-2"></i>Enviar', [
                                'class' => 'btn btn-primary',
                                'style' => 'border-radius: 8px;'
                            ]) ?>
                        </div>
                    <?php ActiveForm::end(); ?>

                    <div class="text-center mt-3">
                        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Volver al login', ['site/login'], [
                            'class' => 'btn btn-outline-secondary btn-sm',
                            'style' => 'border-radius: 8px;'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
