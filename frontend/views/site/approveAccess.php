<?php

/** @var yii\web\View $this */
/** @var common\models\AuthRequest $authRequest */
/** @var string $token */

use yii\bootstrap5\Html;

$this->title = 'Revisar Solicitud de Acceso';

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
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <!-- Encabezado -->
                    <div class="text-center mb-4">
                        <?= Html::img('@web/imagenes/logo.png', [
                            'alt' => 'Logo Principal',
                            'class' => 'img-fluid rounded mb-2',
                            'style' => 'max-height: 80px; object-fit: contain;'
                        ]) ?>
                        <h4 class="fw-bold text-dark mb-1">
                            <i class="fas fa-user-check me-2"></i>Revisar Solicitud de Acceso
                        </h4>
                        <div class="border-top border-primary w-50 mx-auto mb-3" style="border-width: 2px !important;"></div>
                    </div>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle me-2"></i><?= Yii::$app->session->getFlash('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Información de la solicitud -->
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Datos del Solicitante</h5>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <strong><i class="fas fa-user me-1"></i> Nombre:</strong><br>
                                <span class="ms-3"><?= Html::encode($authRequest->nombre_completo) ?></span>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <strong><i class="fas fa-envelope me-1"></i> Email:</strong><br>
                                <span class="ms-3"><?= Html::encode($authRequest->email) ?></span>
                            </div>
                            <?php if ($authRequest->departamento): ?>
                            <div class="col-sm-6 mb-2">
                                <strong><i class="fas fa-building me-1"></i> Departamento:</strong><br>
                                <span class="ms-3"><?= Html::encode($authRequest->departamento) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-6 mb-2">
                                <strong><i class="fas fa-calendar me-1"></i> Fecha de solicitud:</strong><br>
                                <span class="ms-3"><?= Yii::$app->formatter->asDatetime($authRequest->created_at) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Advertencia -->
                    <div class="alert alert-warning small mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción no se puede deshacer. 
                        Al <strong>aprobar</strong>, el usuario podrá iniciar sesión en el sistema de inventario.
                        Al <strong>rechazar</strong>, el usuario será notificado y no podrá acceder.
                    </div>

                    <!-- Botones de acción con formularios POST -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <!-- Formulario de APROBAR -->
                        <form method="post" action="<?= \yii\helpers\Url::to(['site/approve-access', 'token' => $token]) ?>" 
                              onsubmit="return confirm('¿Está seguro de que desea APROBAR el acceso para <?= Html::encode($authRequest->nombre_completo) ?>?');">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success btn-lg px-4" style="border-radius: 10px;">
                                <i class="fas fa-check-circle me-2"></i>Aprobar Acceso
                            </button>
                        </form>

                        <!-- Formulario de RECHAZAR -->
                        <form method="post" action="<?= \yii\helpers\Url::to(['site/approve-access', 'token' => $token]) ?>"
                              onsubmit="return confirm('¿Está seguro de que desea RECHAZAR el acceso para <?= Html::encode($authRequest->nombre_completo) ?>?');">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger btn-lg px-4" style="border-radius: 10px;">
                                <i class="fas fa-times-circle me-2"></i>Rechazar Solicitud
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted small">
                            <i class="fas fa-shield-alt me-1"></i>
                            Solo el administrador autorizado puede tomar esta decisión.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Marca de agua del desarrollador -->
<div class="dev-watermark">Sistema codificado por Juan Ucan</div>
