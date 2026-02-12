<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MagicLinkRequestForm */

$this->title = 'Acceso al Sistema';

$this->registerCss("
    .auth-login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }
    
    .auth-login-box {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        padding: 40px;
        max-width: 450px;
        width: 100%;
    }
    
    .auth-login-box h1 {
        color: #667eea;
        margin-bottom: 10px;
        font-size: 28px;
        text-align: center;
    }
    
    .auth-login-box .subtitle {
        color: #666;
        margin-bottom: 30px;
        text-align: center;
        font-size: 14px;
    }
    
    .icon-header {
        text-align: center;
        font-size: 70px;
        margin-bottom: 20px;
    }
    
    .btn-magic-link {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        color: white;
        transition: transform 0.2s;
    }
    
    .btn-magic-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    
    .divider {
        text-align: center;
        margin: 30px 0;
        position: relative;
    }
    
    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #ddd;
    }
    
    .divider span {
        background: white;
        padding: 0 15px;
        position: relative;
        color: #999;
        font-size: 14px;
    }
    
    .request-access-link {
        text-align: center;
        margin-top: 20px;
    }
    
    .request-access-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    
    .request-access-link a:hover {
        text-decoration: underline;
    }
    
    .info-alert {
        background-color: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-size: 13px;
    }
");
?>

<div class="auth-login-page">
    <div class="auth-login-box">
        <div class="icon-header">🔑</div>
        
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="subtitle">Ingrese su correo para recibir un enlace de acceso</p>
        
        <div class="info-alert">
            <strong>🔒 Autenticación Segura:</strong><br>
            Te enviaremos un enlace único a tu correo que será válido por 15 minutos.
            <br><br>
            <strong>⚠️ Importante:</strong> Tu correo debe estar <strong>APROBADO</strong> por el administrador para recibir el enlace.
        </div>

        <?php $form = ActiveForm::begin(['id' => 'auth-login-form']); ?>

            <?= $form->field($model, 'email')->textInput([
                'autofocus' => true,
                'placeholder' => 'tucorreo@gmail.com o usuario@valladolid.tecnm.mx',
                'type' => 'email'
            ])->label('📧 Correo Electrónico') ?>

            <div class="form-group">
                <?= Html::submitButton('✉️ Enviar Enlace de Acceso', [
                    'class' => 'btn btn-magic-link',
                    'name' => 'magic-link-button'
                ]) ?>
            </div>

        <?php ActiveForm::end(); ?>
        
        <div class="divider">
            <span>o</span>
        </div>
        
        <div class="request-access-link">
            <strong>¿Primera vez?</strong> <?= Html::a('Solicitar autorización de acceso', ['site/request-access'], ['style' => 'font-size: 15px;']) ?>
            <br><br>
            <small style="color: #999;">
                📝 Si nunca has solicitado acceso, primero debes ser autorizado por el administrador.
            </small>
        </div>
    </div>
</div>
