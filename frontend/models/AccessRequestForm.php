<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\AuthRequest;

/**
 * Formulario para solicitar acceso al sistema
 */
class AccessRequestForm extends Model
{
    public $email;
    public $nombre_completo;
    public $departamento;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'nombre_completo'], 'required'],
            ['email', 'email'],
            ['email', 'validateEmailNotExists'],
            [['nombre_completo', 'departamento'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Correo Electrónico',
            'nombre_completo' => 'Nombre Completo',
            'departamento' => 'Departamento (Opcional)',
        ];
    }

    /**
     * Valida que el email no tenga una solicitud pendiente o aprobada
     */
    public function validateEmailNotExists($attribute, $params)
    {
        $existingRequest = AuthRequest::find()
            ->where(['email' => $this->email])
            ->andWhere(['in', 'status', [AuthRequest::STATUS_PENDING, AuthRequest::STATUS_APPROVED]])
            ->one();

        if ($existingRequest) {
            if ($existingRequest->status == AuthRequest::STATUS_PENDING) {
                $this->addError($attribute, 'Ya existe una solicitud pendiente para este correo. Por favor espere la aprobación.');
            } else {
                $this->addError($attribute, 'Este correo ya tiene acceso autorizado al sistema.');
            }
        }
    }

    /**
     * Crea la solicitud de acceso y envía el email al administrador
     */
    public function createRequest()
    {
        if (!$this->validate()) {
            return false;
        }

        $authRequest = new AuthRequest();
        $authRequest->email = $this->email;
        $authRequest->nombre_completo = $this->nombre_completo;
        $authRequest->departamento = $this->departamento;
        $authRequest->generateApprovalToken();

        if ($authRequest->save()) {
            return $this->sendApprovalEmail($authRequest);
        }

        return false;
    }

    /**
     * Envía el email de aprobación al administrador
     */
    protected function sendApprovalEmail($authRequest)
    {
        $adminEmail = 'inventarioapoyoinformatico@valladolid.tecnm.mx';
        
        $approveUrl = Yii::$app->urlManager->createAbsoluteUrl([
            'site/approve-access',
            'token' => $authRequest->approval_token,
            'action' => 'approve'
        ]);

        $rejectUrl = Yii::$app->urlManager->createAbsoluteUrl([
            'site/approve-access',
            'token' => $authRequest->approval_token,
            'action' => 'reject'
        ]);

        try {
            return Yii::$app->mailer->compose(
                ['html' => 'authApprovalRequest-html', 'text' => 'authApprovalRequest-text'],
                [
                    'authRequest' => $authRequest,
                    'approveUrl' => $approveUrl,
                    'rejectUrl' => $rejectUrl,
                ]
            )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($adminEmail)
            ->setSubject('Nueva Solicitud de Acceso al Sistema de Inventario - ' . $authRequest->nombre_completo)
            ->send();
        } catch (\Exception $e) {
            Yii::error('Error al enviar email de aprobación: ' . $e->getMessage());
            return false;
        }
    }
}
