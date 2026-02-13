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
            ['username', 'required', 'message' => 'El usuario es obligatorio.'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Este nombre de usuario ya está en uso.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required', 'message' => 'El correo electrónico es obligatorio.'],
            ['email', 'email', 'message' => 'Ingrese un correo electrónico válido.'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Este correo electrónico ya está registrado.'],
            ['email', 'validateNoPendingRequest'],

            ['password', 'required', 'message' => 'La contraseña es obligatoria.'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Valida que no exista una solicitud de autenticación pendiente o aprobada para este email
     * Si el usuario fue eliminado de la tabla user, permite re-registrarse limpiando auth_requests viejos
     */
    public function validateNoPendingRequest($attribute, $params)
    {
        // Si el usuario ya no existe en la tabla user, limpiar auth_requests viejos para permitir re-registro
        $userExists = \common\models\User::find()
            ->where(['email' => $this->email])
            ->exists();

        if (!$userExists) {
            // Eliminar auth_requests viejos ya que el usuario fue borrado
            \common\models\AuthRequest::deleteAll(['email' => $this->email]);
            return; // Permitir re-registro
        }

        $existingRequest = \common\models\AuthRequest::find()
            ->where(['email' => $this->email])
            ->andWhere(['in', 'status', [
                \common\models\AuthRequest::STATUS_PENDING,
                \common\models\AuthRequest::STATUS_APPROVED
            ]])
            ->one();

        if ($existingRequest) {
            if ($existingRequest->status == \common\models\AuthRequest::STATUS_PENDING) {
                $this->addError($attribute, 'Ya existe una solicitud pendiente para este correo. Espera la aprobación del administrador.');
            } else {
                $this->addError($attribute, 'Este correo ya tiene acceso autorizado al sistema.');
            }
        }
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
        // Verificar si ya existe una solicitud para este email (rechazada anteriormente)
        $existingRequest = \common\models\AuthRequest::findOne([
            'email' => $user->email,
            'status' => \common\models\AuthRequest::STATUS_REJECTED,
        ]);
        
        if ($existingRequest) {
            // Reutilizar la solicitud rechazada, ponerla como pendiente de nuevo
            $existingRequest->status = \common\models\AuthRequest::STATUS_PENDING;
            $existingRequest->nombre_completo = $user->username;
            $existingRequest->approved_by = null;
            $existingRequest->approved_at = null;
            $existingRequest->generateApprovalToken();
            
            if ($existingRequest->save()) {
                return $this->sendApprovalEmail($existingRequest);
            }
            return false;
        }

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
        ]);

        try {
            return Yii::$app->mailer->compose(
                ['html' => 'authApprovalRequest-html', 'text' => 'authApprovalRequest-text'],
                [
                    'authRequest' => $authRequest,
                    'approveUrl' => $approveUrl,
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
