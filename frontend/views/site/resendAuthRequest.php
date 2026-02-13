<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = 'Reenviar Solicitud de Aprobación';

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', [
    'position' => \yii\web\View::POS_HEAD
]);

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
");
?>
<div class="container-fluid d-flex align-items-center justify-content-center position-relative" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 0;">
    <div class="row w-100 justify-content-center" style="z-index: 2; position: relative;">
        <div class="col-md-6 col-lg-4 col-xl-4">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <!-- Logo -->
                    <div class="text-center mb-3">
                        <?= Html::img('@web/imagenes/logo.png', [
                            'alt' => 'Logo Principal',
                            'class' => 'img-fluid rounded mb-2',
                            'style' => 'max-height: 80px; object-fit: contain;'
                        ]) ?>
                        <h5 class="fw-bold text-dark mb-1">Reenviar Solicitud</h5>
                        <p class="text-muted small">Ingresa tu correo electrónico para reenviar la solicitud de aprobación al administrador.</p>
                        <div class="border-top border-warning w-50 mx-auto mb-3" style="border-width: 2px !important;"></div>
                    </div>

                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= Yii::$app->session->getFlash('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle me-2"></i><?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i><?= Yii::$app->session->getFlash('info') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>¿Ya te registraste pero no has recibido aprobación?</strong><br>
                        Ingresa el correo con el que te registraste y reenviaremos la solicitud de aprobación al administrador.
                    </div>

                    <form method="post" action="<?= \yii\helpers\Url::to(['site/resend-auth-request']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold text-dark small">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="tucorreo@valladolid.tecnm.mx"
                                       required
                                       autofocus>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-warning text-dark" style="border-radius: 8px;">
                                <i class="fas fa-paper-plane me-2"></i>Reenviar Solicitud
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <hr class="my-3">
                        <p class="text-muted small mb-2">
                            <?= Html::a('<i class="fas fa-arrow-left me-1"></i>Volver al inicio de sesión', ['site/login'], [
                                'class' => 'text-decoration-none'
                            ]) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Marca de agua del desarrollador -->
<div class="dev-watermark">Sistema codificado por Juan Ucan</div>
