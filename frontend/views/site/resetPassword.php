<?php
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Nueva Contraseña';

// Registrar Font Awesome
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', [
    'position' => \yii\web\View::POS_HEAD
]);

// Estilos para el botón de mostrar/ocultar contraseña
$this->registerCss("
    .password-wrapper {
        position: relative;
    }
    .password-wrapper .form-control {
        padding-right: 45px !important;
    }
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 38px;
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
        background: transparent;
        border: none;
        padding: 8px;
        font-size: 18px;
        line-height: 1;
    }
    .password-toggle:hover {
        color: #495057;
    }
    .password-toggle:focus {
        outline: none;
    }
");

// Script para mostrar/ocultar contraseña
$this->registerJs("
    $('.password-toggle').on('click', function(e) {
        e.preventDefault();
        var wrapper = $(this).closest('.password-wrapper');
        var input = wrapper.find('input[type=\"password\"], input[type=\"text\"]');
        var icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
", \yii\web\View::POS_READY);
?>
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h4 class="fw-bold">Crear Nueva Contraseña</h4>
                        <p class="text-muted">Elige una contraseña segura (mínimo 8 caracteres)</p>
                    </div>

                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                        <div class="password-wrapper">
                            <?= $form->field($model, 'password')->passwordInput([
                                'autofocus' => true,
                                'placeholder' => 'Nueva contraseña',
                                'class' => 'form-control',
                                'id' => 'password-input'
                            ])->label('Nueva Contraseña') ?>
                            <button type="button" class="password-toggle" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <div class="d-grid gap-2">
                            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Guardar Contraseña', [
                                'class' => 'btn btn-primary',
                                'style' => 'border-radius: 8px;'
                            ]) ?>
                        </div>
                    <?php ActiveForm::end(); ?>

                    <div class="text-center mt-3">
                        <p class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Después de guardar, podrás iniciar sesión con tu nueva contraseña
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
