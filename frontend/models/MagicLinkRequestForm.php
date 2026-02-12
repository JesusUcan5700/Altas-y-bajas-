<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\AuthRequest;

/**
 * Formulario para solicitar un enlace mágico de acceso
 */
class MagicLinkRequestForm extends Model
{
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'validateApprovedEmail'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Correo Electrónico',
        ];
    }

    /**
     * Valida que el email esté aprobado
     */
    public function validateApprovedEmail($attribute, $params)
    {
        // Buscar si existe alguna solicitud con este email
        $authRequest = AuthRequest::findOne(['email' => $this->email]);

        if (!$authRequest) {
            // No hay ninguna solicitud
            $this->addError($attribute, 'Este correo no está registrado. Por favor solicite acceso primero haciendo clic en "Solicitar autorización de acceso" más abajo.');
            return;
        }

        // Verificar el estado de la solicitud
        switch ($authRequest->status) {
            case AuthRequest::STATUS_PENDING:
                $diasEspera = floor((time() - $authRequest->created_at) / 86400);
                $this->addError($attribute, 
                    "Tu solicitud está PENDIENTE de aprobación (esperando $diasEspera día(s)). " .
                    "El administrador debe aprobarla antes de que puedas acceder. " .
                    "Si es urgente, contacta a inventarioapoyoinformatico@valladolid.tecnm.mx"
                );
                break;
            
            case AuthRequest::STATUS_REJECTED:
                $this->addError($attribute, 
                    'Tu solicitud fue RECHAZADA. ' .
                    'Para más información contacta a inventarioapoyoinformatico@valladolid.tecnm.mx'
                );
                break;
            
            case AuthRequest::STATUS_APPROVED:
                // Todo bien, está aprobado
                break;
            
            default:
                $this->addError($attribute, 'Estado de solicitud desconocido. Contacta al administrador.');
        }
    }

    /**
     * Envía el enlace mágico al email
     */
    public function sendMagicLink()
    {
        if (!$this->validate()) {
            return false;
        }

        $authRequest = AuthRequest::findByEmail($this->email);
        
        // Generar nuevo token de enlace mágico (válido por 15 minutos)
        $authRequest->generateMagicLinkToken(900);
        
        if ($authRequest->save()) {
            return $this->sendMagicLinkEmail($authRequest);
        }

        return false;
    }

    /**
     * Envía el email con el enlace mágico
     */
    protected function sendMagicLinkEmail($authRequest)
    {
        $loginUrl = Yii::$app->urlManager->createAbsoluteUrl([
            'site/magic-login',
            'token' => $authRequest->magic_link_token,
        ]);

        try {
            return Yii::$app->mailer->compose(
                ['html' => 'magicLink-html', 'text' => 'magicLink-text'],
                [
                    'authRequest' => $authRequest,
                    'loginUrl' => $loginUrl,
                ]
            )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($authRequest->email)
            ->setSubject('Enlace de Acceso al Sistema de Inventario')
            ->send();
        } catch (\Exception $e) {
            Yii::error('Error al enviar enlace mágico: ' . $e->getMessage());
            return false;
        }
    }
}
