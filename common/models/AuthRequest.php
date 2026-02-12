<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Modelo para solicitudes de autorización de acceso
 *
 * @property int $id
 * @property string $email
 * @property string $nombre_completo
 * @property string $departamento
 * @property int $status
 * @property string $approval_token
 * @property string $magic_link_token
 * @property int $token_expiry
 * @property string $approved_by
 * @property int $approved_at
 * @property int $created_at
 * @property int $updated_at
 * @property int $last_login
 * @property int $login_count
 */
class AuthRequest extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%auth_request}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'nombre_completo'], 'required'],
            [['email'], 'email'],
            [['email', 'nombre_completo', 'departamento', 'approval_token', 'magic_link_token', 'approved_by'], 'string', 'max' => 255],
            [['status', 'token_expiry', 'approved_at', 'last_login', 'login_count'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Correo Electrónico',
            'nombre_completo' => 'Nombre Completo',
            'departamento' => 'Departamento',
            'status' => 'Estado',
            'approved_by' => 'Aprobado Por',
            'approved_at' => 'Aprobado El',
            'created_at' => 'Creado El',
            'updated_at' => 'Actualizado El',
            'last_login' => 'Último Acceso',
            'login_count' => 'Número de Accesos',
        ];
    }

    /**
     * Genera el token de aprobación
     */
    public function generateApprovalToken()
    {
        $this->approval_token = Yii::$app->security->generateRandomString(64);
    }

    /**
     * Genera el token de enlace mágico
     */
    public function generateMagicLinkToken($duration = 900) // 15 minutos por defecto
    {
        $this->magic_link_token = Yii::$app->security->generateRandomString(64);
        $this->token_expiry = time() + $duration;
    }

    /**
     * Verifica si el token de enlace mágico es válido
     */
    public function isMagicLinkValid()
    {
        return $this->magic_link_token !== null && 
               $this->token_expiry !== null && 
               $this->token_expiry >= time();
    }

    /**
     * Busca por token de aprobación
     */
    public static function findByApprovalToken($token)
    {
        return static::findOne(['approval_token' => $token]);
    }

    /**
     * Busca por token de enlace mágico
     */
    public static function findByMagicLinkToken($token)
    {
        return static::findOne([
            'magic_link_token' => $token,
            'status' => self::STATUS_APPROVED,
        ]);
    }

    /**
     * Busca por email
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Aprueba la solicitud
     */
    public function approve($approverEmail)
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $approverEmail;
        $this->approved_at = time();
        return $this->save();
    }

    /**
     * Rechaza la solicitud
     */
    public function reject($approverEmail)
    {
        $this->status = self::STATUS_REJECTED;
        $this->approved_by = $approverEmail;
        $this->approved_at = time();
        return $this->save();
    }

    /**
     * Registra un inicio de sesión
     */
    public function recordLogin()
    {
        $this->last_login = time();
        $this->login_count += 1;
        // Invalidar el token usado
        $this->magic_link_token = null;
        $this->token_expiry = null;
        return $this->save();
    }

    /**
     * Obtiene el nombre del estado
     */
    public function getStatusName()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'Pendiente';
            case self::STATUS_APPROVED:
                return 'Aprobado';
            case self::STATUS_REJECTED:
                return 'Rechazado';
            default:
                return 'Desconocido';
        }
    }
}
