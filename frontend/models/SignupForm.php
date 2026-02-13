<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->status = User::STATUS_INACTIVE; // Inactivo hasta que el admin apruebe

        if ($user->save()) {
            // Crear solicitud de autenticación y enviar email al admin
            $this->createAuthRequest($user);
            return $user;
        }

        return null;
    }

    /**
     * Crea una solicitud de autenticación y envía email de aprobación al admin
     * @param User $user el usuario recién registrado
     * @return bool
     */
    protected function createAuthRequest($user)
    {
        $authRequest = new \common\models\AuthRequest();
        $authRequest->email = $user->email;
        $authRequest->nombre_completo = $user->username;
        $authRequest->departamento = '';
        $authRequest->generateApprovalToken();

        if ($authRequest->save()) {
            return $this->sendApprovalEmail($authRequest);
        }

        return false;
    }

    /**
     * Envía el email de aprobación al administrador
     * @param \common\models\AuthRequest $authRequest
     * @return bool
     */
    protected function sendApprovalEmail($authRequest)
    {
        $adminEmail = Yii::$app->params['authRequestEmail'] ?? Yii::$app->params['adminEmail'];

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
            ->setSubject('Nueva Solicitud de Registro - ' . $authRequest->nombre_completo)
            ->send();
        } catch (\Exception $e) {
            Yii::error('Error al enviar email de aprobación de registro: ' . $e->getMessage());
            return false;
        }
    }
}
