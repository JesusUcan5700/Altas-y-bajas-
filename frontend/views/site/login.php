<?php

/** @var yii\web                                   <!-- Header reorganizado correctamente -->
                    <div class="text-center mb-3">
                        <!-- Título del sistema PRIMERO -->
                        <p class="text-muted small mb-2">Sistema de inventario de altas y bajas de equipo de cómputo</p>
                        
                        <!-- Logo DESPUÉS del título del sistema -->
                        <?= Html::img('@web/imagenes/logo.jpg', [
                            'alt' => 'Logo Principal',
                            'class' => 'img-fluid rounded mb-2',
                            'style' => 'max-height: 80px; object-fit: contain;'
                        ]) ?>
                        
                        <!-- Acción específica AL FINAL -->
                        <h4 class="fw-bold text-dark mb-1">Iniciar Sesión</h4>
                        <div class="border-top border-primary w-50 mx-auto mb-3" style="border-width: 2px !important;"></div>
                    </div>eader reorganizado -->
                    <div class="text-center mb-3">
                        <!-- Título del sistema primero -->
                        <h6 class="fw-bold text-primary mb-2">Sistema de inventario de altas y bajas de equipo de cómputo</h6>
                        
                        <!-- Logo después del título -->
                        <?= Html::img('@web/../views/site/logo.jpg', [
                            'alt' => 'Logo Principal',
                            'class' => 'img-fluid rounded mb-2',
                            'style' => 'max-height: 80px; object-fit: contain;'
                        ]) ?>
                        
                        <!-- Acción específica al final -->
                        <h4 class="fw-bold text-dark mb-1">Iniciar Sesión</h4>
                        <div class="border-top border-primary w-50 mx-auto mb-3" style="border-width: 2px !important;"></div>
                    </div> */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Iniciar Sesión';

// Registrar Font Awesome
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', [
    'position' => \yii\web\View::POS_HEAD
]);

// Registrar CSS para marca de agua del desarrollador
$this->registerCss("
    .dev-watermark {
        position: fixed !important;
        bottom: 25px !important;
        right: 20px !important;
        font-size: 13px !important;
        color: rgba(0, 0, 0, 0.7) !important;
        z-index: 9999 !important;
        pointer-events: none !important;
        user-select: none !important;
        font-family: 'Courier New', monospace !important;
        text-shadow: 1px 1px 3px rgba(255,255,255,0.9) !important;
        transform: rotate(-3deg) !important;
        background: linear-gradient(45deg, rgba(255,255,255,0.2), rgba(0,0,0,0.1)) !important;
        padding: 4px 8px !important;
        border-radius: 5px !important;
        opacity: 0.8 !important;
        font-weight: 600 !important;
        letter-spacing: 0.8px !important;
        border: 1px solid rgba(0,0,0,0.1) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
    }
    .dev-watermark::before { content: '💻 ' !important; }
    .dev-watermark::after { content: ' 👨‍💻' !important; }
    
    /* Estilos para el botón de mostrar/ocultar contraseña */
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

// Registrar script de protección
$this->registerJsFile('@web/js/watermark-protection.js', ['position' => \yii\web\View::POS_END]);

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
<div class="container-fluid d-flex align-items-center justify-content-center position-relative" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 0;">
    <!-- Logo de fondo -->
    <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="opacity: 0.08; z-index: 1;">
    </div>
    
    <div class="row w-100 justify-content-center" style="z-index: 2; position: relative;">
        <div class="col-md-6 col-lg-4 col-xl-4">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <!-- Logo principal arriba del formulario -->
                    <div class="text-center mb-3">
                        <?= Html::img('@web/../views/site/logo.jpg', [
                            'alt' => 'Logo Principal',
                            'class' => 'img-fluid rounded mb-2',
                            'style' => 'max-height: 80px; object-fit: contain;'
                        ]) ?>
                        <h5 class="fw-bold text-dark mb-1">Sistema de inventario de altas y bajas de equipo de cómputo</h5>
                        <h3 class="text-muted small">Iniciar Sesión</h3>
                        <div class="border-top border-primary w-50 mx-auto mb-3" style="border-width: 2px !important;"></div>
                    </div>

                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('warning')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= Yii::$app->session->getFlash('warning') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle me-2"></i><?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        'fieldConfig' => [
                            'template' => '<div class="mb-3">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'form-label fw-bold text-dark small'],
                            'inputOptions' => ['class' => 'form-control'],
                            'errorOptions' => ['class' => 'text-danger small'],
                        ]
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'Ingrese su usuario',
                        'class' => 'form-control'
                    ])->label('Usuario') ?>

                    <div class="password-wrapper">
                        <?= $form->field($model, 'password')->passwordInput([
                            'placeholder' => 'Ingrese su contraseña',
                            'class' => 'form-control',
                            'id' => 'password-input'
                        ])->label('Contraseña') ?>
                        <button type="button" class="password-toggle" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => '<div class="form-check mb-3">{input} {label}</div>',
                        'class' => 'form-check-input',
                        'labelOptions' => ['class' => 'form-check-label text-muted small'],
                    ])->label('Recordarme') ?>

                    <div class="d-grid gap-2 mt-3">
                        <?= Html::submitButton('<i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión', [
                            'class' => 'btn btn-primary',
                            'style' => 'border-radius: 8px;'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="text-center mt-3">
                        <p class="text-muted small mb-2">
                            <?= Html::a('¿Olvidaste tu contraseña?', ['site/request-password-reset'], [
                                'class' => 'text-decoration-none'
                            ]) ?>
                        </p>
                        <p class="text-muted small mb-2">¿No tienes cuenta?</p>
                        <?= Html::a('<i class="fas fa-user-plus me-2"></i>Registrarse', ['site/signup'], [
                            'class' => 'btn btn-outline-secondary btn-sm',
                            'style' => 'border-radius: 8px;'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Marca de agua del desarrollador -->
<div class="dev-watermark">Sistema codificado por Juan Ucan</div>
