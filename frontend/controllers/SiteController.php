<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use frontend\models\Nobreak;
use frontend\models\Equipo;
use frontend\models\Impresora;
use frontend\models\VideoVigilancia;
use frontend\models\Conectividad;
use frontend\models\Telefonia;
use frontend\models\Procesador;
use frontend\models\Almacenamiento;
use frontend\models\Ram;
use frontend\models\Sonido;
use frontend\models\Monitor;
use frontend\models\FuentesDePoder;
use frontend\models\Microfono;
use frontend\models\Bateria;
use frontend\models\Adaptador;
use frontend\models\PiezaReciclaje;
use frontend\models\HistorialPiezaReciclaje;
use Yii;
use Exception;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\AuthRequest;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\AccessRequestForm;
use frontend\models\MagicLinkRequestForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup', 'login', 'request-password-reset', 'reset-password', 'auth-login', 'magic-login', 'request-access', 'approve-access'],
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'request-password-reset', 'reset-password', 'auth-login', 'magic-login', 'request-access', 'approve-access'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'equipo-eliminar' => ['post'],
                    'equipo-eliminar-multiple' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        // Verificar autenticación personalizada para usuarios autorizados por email
        if (!in_array($action->id, ['login', 'auth-login', 'magic-login', 'request-access', 'approve-access', 'error', 'captcha'])) {
            // Verificar si el usuario está autenticado via sistema tradicional o via auth_request
            $isAuthenticatedViaSession = Yii::$app->session->get('authenticated', false);
            $isAuthenticatedViaUser = !Yii::$app->user->isGuest;
            
            if (!$isAuthenticatedViaSession && !$isAuthenticatedViaUser) {
                Yii::$app->session->setFlash('warning', 'Debe iniciar sesión para acceder al sistema.');
                return $this->redirect(['site/auth-login']);
            }
        }
        
        // Deshabilitar CSRF para acciones de eliminación y reciclaje
        if (in_array($action->id, ['equipo-eliminar', 'equipo-eliminar-multiple', 'ram-eliminar', 'ram-eliminar-multiple', 'eliminar-ram', 'eliminar-ram-masivo', 'procesador-eliminar', 'procesador-eliminar-multiple', 'eliminar-procesador', 'eliminar-procesadores-masivo', 'almacenamiento-eliminar', 'almacenamiento-eliminar-multiple', 'eliminar-almacenamiento', 'eliminar-almacenamiento-masivo', 'fuente-eliminar', 'fuente-eliminar-multiple', 'monitor-eliminar', 'monitor-eliminar-multiple', 'eliminar-monitor', 'eliminar-monitores-masivo', 'registrar-pieza-reciclaje', 'actualizar-pieza-reciclaje', 'eliminar-pieza-reciclaje', 'inventario-piezas-reciclaje', 'detalle-pieza-reciclaje', 'estadisticas-reciclaje', 'opciones-pieza-reciclaje', 'catalogo-piezas-existentes', 'obtener-dispositivos-baja', 'detalle-dispositivo-baja'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // Verificar autenticación (ya verificado en beforeAction, pero por seguridad)
        $isAuthenticatedViaSession = Yii::$app->session->get('authenticated', false);
        $isAuthenticatedViaUser = !Yii::$app->user->isGuest;
        
        if (!$isAuthenticatedViaSession && !$isAuthenticatedViaUser) {
            Yii::$app->session->setFlash('warning', 'Debe iniciar sesión para acceder al sistema.');
            return $this->redirect(['site/auth-login']);
        }
        
        return $this->render('index');
    }

    /**
     * Logout action.
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        // Cerrar sesión tradicional de Yii2
        Yii::$app->user->logout();
        
        // Limpiar sesión de autenticación personalizada
        Yii::$app->session->remove('auth_user_id');
        Yii::$app->session->remove('auth_user_email');
        Yii::$app->session->remove('auth_user_name');
        Yii::$app->session->remove('authenticated');
        
        Yii::$app->session->setFlash('success', '👋 Has cerrado sesión correctamente.');

        return $this->redirect(['auth-login']);
    }

    /**
     * Login action.
     *
     * @return \yii\web\Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new \common\models\LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Signs user up.
     *
     * @return \yii\web\Response|string
     */
    public function actionSignup()
    {
        $model = new \frontend\models\SignupForm();
        if ($model->load(Yii::$app->request->post()) && $user = $model->signup()) {
            if (Yii::$app->getUser()->login($user)) {
                Yii::$app->session->setFlash('success', 'Usuario registrado exitosamente con status 10.');
                return $this->goHome();
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Revisa tu email para instrucciones de recuperación de contraseña.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Lo sentimos, no pudimos enviar el email de recuperación.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Token de recuperación inválido o expirado.');
            return $this->goHome();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Nueva contraseña guardada exitosamente.');
            return $this->redirect(['login']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Solicitud de acceso al sistema
     * Los usuarios envían sus datos para ser autorizados
     *
     * @return mixed
     */
    public function actionRequestAccess()
    {
        $model = new AccessRequestForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->createRequest()) {
                Yii::$app->session->setFlash('success', 
                    '✅ Solicitud enviada correctamente. Se ha notificado al administrador. ' .
                    'Recibirás un correo cuando tu acceso sea aprobado.'
                );
                return $this->redirect(['auth-login']);
            } else {
                Yii::$app->session->setFlash('error', 
                    '❌ No se pudo procesar la solicitud. Por favor verifica los datos o intenta más tarde.'
                );
            }
        }

        return $this->render('request-access', [
            'model' => $model,
        ]);
    }

    /**
     * Aprobación/Rechazo de solicitud de acceso
     * Solo accesible desde el enlace enviado al administrador
     *
     * @param string $token
     * @param string $action
     * @return mixed
     */
    public function actionApproveAccess($token, $action)
    {
        $authRequest = AuthRequest::findByApprovalToken($token);

        if (!$authRequest) {
            Yii::$app->session->setFlash('error', '❌ Token de aprobación inválido o expirado.');
            return $this->goHome();
        }

        if ($authRequest->status != AuthRequest::STATUS_PENDING) {
            Yii::$app->session->setFlash('warning', 
                '⚠️ Esta solicitud ya fue procesada anteriormente.'
            );
            return $this->goHome();
        }

        $adminEmail = 'inventarioapoyoinformatico@valladolid.tecnm.mx';

        if ($action === 'approve') {
            if ($authRequest->approve($adminEmail)) {
                // Enviar notificación al usuario
                $this->sendApprovalNotification($authRequest);
                
                Yii::$app->session->setFlash('success', 
                    '✅ Acceso aprobado para ' . $authRequest->nombre_completo . 
                    '. Se ha enviado una notificación al usuario.'
                );
            } else {
                Yii::$app->session->setFlash('error', '❌ Error al aprobar la solicitud.');
            }
        } elseif ($action === 'reject') {
            if ($authRequest->reject($adminEmail)) {
                // Enviar notificación de rechazo al usuario
                $this->sendRejectionNotification($authRequest);
                
                Yii::$app->session->setFlash('info', 
                    'ℹ️ Solicitud rechazada para ' . $authRequest->nombre_completo . 
                    '. Se ha notificado al usuario.'
                );
            } else {
                Yii::$app->session->setFlash('error', '❌ Error al rechazar la solicitud.');
            }
        }

        return $this->redirect(['auth-login']);
    }

    /**
     * Formulario de autenticación por enlace mágico
     * Los usuarios autorizados pueden solicitar un enlace de acceso
     *
     * @return mixed
     */
    public function actionAuthLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new MagicLinkRequestForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->sendMagicLink()) {
                Yii::$app->session->setFlash('success', 
                    '📧 Enlace de acceso enviado. Revisa tu correo electrónico. ' .
                    'El enlace será válido por 15 minutos.'
                );
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 
                    '❌ No se pudo enviar el enlace. Verifica que tu email esté autorizado.'
                );
            }
        }

        return $this->render('auth-login', [
            'model' => $model,
        ]);
    }

    /**
     * Autenticación mediante enlace mágico
     * El usuario hace clic en el enlace recibido por correo
     *
     * @param string $token
     * @return mixed
     */
    public function actionMagicLogin($token)
    {
        $authRequest = AuthRequest::findByMagicLinkToken($token);

        if (!$authRequest) {
            Yii::$app->session->setFlash('error', 
                '❌ El enlace de acceso es inválido o ha expirado. Por favor solicita uno nuevo.'
            );
            return $this->redirect(['auth-login']);
        }

        if (!$authRequest->isMagicLinkValid()) {
            Yii::$app->session->setFlash('error', 
                '⏰ El enlace de acceso ha expirado. Por favor solicita uno nuevo.'
            );
            return $this->redirect(['auth-login']);
        }

        // Crear sesión temporal sin usuario en la tabla user
        // Usamos un identificador basado en el email del authRequest
        $identity = new \yii\web\User([
            'identityClass' => 'common\models\User',
        ]);

        // Login usando el ID del auth_request como identificador temporal
        // Nota: Esto requiere modificar el modelo User o crear un IdentityInterface personalizado
        // Por simplicidad, vamos a usar sesión directa
        
        Yii::$app->session->set('auth_user_id', $authRequest->id);
        Yii::$app->session->set('auth_user_email', $authRequest->email);
        Yii::$app->session->set('auth_user_name', $authRequest->nombre_completo);
        Yii::$app->session->set('authenticated', true);

        // Registrar el login
        $authRequest->recordLogin();

        Yii::$app->session->setFlash('success', 
            '✅ Bienvenido ' . $authRequest->nombre_completo . '. Has iniciado sesión correctamente.'
        );

        return $this->redirect(['index']);
    }

    /**
     * Envía notificación de aprobación al usuario
     */
    protected function sendApprovalNotification($authRequest)
    {
        try {
            return Yii::$app->mailer->compose(
                ['html' => 'authApproved-html', 'text' => 'authApproved-text'],
                ['authRequest' => $authRequest]
            )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($authRequest->email)
            ->setSubject('✅ Acceso Aprobado - Sistema de Inventario')
            ->send();
        } catch (\Exception $e) {
            Yii::error('Error al enviar notificación de aprobación: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía notificación de rechazo al usuario
     */
    protected function sendRejectionNotification($authRequest)
    {
        try {
            return Yii::$app->mailer->compose(
                ['html' => 'authRejected-html', 'text' => 'authRejected-text'],
                ['authRequest' => $authRequest]
            )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setTo($authRequest->email)
            ->setSubject('Solicitud de Acceso - Sistema de Inventario')
            ->send();
        } catch (\Exception $e) {
            Yii::error('Error al enviar notificación de rechazo: ' . $e->getMessage());
            return false;
        }
    }


    public function actionEditar()
    {
        // Si es una petición AJAX para cargar equipos
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            $action = Yii::$app->request->post('action');
            
            if ($action === 'cargar_equipos') {
                try {
                    // Verificar conexión a la base de datos
                    $connection = Yii::$app->db;
                    
                    if (!$connection) {
                        throw new Exception('No se pudo establecer conexión con la base de datos');
                    }
                    
                    // Verificar si la tabla existe
                    $tableExists = $connection->createCommand("SHOW TABLES LIKE 'nobreak'")->queryScalar();
                    if (!$tableExists) {
                        throw new Exception('La tabla "nobreak" no existe en la base de datos');
                    }
                    
                    // Verificar las columnas de la tabla
                    $tableInfo = $connection->createCommand("SHOW COLUMNS FROM nobreak")->queryAll();
                    if (empty($tableInfo)) {
                        throw new Exception('No se pudieron obtener las columnas de la tabla "nobreak"');
                    }
                    
                    // Log de información para debugging
                    Yii::info('Cargando equipos. Columnas disponibles: ' . implode(', ', array_column($tableInfo, 'Field')), __METHOD__);
                    
                    // Consulta SQL mejorada para obtener toda la información
                    $sql = "
                        SELECT 
                            idNOBREAK,
                            MARCA,
                            MODELO,
                            CAPACIDAD,
                            NUMERO_SERIE,
                            NUMERO_INVENTARIO,
                            DESCRIPCION,
                            Estado,
                            fecha,
                            ubicacion_edificio,
                            ubicacion_detalle,
                            -- Crear ubicación completa concatenando campos
                            CONCAT(
                                COALESCE(ubicacion_edificio, ''),
                                CASE 
                                    WHEN ubicacion_edificio IS NOT NULL AND ubicacion_detalle IS NOT NULL 
                                    THEN ' - ' 
                                    ELSE '' 
                                END,
                                COALESCE(ubicacion_detalle, '')
                            ) as ubicacion_completa,
                            -- Obtener fecha de creación/modificación si existe
                            COALESCE(fecha, CURDATE()) as fecha_registro
                        FROM nobreak 
                        ORDER BY idNOBREAK ASC
                    ";
                    
                    $command = $connection->createCommand($sql);
                    $equipos = $command->queryAll();
                    
                    // Si no hay equipos, crear datos de ejemplo más completos
                    if (empty($equipos)) {
                        return [
                            [
                                'id' => 1,
                                'marca' => 'APC',
                                'modelo' => 'BR1500G',
                                'numero_serie' => 'TEST123456',
                                'estado' => 'Activo',
                                'ubicacion' => 'Edificio A - Sala de Servidores',
                                'data' => [
                                    'idNOBREAK' => 1,
                                    'MARCA' => 'APC',
                                    'MODELO' => 'BR1500G',
                                    'CAPACIDAD' => '1500VA/900W',
                                    'NUMERO_SERIE' => 'TEST123456',
                                    'NUMERO_INVENTARIO' => 'INV-2024-001',
                                    'DESCRIPCION' => 'No Break APC de 1500VA para servidores críticos. Incluye software de monitoreo PowerChute.',
                                    'Estado' => 'Activo',
                                    'fecha' => '2024-01-15',
                                    'ubicacion_edificio' => 'Edificio A',
                                    'ubicacion_detalle' => 'Sala de Servidores'
                                ]
                            ]
                        ];
                    }
                    
                    // Procesar datos para la respuesta
                    $resultado = [];
                    foreach ($equipos as $equipo) {
                        $ubicacionCompleta = trim(($equipo['ubicacion_edificio'] ?? '') . 
                                                  (($equipo['ubicacion_edificio'] ?? '') && ($equipo['ubicacion_detalle'] ?? '') ? ' - ' : '') . 
                                                  ($equipo['ubicacion_detalle'] ?? ''));
                        
                        $resultado[] = [
                            'id' => $equipo['idNOBREAK'],
                            'marca' => $equipo['MARCA'] ?? 'Sin especificar',
                            'modelo' => $equipo['MODELO'] ?? 'Sin especificar',
                            'numero_serie' => $equipo['NUMERO_SERIE'] ?? 'Sin especificar',
                            'estado' => $equipo['Estado'] ?? 'Sin especificar',
                            'ubicacion' => $ubicacionCompleta ?: 'Sin especificar',
                            'data' => $equipo
                        ];
                    }
                    
                    return $resultado;
                    
                } catch (Exception $e) {
                    // Log del error para debugging
                    Yii::error('Error al cargar equipos: ' . $e->getMessage(), __METHOD__);
                    Yii::error('Stack trace: ' . $e->getTraceAsString(), __METHOD__);
                    
                    return [
                        'error' => true,
                        'message' => 'Error de base de datos: ' . $e->getMessage(),
                        'details' => [
                            'codigo_error' => $e->getCode(),
                            'archivo' => basename($e->getFile()),
                            'linea' => $e->getLine(),
                            'tipo_error' => get_class($e),
                            'timestamp' => date('Y-m-d H:i:s')
                        ],
                        'sugerencia' => 'Verifica la conexión a la base de datos y que la tabla "nobreak" exista.'
                    ];
                }
            }

            return [
                'error' => true,
                'message' => 'Acción no reconocida: ' . $action,
                'received_action' => $action,
                'post_data' => Yii::$app->request->post()
            ];
        }
        
        return $this->render('editar');
    }

    public function actionTestSimple()
    {
        return $this->render('test-simple');
    }

    public function actionSimple()
    {
        // Si es una petición AJAX, devolver datos JSON
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            try {
                // Conexión directa a la base de datos
                $connection = Yii::$app->db;
                
                // Consulta simple
                $sql = "SELECT * FROM nobreak ORDER BY idNOBREAK ASC";
                $equipos = $connection->createCommand($sql)->queryAll();
                
                // Formatear datos para el frontend
                $resultado = [];
                foreach ($equipos as $equipo) {
                    $resultado[] = [
                        'id' => $equipo['idNOBREAK'],
                        'marca' => $equipo['MARCA'],
                        'modelo' => $equipo['MODELO'],
                        'capacidad' => $equipo['CAPACIDAD'],
                        'numero_serie' => $equipo['NUMERO_SERIE'],
                        'estado' => $equipo['Estado'],
                        'ubicacion_edificio' => $equipo['ubicacion_edificio'],
                        'ubicacion_detalle' => $equipo['ubicacion_detalle'],
                        'fecha' => $equipo['fecha'],
                        'inventario' => $equipo['NUMERO_INVENTARIO'],
                        'descripcion' => $equipo['DESCRIPCION']
                    ];
                }
                
                return $resultado;
                
            } catch (Exception $e) {
                return [
                    'error' => true,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $this->render('simple');
    }
    
    public function actionDirecto()
    {
        return $this->render('directo');
    }
    
    public function actionAgregarNuevo()
    {
        return $this->render('agregar_nuevo');
    }
    
    public function actionGestionCategorias()
    {
        return $this->render('gestion_categorias');
    }
    
    public function actionVerEquipos()
    {
        return $this->render('ver-equipos');
    }
    
    // ==================== ACCIONES PARA NO BREAK ====================
    public function actionNobreakAgregar()
    {
        $model = new Nobreak();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'No Break agregado exitosamente.');
                return $this->redirect(['nobreak-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al agregar el No Break.');
            }
        }

        return $this->render('nobreak/agregar', ['model' => $model]);
    }
    
    public function actionNobreakListar()
    {
        try {
            $nobreaks = Nobreak::find()->where(['!=', 'Estado', 'BAJA'])->orderBy('idNOBREAK ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $nobreaks = [];
            $error = $e->getMessage();
        }

        return $this->render('nobreak/listar', [
            'nobreaks' => $nobreaks,
            'error' => $error
        ]);
    }

    public function actionNobreakVer($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de No Break no especificado.');
            return $this->redirect(['site/nobreak-listar']);
        }

        $model = Nobreak::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'No Break no encontrado.');
            return $this->redirect(['site/nobreak-listar']);
        }

        return $this->render('nobreak/ver', [
            'model' => $model,
        ]);
    }

    public function actionNobreakEliminarMultiple()
    {
        $ids = Yii::$app->request->post('ids');
        $fromCatalog = Yii::$app->request->post('from_catalog');
        if (!empty($ids) && is_array($ids)) {
            $eliminados = 0;
            foreach ($ids as $id) {
                $model = Nobreak::findOne($id);
                if ($model !== null) {
                    if ($model->delete()) {
                        $eliminados++;
                    }
                }
            }
            if ($eliminados > 0) {
                Yii::$app->session->setFlash('success', "Se eliminaron $eliminados No Break(s).");
            }
        } else {
            Yii::$app->session->setFlash('error', 'No se seleccionaron No Break para eliminar.');
        }
        if ($fromCatalog) {
            return $this->redirect(['site/nobreak-catalogo-listar']);
        }
        return $this->redirect(['site/nobreak-listar']);
    }
    
    public function actionNobreakEditar($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de No Break no especificado.');
            return $this->redirect(['site/nobreak-listar']);
        }

        $model = Nobreak::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'No Break no encontrado.');
            return $this->redirect(['site/nobreak-listar']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'No Break actualizado exitosamente.');
            return $this->redirect(['site/nobreak-listar']);
        }

        return $this->render('nobreak/editar', [
            'model' => $model,
        ]);
    }

    /**
     * Acción para cambiar estado masivo de equipos
     */
    public function actionCambiarEstadoMasivo()
    {
        if (!Yii::$app->request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido.');
            return $this->redirect(['site/index']);
        }

        $modelo = Yii::$app->request->post('modelo');
        $equipos = Yii::$app->request->post('equipos', []);
        $nuevoEstado = Yii::$app->request->post('nuevo_estado'); // Corregido para coincidir con el formulario

        if (empty($equipos)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron equipos.');
            return $this->redirect(['site/' . strtolower($modelo) . '-listar']);
        }

        if (empty($nuevoEstado)) {
            Yii::$app->session->setFlash('error', 'Estado no especificado.');
            return $this->redirect(['site/' . strtolower($modelo) . '-listar']);
        }

        try {
            $modelClass = "frontend\\models\\$modelo";
            if (!class_exists($modelClass)) {
                throw new \Exception("Modelo $modelo no encontrado.");
            }

            $actualizados = 0;
            $errores = [];
            
            // For Monitor model, use direct SQL update to avoid property issues
            // Use direct SQL for models with validation issues
            if ($modelo === 'Monitor' || $modelo === 'Telefonia' || $modelo === 'Videovigilancia') {
                try {
                    $equiposStr = implode(',', array_map('intval', $equipos));
                    
                    // Determine table name and primary key field
                    $tableName = '';
                    $primaryKey = '';
                    
                    if ($modelo === 'Monitor') {
                        $tableName = 'monitor';
                        $primaryKey = 'idMonitor';
                    } elseif ($modelo === 'Telefonia') {
                        $tableName = 'telefonia';
                        $primaryKey = 'idTELEFONIA';
                    } elseif ($modelo === 'Videovigilancia') {
                        $tableName = 'video_vigilancia';
                        $primaryKey = 'idVIDEO_VIGILANCIA';
                    }
                    
                    $command = Yii::$app->db->createCommand(
                        "UPDATE $tableName SET ESTADO = :estado WHERE $primaryKey IN ($equiposStr)"
                    );
                    $command->bindValue(':estado', $nuevoEstado);
                    $actualizados = $command->execute();
                    
                    if ($actualizados > 0) {
                        $mensaje = "Se actualizaron $actualizados equipo(s) al estado '$nuevoEstado'.";
                        Yii::$app->session->setFlash('success', $mensaje);
                    } else {
                        Yii::$app->session->setFlash('warning', 'No se pudo actualizar ningún equipo.');
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('error', 'Error al actualizar: ' . $e->getMessage());
                }
            } else {
                // For other models, use the original ActiveRecord approach
                foreach ($equipos as $equipoId) {
                    $equipo = $modelClass::findOne($equipoId);
                    if ($equipo) {
                        // Determinar el nombre del campo de estado según el modelo y verificar que existe
                        $campoEstado = null;
                        
                        // Intentar diferentes nombres de campos según el modelo
                        if ($modelo === 'Telefonia' || $modelo === 'Videovigilancia') {
                            if ($equipo->hasAttribute('ESTADO')) {
                                $campoEstado = 'ESTADO';
                            }
                        }
                        
                        // Para otros modelos, intentar Estado primero
                        if ($campoEstado === null) {
                            if ($equipo->hasAttribute('Estado')) {
                                $campoEstado = 'Estado';
                            } elseif ($equipo->hasAttribute('estado')) {
                                $campoEstado = 'estado';
                            } elseif ($equipo->hasAttribute('ESTADO')) {
                                $campoEstado = 'ESTADO';
                            }
                        }
                        
                        if ($campoEstado === null) {
                            $errores[] = "Equipo ID $equipoId: No se encontró campo de estado válido";
                            continue;
                        }
                        
                        $equipo->$campoEstado = $nuevoEstado;
                        
                        // Agregar información del editor
                        if ($equipo->hasAttribute('fecha_ultima_edicion')) {
                            $equipo->fecha_ultima_edicion = date('Y-m-d H:i:s');
                        }
                        if ($equipo->hasAttribute('ultimo_editor')) {
                            $equipo->ultimo_editor = Yii::$app->user->identity->username ?? 'Sistema';
                        }
                        
                        if ($equipo->save()) {
                            $actualizados++;
                        } else {
                            $errores[] = "Equipo ID $equipoId: " . implode(', ', $equipo->getFirstErrors());
                        }
                    } else {
                        $errores[] = "Equipo ID $equipoId no encontrado";
                    }
                }

                if ($actualizados > 0) {
                    $mensaje = "Se actualizaron $actualizados equipo(s) al estado '$nuevoEstado'.";
                    if (!empty($errores)) {
                        $mensaje .= " Errores: " . implode('; ', $errores);
                    }
                    Yii::$app->session->setFlash('success', $mensaje);
                } else {
                    $mensaje = 'No se pudo actualizar ningún equipo.';
                    if (!empty($errores)) {
                        $mensaje .= " Errores: " . implode('; ', $errores);
                    }
                    Yii::$app->session->setFlash('warning', $mensaje);
                }
            }

        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al actualizar equipos: ' . $e->getMessage());
        }

        // Mapeo especial para redirecciones
        $redirectMap = [
            'Adaptador' => 'adaptadores-listar',
            'Monitor' => 'monitor-listar',
            'Procesador' => 'procesador-listar',
            'Ram' => 'ram-listar',
            'Bateria' => 'baterias-listar',
            'Almacenamiento' => 'almacenamiento-listar',
            'Sonido' => 'sonido-listar',
            'Microfono' => 'microfono-listar',
            'Impresora' => 'impresora-listar',
            'Equipo' => 'equipo-listar',
            'Nobreak' => 'nobreak-listar',
            'Conectividad' => 'conectividad-listar',
            'Telefonia' => 'telefonia-listar',
            'Videovigilancia' => 'videovigilancia-listar'
        ];
        
        $redirectAction = isset($redirectMap[$modelo]) ? $redirectMap[$modelo] : strtolower($modelo) . '-listar';
        return $this->redirect(['site/' . $redirectAction]);
    }
    
    // ==================== ACCIONES PARA EQUIPOS DE CÓMPUTO ====================
    public function actionEquipoAgregar()
    {
        $model = new Equipo();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo agregado exitosamente.');
                return $this->redirect(['equipo-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al agregar el equipo.');
            }
        }

        return $this->render('equipo/agregar', ['model' => $model]);
    }
    
    public function actionEquipoListar()
    {
        try {
            $equipos = Equipo::find()->where(['!=', 'Estado', 'BAJA'])->orderBy('idEQUIPO ASC')->all();
            
            // Obtener información del último equipo modificado usando campos de auditoría
            $ultimaModificacion = null;
            try {
                // Buscar el equipo con la fecha de edición más reciente
                $equipoMasReciente = Equipo::find()
                    ->orderBy('fecha_ultima_edicion DESC')
                    ->one();
                
                $totalEquipos = count($equipos);
                $equiposActivos = 0;
                
                foreach ($equipos as $equipo) {
                    if ($equipo->Estado === 'activo') {
                        $equiposActivos++;
                    }
                }
                
                if ($equipoMasReciente && !empty($equipoMasReciente->fecha_ultima_edicion)) {
                    // Calcular tiempo desde la última edición
                    $fechaUltima = new \DateTime($equipoMasReciente->fecha_ultima_edicion);
                    $fechaActual = new \DateTime();
                    $diferencia = $fechaActual->diff($fechaUltima);
                    
                    $tiempoTranscurrido = '';
                    if ($diferencia->days == 0) {
                        if ($diferencia->h == 0) {
                            $tiempoTranscurrido = 'Hace ' . $diferencia->i . ' minutos';
                        } else {
                            $tiempoTranscurrido = 'Hace ' . $diferencia->h . ' horas';
                        }
                    } elseif ($diferencia->days == 1) {
                        $tiempoTranscurrido = 'Ayer';
                    } else {
                        $tiempoTranscurrido = 'Hace ' . $diferencia->days . ' días';
                    }
                    
                    // Obtener información del usuario que editó
                    $userInfo = $equipoMasReciente->getInfoUsuarioEditor();
                    
                    $ultimaModificacion = [
                        'equipo' => $equipoMasReciente->MARCA . ' ' . $equipoMasReciente->MODELO,
                        'id' => $equipoMasReciente->idEQUIPO,
                        'fecha_edicion' => $equipoMasReciente->fecha_ultima_edicion,
                        'usuario' => $userInfo['username'],
                        'usuario_email' => $userInfo['email'],
                        'usuario_display' => $userInfo['display_name'],
                        'fecha_formateada' => date('d/m/Y H:i', strtotime($equipoMasReciente->fecha_ultima_edicion)),
                        'tiempo_transcurrido' => $tiempoTranscurrido,
                        'total_equipos' => $totalEquipos,
                        'equipos_activos' => $equiposActivos
                    ];
                }
            } catch (Exception $e) {
                // Si hay error, continuar sin la información de última modificación
                $ultimaModificacion = null;
            }
            
            $error = null;
        } catch (Exception $e) {
            $equipos = [];
            $ultimaModificacion = null;
            $error = $e->getMessage();
        }

        return $this->render('equipo/listar', [
            'equipos' => $equipos,
            'ultimaModificacion' => $ultimaModificacion,
            'error' => $error
        ]);
    }
    
    /**
     * Vista de solo lectura de un equipo (para QR)
     */
    public function actionEquipoVer($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de Equipo no especificado.');
            return $this->redirect(['site/equipo-listar']);
        }

        $model = Equipo::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Equipo no encontrado.');
            return $this->redirect(['site/equipo-listar']);
        }

        return $this->render('equipo/ver', [
            'model' => $model,
        ]);
    }
    
    public function actionEquipoEditar($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de Equipo no especificado.');
            return $this->redirect(['site/equipo-listar']);
        }

        $model = Equipo::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Equipo no encontrado.');
            return $this->redirect(['site/equipo-listar']);
        }

        if ($model->load(Yii::$app->request->post())) {
            // Procesar campos DD2, DD3, DD4 si no están marcados
            if (empty($model->DD2) || $model->DD2 === '' || $model->DD2 === 'NO') {
                $model->DD2 = 'NO';
                $model->DD2_ID = null;
            }
            if (empty($model->DD3) || $model->DD3 === '' || $model->DD3 === 'NO') {
                $model->DD3 = 'NO';
                $model->DD3_ID = null;
            }
            if (empty($model->DD4) || $model->DD4 === '' || $model->DD4 === 'NO') {
                $model->DD4 = 'NO';
                $model->DD4_ID = null;
            }
            
            // Procesar campos RAM2, RAM3, RAM4 si no están marcados
            if (empty($model->RAM2) || $model->RAM2 === '' || $model->RAM2 === 'NO') {
                $model->RAM2 = 'NO';
                $model->RAM2_ID = null;
            }
            if (empty($model->RAM3) || $model->RAM3 === '' || $model->RAM3 === 'NO') {
                $model->RAM3 = 'NO';
                $model->RAM3_ID = null;
            }
            if (empty($model->RAM4) || $model->RAM4 === '' || $model->RAM4 === 'NO') {
                $model->RAM4 = 'NO';
                $model->RAM4_ID = null;
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo actualizado exitosamente.');
                return $this->redirect(['site/equipo-listar']);
            } else {
                // Mostrar errores específicos de validación
                $errors = [];
                foreach ($model->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = "$field: $error";
                    }
                }
                $errorMessage = empty($errors) ? 'Error desconocido al actualizar el equipo.' : 'Errores de validación: ' . implode('; ', $errors);
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        // Obtener catálogos de componentes
        $almacenamiento = \frontend\models\Almacenamiento::find()->all();
        $memoriaRam = \frontend\models\Ram::find()->all();
        $procesadores = \frontend\models\Procesador::find()->all();

        return $this->render('equipo/editar', [
            'model' => $model,
            'almacenamiento' => $almacenamiento,
            'memoriaRam' => $memoriaRam,
            'procesadores' => $procesadores,
        ]);
    }
    
    /**
     * Procesa campos especiales del formulario de equipo
     */
    private function procesarCamposEspeciales($model)
    {
        // Procesar campos DD que no están seleccionados
        if (empty($model->DD2) || $model->DD2 === 'NO') {
            $model->DD2 = 'NO';
        }
        if (empty($model->DD3) || $model->DD3 === 'NO') {
            $model->DD3 = 'NO';
        }
        if (empty($model->DD4) || $model->DD4 === 'NO') {
            $model->DD4 = 'NO';
        }
        
        // Procesar campos RAM que no están seleccionados
        if (empty($model->RAM2) || $model->RAM2 === 'NO') {
            $model->RAM2 = 'NO';
        }
        if (empty($model->RAM3) || $model->RAM3 === 'NO') {
            $model->RAM3 = 'NO';
        }
        if (empty($model->RAM4) || $model->RAM4 === 'NO') {
            $model->RAM4 = 'NO';
        }
    }
    
    // ==================== ACCIONES PARA IMPRESORAS ====================
    public function actionImpresoraAgregar()
    {
        $model = new Impresora();
        
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                
                // Configurar fecha de creación si no está establecida
                if (empty($model->fecha_creacion)) {
                    $model->fecha_creacion = date('Y-m-d H:i:s');
                }
                
                if ($model->save()) {
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'success' => true,
                            'message' => 'Impresora agregada exitosamente.',
                            'redirect' => Url::to(['site/impresora-listar'])
                        ];
                    } else {
                        Yii::$app->session->setFlash('success', 'Impresora agregada exitosamente.');
                        return $this->redirect(['site/impresora-listar']);
                    }
                } else {
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return [
                            'success' => false,
                            'errors' => $model->getErrors(),
                            'message' => 'No se pudo guardar la impresora.'
                        ];
                    } else {
                        Yii::$app->session->setFlash('error', 'No se pudo guardar la impresora. Revisa los errores.');
                    }
                }
            } else {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'errors' => $model->getErrors(),
                        'message' => 'Datos inválidos.'
                    ];
                }
            }
        }
        
        return $this->render('impresora/agregar', [
            'model' => $model,
        ]);
    }
    
    public function actionImpresoraListar()
    {
        try {
            $impresoras = Impresora::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idIMPRESORA ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $impresoras = [];
            $error = $e->getMessage();
        }

        return $this->render('impresora/listar', [
            'impresoras' => $impresoras,
            'error' => $error
        ]);
    }

    public function actionImpresoraVer($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de Impresora no especificado.');
            return $this->redirect(['site/impresora-listar']);
        }

        $model = Impresora::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Impresora no encontrada.');
            return $this->redirect(['site/impresora-listar']);
        }

        return $this->render('impresora/ver', [
            'model' => $model,
        ]);
    }

    public function actionImpresoraEliminarMultiple()
    {
        $ids = Yii::$app->request->post('ids');
        if (!empty($ids) && is_array($ids)) {
            $eliminados = 0;
            $catalogoEncontrado = false;
            foreach ($ids as $id) {
                $model = Impresora::findOne($id);
                if ($model !== null) {
                    // PROTECCIÓN: No permitir eliminar items del catálogo
                    if (!empty($model->ubicacion_detalle) && stripos($model->ubicacion_detalle, 'Catálogo') !== false) {
                        $catalogoEncontrado = true;
                        continue;
                    }
                    if ($model->delete()) {
                        $eliminados++;
                    }
                }
            }
            if ($catalogoEncontrado) {
                Yii::$app->session->setFlash('warning', 'Se omitieron items del catálogo. Los items del catálogo no se pueden eliminar.');
            }
            if ($eliminados > 0) {
                Yii::$app->session->setFlash('success', "Las impresoras seleccionadas han sido eliminadas ($eliminados).");
            }
        } else {
            Yii::$app->session->setFlash('error', 'No se seleccionaron impresoras para eliminar.');
        }
        return $this->redirect(['site/impresora-listar']);
    }
    
    public function actionImpresoraEditar($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de Impresora no especificado.');
            return $this->redirect(['site/impresora-listar']);
        }

        $model = Impresora::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Impresora no encontrada.');
            return $this->redirect(['site/impresora-listar']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Impresora actualizada exitosamente.');
                return $this->redirect(['site/impresora-listar']);
            } else {
                // Mostrar errores específicos de validación
                $errors = [];
                foreach ($model->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = "$field: $error";
                    }
                }
                $errorMessage = empty($errors) ? 'Error desconocido al actualizar la impresora.' : 'Errores de validación: ' . implode('; ', $errors);
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        return $this->render('impresora/editar', [
            'model' => $model,
        ]);
    }
    
    // ==================== ACCIONES PARA MONITORES ====================
    public function actionMonitorAgregar()
    {
        $model = new Monitor();
        $modoSimplificado = Yii::$app->request->get('simple', false);

        if ($model->load(Yii::$app->request->post())) {
            if ($modoSimplificado) {
                // Establecer escenario simplificado ANTES de procesar
                $model->scenario = 'simplificado';
                
                // Solo procesar MARCA y MODELO del POST
                $postData = Yii::$app->request->post('Monitor', []);
                if (isset($postData['MARCA'])) $model->MARCA = $postData['MARCA'];
                if (isset($postData['MODELO'])) $model->MODELO = $postData['MODELO'];
                
                // Establecer ubicacion_detalle como Catálogo automáticamente
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Monitor agregado al catálogo exitosamente.');
                    return $this->redirect(['monitor-catalogo-listar']);
                }
            } else {
                // Modo normal - validación completa
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Monitor agregado exitosamente.');
                    
                    // Si hay parámetro redirect, redirigir al formulario de equipos
                    $redirect = Yii::$app->request->get('redirect');
                    if ($redirect === 'computo') {
                        return $this->redirect(['computo']);
                    }
                    
                    return $this->redirect(['monitor-listar']);
                }
            }
        }

        return $this->render('monitor/agregar', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionMonitorListar()
    {
        try {
            $monitores = Monitor::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['!=', 'ubicacion_edificio', 'Catálogo'])
                ->orderBy('idMonitor ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $monitores = [];
            $error = $e->getMessage();
        }

        return $this->render('monitor/listar', [
            'monitores' => $monitores,
            'error' => $error
        ]);
    }

    public function actionMonitorVer($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de Monitor no especificado.');
            return $this->redirect(['site/monitor-listar']);
        }

        $model = Monitor::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Monitor no encontrado.');
            return $this->redirect(['site/monitor-listar']);
        }

        return $this->render('monitor/ver', [
            'model' => $model,
        ]);
    }

    public function actionMonitorEliminarMultiple()
    {
        $ids = Yii::$app->request->post('ids');
        if (!empty($ids) && is_array($ids)) {
            foreach ($ids as $id) {
                $model = Monitor::findOne($id);
                if ($model !== null) {
                    $model->delete();
                }
            }
            Yii::$app->session->setFlash('success', 'Los monitores seleccionados han sido eliminados.');
        } else {
            Yii::$app->session->setFlash('error', 'No se seleccionaron monitores para eliminar.');
        }
        return $this->redirect(['site/monitor-listar']);
    }
    
    public function actionMonitorEditar($id = null)
    {
        if ($id === null) {
            throw new \yii\web\BadRequestHttpException('ID no proporcionado.');
        }

        $model = Monitor::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Monitor no encontrado.');
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Monitor actualizado exitosamente.');
                return $this->redirect(['monitor-listar']);
            } else {
                // Mostrar errores específicos de validación
                $errors = [];
                foreach ($model->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = "$field: $error";
                    }
                }
                $errorMessage = empty($errors) ? 'Error desconocido al actualizar el monitor.' : 'Errores de validación: ' . implode('; ', $errors);
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        return $this->render('monitor/editar', ['model' => $model]);
    }
    
    // ==================== ACCIONES PARA MICRÓFONOS ====================
    public function actionMicrofonoAgregar()
    {
        return $this->render('microfono/agregar');
    }
    
    public function actionMicrofonoListar()
    {
        try {
            $microfonos = Microfono::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idMicrofono ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $microfonos = [];
            $error = $e->getMessage();
        }

        return $this->render('microfono/listar', [
            'microfonos' => $microfonos,
            'error' => $error
        ]);
    }
    
    public function actionMicrofonoEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de micrófono no proporcionado.');
            return $this->redirect(['microfono-listar']);
        }

        $model = Microfono::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Micrófono no encontrado.');
            return $this->redirect(['microfono-listar']);
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Micrófono actualizado exitosamente.');
                return $this->redirect(['microfono-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar el micrófono.');
            }
        }

        return $this->render('microfono/editar', ['model' => $model]);
    }
    
    // ==================== ACCIONES ADICIONALES ====================
    public function actionReportes()
    {
        return $this->render('reportes');
    }
    
    // ==================== ACCIONES PARA ADAPTADORES ====================
    public function actionAdaptadores()
    {
        $model = new Adaptador();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->fecha = date('Y-m-d');
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Adaptador agregado exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['adaptadores-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('adaptadores', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionAdaptadorAgregar()
    {
        $model = new Adaptador();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Adaptador agregado exitosamente.');
                return $this->redirect(['adaptadores-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al agregar el adaptador.');
            }
        }

        return $this->render('adaptador/agregar', ['model' => $model]);
    }
    
    public function actionAdaptadoresListar()
    {
        try {
            $adaptadores = Adaptador::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idAdaptador ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $adaptadores = [];
            $error = $e->getMessage();
        }

        return $this->render('adaptador/listar', [
            'adaptadores' => $adaptadores,
            'error' => $error
        ]);
    }
    
    public function actionAdaptadorVer($id)
    {
        $model = Adaptador::findOne(['idAdaptador' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El adaptador no existe.');
        }
        return $this->render('adaptador/ver', ['model' => $model]);
    }

    public function actionAdaptadorEliminarMultiple()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            return $this->redirect(['site/adaptadores-listar']);
        }
        $ids = $request->post('ids');
        if (!$ids || !is_array($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron adaptadores');
            return $this->redirect(['site/adaptadores-listar']);
        }
        $eliminados = 0;
        $catalogoEncontrado = false;
        foreach ($ids as $id) {
            $model = Adaptador::findOne(['idAdaptador' => $id]);
            if ($model) {
                // PROTECCIÓN: No permitir eliminar items del catálogo
                if (!empty($model->ubicacion_detalle) && stripos($model->ubicacion_detalle, 'Catálogo') !== false) {
                    $catalogoEncontrado = true;
                    continue;
                }
                if ($model->delete()) {
                    $eliminados++;
                }
            }
        }
        if ($catalogoEncontrado) {
            Yii::$app->session->setFlash('warning', 'Se omitieron items del catálogo. Los items del catálogo no se pueden eliminar.');
        }
        if ($eliminados > 0) {
            Yii::$app->session->setFlash('success', "Se eliminaron $eliminados adaptador(es)");
        }
        return $this->redirect(['site/adaptadores-listar']);
    }

    public function actionAdaptadorEditar($id = null)
    {
        if ($id === null) {
            Yii::$app->session->setFlash('error', 'ID de Adaptador no especificado.');
            return $this->redirect(['site/adaptadores-listar']);
        }

        $model = Adaptador::findOne($id);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Adaptador no encontrado.');
            return $this->redirect(['site/adaptadores-listar']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Adaptador actualizado exitosamente.');
                return $this->redirect(['site/adaptadores-listar']);
            } else {
                // Mostrar errores específicos de validación
                $errors = [];
                foreach ($model->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = "$field: $error";
                    }
                }
                $errorMessage = empty($errors) ? 'Error desconocido al actualizar el adaptador.' : 'Errores de validación: ' . implode('; ', $errors);
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        return $this->render('adaptador/editar', [
            'model' => $model,
        ]);
    }
    
    // ==================== ACCIONES PARA BATERÍAS ====================
    public function actionBaterias()
    {
        $model = new Bateria();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Batería agregada exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['baterias-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('baterias', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionBateriasListar()
    {
        try {
            $baterias = Bateria::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idBateria ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $baterias = [];
            $error = $e->getMessage();
        }

        return $this->render('bateria/listar', [
            'baterias' => $baterias,
            'error' => $error
        ]);
    }
    
    public function actionBateriaVer($id)
    {
        $model = Bateria::findOne(['idBateria' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('La batería no existe.');
        }
        return $this->render('bateria/ver', ['model' => $model]);
    }

    public function actionBateriaEliminarMultiple()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }
        
        // Obtener datos JSON del body
        $rawBody = $request->getRawBody();
        $data = json_decode($rawBody, true);
        $ids = $data['ids'] ?? $request->post('ids');
        
        if (!$ids || !is_array($ids)) {
            return ['success' => false, 'message' => 'No se seleccionaron baterías'];
        }
        
        $eliminados = 0;
        $catalogoEncontrado = false;
        foreach ($ids as $id) {
            $model = Bateria::findOne(['idBateria' => $id]);
            if ($model) {
                // PROTECCIÓN: No permitir eliminar items del catálogo
                if (!empty($model->ubicacion_detalle) && stripos($model->ubicacion_detalle, 'Catálogo') !== false) {
                    $catalogoEncontrado = true;
                    continue;
                }
                if ($model->delete()) {
                    $eliminados++;
                }
            }
        }
        
        $mensaje = "Se eliminaron $eliminados batería(s)";
        if ($catalogoEncontrado) {
            $mensaje .= '. Se omitieron items del catálogo (no se pueden eliminar)';
        }
        
        return ['success' => true, 'message' => $mensaje];
    }

    public function actionBateriaEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de batería no proporcionado.');
            return $this->redirect(['baterias-listar']);
        }

        $model = Bateria::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Batería no encontrada.');
            return $this->redirect(['baterias-listar']);
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Batería actualizada exitosamente.');
                return $this->redirect(['baterias-listar']);
            } else {
                // Mostrar errores específicos de validación
                $errors = [];
                foreach ($model->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = "$field: $error";
                    }
                }
                $errorMessage = empty($errors) ? 'Error desconocido al actualizar la batería.' : 'Errores de validación: ' . implode('; ', $errors);
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        return $this->render('bateria/editar', ['model' => $model]);
    }
    
    // ==================== ACCIONES PARA ALMACENAMIENTO ====================
    public function actionDispositivosDeAlmacenamiento()
    {
        $model = new Almacenamiento();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->CAPACIDAD = $model->CAPACIDAD ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Dispositivo de almacenamiento agregado exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['site/almacenamiento-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('dispositivos-de-almacenamiento', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionAlmacenamientoAgregar()
    {
        $model = new Almacenamiento();
        
        // Verificar si viene desde el formulario de equipo (modo simplificado)
        $modoSimplificado = Yii::$app->request->get('simple', false) || 
                           (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'computo') !== false);

        if ($model->load(Yii::$app->request->post())) {
            if ($modoSimplificado) {
                // Establecer escenario simplificado ANTES de procesar
                $model->scenario = 'simplificado';
                
                // Procesar MARCA, MODELO, CAPACIDAD y TIPO del POST
                $postData = Yii::$app->request->post('Almacenamiento', []);
                if (isset($postData['MARCA'])) $model->MARCA = $postData['MARCA'];
                if (isset($postData['MODELO'])) $model->MODELO = $postData['MODELO'];
                if (isset($postData['CAPACIDAD'])) $model->CAPACIDAD = $postData['CAPACIDAD'];
                if (isset($postData['TIPO'])) $model->TIPO = $postData['TIPO'];
                
                // NO asignar otros campos - dejar que se manejen automáticamente
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Dispositivo de almacenamiento agregado al catálogo exitosamente.');
                    
                    // Manejar redirección si viene del formulario de equipos
                    if (Yii::$app->request->get('redirect') === 'computo') {
                        return $this->redirect(['computo']);
                    }
                    
                    return $this->refresh();
                }
            } else {
                // Modo normal - validación completa
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Dispositivo de almacenamiento agregado exitosamente.');
                    
                    // Manejar redirección si viene del formulario de equipos
                    if (Yii::$app->request->get('redirect') === 'computo') {
                        return $this->redirect(['computo']);
                    }
                    
                    return $this->redirect(['almacenamiento-listar']);
                } else {
                    Yii::$app->session->setFlash('error', 'Error al agregar el dispositivo de almacenamiento.');
                }
            }
        }

        return $this->render('almacenamiento/agregar', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionAlmacenamientoListar()
    {
        try {
            // Excluir los equipos que están en el catálogo y los dados de baja
            $almacenamientos = Almacenamiento::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere([
                    'or',
                    ['not like', 'ubicacion_detalle', 'Catálogo'],
                    ['ubicacion_detalle' => null],
                    ['ubicacion_detalle' => '']
                ])
                ->orderBy('idAlmacenamiento ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $almacenamientos = [];
            $error = 'Error al cargar dispositivos de almacenamiento: ' . $e->getMessage();
        }
        
        return $this->render('almacenamiento/listar', [
            'almacenamientos' => $almacenamientos,
            'error' => $error
        ]);
    }
    
    public function actionAlmacenamientoVer($id)
    {
        $model = Almacenamiento::findOne(['idAlmacenamiento' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El dispositivo de almacenamiento no existe.');
        }
        return $this->render('almacenamiento/ver', ['model' => $model]);
    }

    public function actionAlmacenamientoEliminarMultiple()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            return $this->redirect(['site/almacenamiento-listar']);
        }
        $ids = $request->post('ids');
        if (!$ids || !is_array($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron dispositivos');
            return $this->redirect(['site/almacenamiento-listar']);
        }
        $eliminados = 0;
        foreach ($ids as $id) {
            $model = Almacenamiento::findOne(['idAlmacenamiento' => $id]);
            if ($model && $model->delete()) {
                $eliminados++;
            }
        }
        Yii::$app->session->setFlash('success', "Se eliminaron $eliminados dispositivo(s)");
        return $this->redirect(['site/almacenamiento-listar']);
    }

    public function actionAlmacenamientoEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de dispositivo de almacenamiento no proporcionado.');
            return $this->redirect(['almacenamiento-listar']);
        }

        $model = Almacenamiento::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Dispositivo de almacenamiento no encontrado.');
            return $this->redirect(['almacenamiento-listar']);
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Dispositivo de almacenamiento actualizado exitosamente.');
                return $this->redirect(['almacenamiento-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar el dispositivo de almacenamiento.');
            }
        }

        return $this->render('almacenamiento/editar', ['model' => $model]);
    }
    
    // ==================== ACCIONES PARA MEMORIA RAM ====================
    public function actionMemoriaRam()
    {
        $model = new Ram();
        
        // Verificar si viene desde el formulario de equipo (modo simplificado)
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            if ($modoSimplificado) {
                // Establecer escenario simplificado ANTES de procesar
                $model->scenario = 'simplificado';
                
                // Solo procesar MARCA, MODELO y CAPACIDAD del POST
                $postData = Yii::$app->request->post('Ram', []);
                if (isset($postData['MARCA'])) $model->MARCA = $postData['MARCA'];
                if (isset($postData['MODELO'])) $model->MODELO = $postData['MODELO'];
                if (isset($postData['CAPACIDAD'])) $model->CAPACIDAD = $postData['CAPACIDAD'];
                
                // Establecer ubicacion_detalle como Catálogo automáticamente
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                
                // NO asignar otros campos - dejar que se manejen automáticamente o se queden vacíos
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Memoria RAM agregada al catálogo exitosamente.');
                    
                    if ($modoSimplificado) {
                        return $this->redirect(['ram-catalogo-listar']);
                    }
                    
                    // Manejar redirección si viene del formulario de equipos
                    if (Yii::$app->request->get('redirect') === 'computo') {
                        return $this->redirect(['computo']);
                    }
                    
                    return $this->refresh();
                }
            } else {
                // Modo normal - validación completa
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Memoria RAM agregada exitosamente.');
                    
                    // Manejar redirección si viene del formulario de equipos
                    if (Yii::$app->request->get('redirect') === 'computo') {
                        return $this->redirect(['computo']);
                    }
                    
                    return $this->refresh();
                }
            }
        }
        
        return $this->render('memoria-ram', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionRamListar()
    {
        try {
            $rams = Ram::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['!=', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('idRAM ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $rams = [];
            $error = $e->getMessage();
        }

        return $this->render('ram/listar', [
            'rams' => $rams,
            'error' => $error
        ]);
    }
    
    public function actionRamVer($id)
    {
        $model = Ram::findOne(['idRAM' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('La memoria RAM no existe.');
        }
        return $this->render('ram/ver', ['model' => $model]);
    }

    public function actionRamEliminarMultiple()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            return $this->redirect(['site/ram-listar']);
        }
        $ids = $request->post('ids');
        if (!$ids || !is_array($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron memorias RAM');
            return $this->redirect(['site/ram-listar']);
        }
        $eliminados = 0;
        foreach ($ids as $id) {
            $model = Ram::findOne(['idRAM' => $id]);
            if ($model && $model->delete()) {
                $eliminados++;
            }
        }
        Yii::$app->session->setFlash('success', "Se eliminaron $eliminados memoria(s) RAM");
        return $this->redirect(['site/ram-listar']);
    }

    public function actionRamEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de memoria RAM no proporcionado.');
            return $this->redirect(['ram-listar']);
        }

        $model = Ram::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Memoria RAM no encontrada.');
            return $this->redirect(['ram-listar']);
        }

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            // Remover campos de auditoría del POST para evitar conflictos
            if (isset($postData['Ram']['fecha_creacion'])) {
                unset($postData['Ram']['fecha_creacion']);
            }
            if (isset($postData['Ram']['fecha_ultima_edicion'])) {
                unset($postData['Ram']['fecha_ultima_edicion']);
            }
            
            if ($model->load($postData) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Memoria RAM actualizada exitosamente.');
                return $this->redirect(['ram-listar']);
            } else {
                // Mostrar errores específicos de validación
                $errors = [];
                foreach ($model->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = "$field: $error";
                    }
                }
                $errorMessage = empty($errors) ? 'Error desconocido al actualizar la memoria RAM.' : 'Errores de validación: ' . implode('; ', $errors);
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        return $this->render('ram/editar', ['model' => $model]);
    }

    public function actionRamCatalogoListar()
    {
        try {
            // Obtener solo elementos del catálogo (ubicacion_detalle contiene 'Catálogo')
            $rams = Ram::find()
                ->where(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('MARCA ASC, MODELO ASC')
                ->all();
            
            $error = null;
        } catch (Exception $e) {
            $rams = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/ram-listar', [
            'rams' => $rams,
            'error' => $error
        ]);
    }

    public function actionRamEliminar()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['ram-catalogo-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['ram-catalogo-listar']);
        }

        try {
            $ram = Ram::findOne(['idRAM' => $id]);
            
            if (!$ram) {
                Yii::$app->session->setFlash('error', "Módulo RAM con ID $id no encontrado");
                return $this->redirect(['ram-catalogo-listar']);
            }
            
            // PROTECCIÓN: No permitir eliminar items del catálogo
            if (!empty($ram->ubicacion_detalle) && stripos($ram->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se pueden eliminar items del catálogo. Los items del catálogo son reutilizables infinitamente.');
                return $this->redirect(['ram-catalogo-listar']);
            }
            
            $marca = $ram->MARCA ?? 'Sin marca';
            $modelo = $ram->MODELO ?? 'Sin modelo';
            
            // Eliminar el módulo RAM
            if ($ram->delete()) {
                Yii::$app->session->setFlash('success', "Módulo RAM $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el módulo RAM');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['ram-catalogo-listar']);
    }

    // Acciones específicas para eliminación desde el listado de RAM
    public function actionEliminarRam()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/ram-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/ram-listar']);
        }

        try {
            $ram = Ram::findOne(['idRAM' => $id]);
            
            if (!$ram) {
                Yii::$app->session->setFlash('error', "Módulo de RAM con ID $id no encontrado");
                return $this->redirect(['site/ram-listar']);
            }
            
            // Verificar si es un elemento de catálogo y protegerlo
            if (strpos($ram->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se puede eliminar un módulo de RAM del catálogo. Los elementos del catálogo están protegidos para reutilización infinita.');
                return $this->redirect(['site/ram-listar']);
            }
            
            $marca = $ram->MARCA ?? 'Sin marca';
            $capacidad = $ram->CAPACIDAD ?? 'Sin capacidad';
            
            // Eliminar el módulo RAM
            if ($ram->delete()) {
                Yii::$app->session->setFlash('success', "Módulo de RAM $marca {$capacidad}GB eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el módulo de RAM');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/ram-listar']);
    }

    public function actionEliminarRamMasivo()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/ram-listar']);
        }

        $idsJson = $request->post('ids');
        
        if (empty($idsJson)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron módulos de RAM para eliminar');
            return $this->redirect(['site/ram-listar']);
        }

        $ids = json_decode($idsJson, true);
        
        if (!is_array($ids) || empty($ids)) {
            Yii::$app->session->setFlash('error', 'Datos de selección inválidos');
            return $this->redirect(['site/ram-listar']);
        }

        try {
            $eliminados = 0;
            $errores = [];
            $protegidos = 0;

            foreach ($ids as $id) {
                if (empty($id)) continue;
                
                $ram = Ram::findOne(['idRAM' => $id]);
                
                if (!$ram) {
                    $errores[] = "Módulo de RAM con ID $id no encontrado";
                    continue;
                }

                // Verificar si es un elemento de catálogo y protegerlo
                if (strpos($ram->ubicacion_detalle, 'Catálogo') !== false) {
                    $protegidos++;
                    continue;
                }

                if ($ram->delete()) {
                    $eliminados++;
                } else {
                    $errores[] = "Error al eliminar módulo de RAM ID $id";
                }
            }

            // Construir mensaje informativo
            $messages = [];
            if ($eliminados > 0) {
                $messages[] = "Se eliminaron $eliminados módulos de RAM exitosamente";
            }
            if ($protegidos > 0) {
                $messages[] = "$protegidos módulos de RAM del catálogo fueron protegidos (no se eliminan)";
            }
            if (count($errores) > 0) {
                $messages[] = "Errores: " . implode(', ', array_slice($errores, 0, 3));
                if (count($errores) > 3) {
                    $messages[count($messages)-1] .= " y " . (count($errores) - 3) . " más";
                }
            }

            if (!empty($messages)) {
                $finalMessage = implode('. ', $messages);
                if ($eliminados > 0 || $protegidos > 0) {
                    Yii::$app->session->setFlash('success', $finalMessage);
                } else {
                    Yii::$app->session->setFlash('error', $finalMessage);
                }
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo procesar ningún módulo de RAM');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al procesar eliminación: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/ram-listar']);
    }
    
    // ==================== ACCIONES PARA PROCESADORES ====================
    public function actionProcesadorEliminar()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/catalogo-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/catalogo-listar']);
        }

        try {
            $procesador = Procesador::findOne(['idProcesador' => $id]);
            
            if (!$procesador) {
                Yii::$app->session->setFlash('error', "Procesador con ID $id no encontrado");
                return $this->redirect(['site/catalogo-listar']);
            }
            
            // PROTECCIÓN: No permitir eliminar items del catálogo
            if (!empty($procesador->ubicacion_detalle) && stripos($procesador->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se pueden eliminar items del catálogo. Los items del catálogo son reutilizables infinitamente.');
                return $this->redirect(['site/catalogo-listar']);
            }
            
            $marca = $procesador->MARCA ?? 'Sin marca';
            $modelo = $procesador->MODELO ?? 'Sin modelo';
            
            // Eliminar el procesador
            if ($procesador->delete()) {
                Yii::$app->session->setFlash('success', "Procesador $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el procesador');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/catalogo-listar']);
    }

    public function actionProcesadorEliminarMultiple()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/catalogo-listar']);
        }

        $ids = $request->post('ids');
        
        if (!$ids || !is_array($ids) || empty($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron procesadores para eliminar');
            return $this->redirect(['site/catalogo-listar']);
        }

        try {
            $eliminados = 0;
            $errores = [];

            foreach ($ids as $id) {
                if (empty($id)) continue;
                
                $procesador = Procesador::findOne(['idProcesador' => $id]);
                
                if (!$procesador) {
                    $errores[] = "Procesador con ID $id no encontrado";
                    continue;
                }

                if ($procesador->delete()) {
                    $eliminados++;
                } else {
                    $errores[] = "Error al eliminar procesador ID $id";
                }
            }

            if ($eliminados > 0) {
                $message = "Se eliminaron $eliminados procesadores exitosamente";
                if (count($errores) > 0) {
                    $message .= ". Algunos errores: " . implode(', ', $errores);
                }
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo eliminar ningún procesador. ' . implode(', ', $errores));
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al procesar eliminación: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/catalogo-listar']);
    }

    // Acciones específicas para eliminación desde el listado de procesadores
    public function actionEliminarProcesador()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/procesador-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/procesador-listar']);
        }

        try {
            $procesador = Procesador::findOne(['idProcesador' => $id]);
            
            if (!$procesador) {
                Yii::$app->session->setFlash('error', "Procesador con ID $id no encontrado");
                return $this->redirect(['site/procesador-listar']);
            }
            
            // Verificar si es un elemento de catálogo y protegerlo
            if (strpos($procesador->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se puede eliminar un procesador del catálogo. Los elementos del catálogo están protegidos para reutilización infinita.');
                return $this->redirect(['site/procesador-listar']);
            }
            
            $marca = $procesador->MARCA ?? 'Sin marca';
            $modelo = $procesador->MODELO ?? 'Sin modelo';
            
            // Eliminar el procesador
            if ($procesador->delete()) {
                Yii::$app->session->setFlash('success', "Procesador $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el procesador');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/procesador-listar']);
    }

    public function actionEliminarProcesadoresMasivo()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/procesador-listar']);
        }

        $idsJson = $request->post('ids');
        
        if (empty($idsJson)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron procesadores para eliminar');
            return $this->redirect(['site/procesador-listar']);
        }

        $ids = json_decode($idsJson, true);
        
        if (!is_array($ids) || empty($ids)) {
            Yii::$app->session->setFlash('error', 'Datos de selección inválidos');
            return $this->redirect(['site/procesador-listar']);
        }

        try {
            $eliminados = 0;
            $errores = [];
            $protegidos = 0;

            foreach ($ids as $id) {
                if (empty($id)) continue;
                
                $procesador = Procesador::findOne(['idProcesador' => $id]);
                
                if (!$procesador) {
                    $errores[] = "Procesador con ID $id no encontrado";
                    continue;
                }

                // Verificar si es un elemento de catálogo y protegerlo
                if (strpos($procesador->ubicacion_detalle, 'Catálogo') !== false) {
                    $protegidos++;
                    continue;
                }

                if ($procesador->delete()) {
                    $eliminados++;
                } else {
                    $errores[] = "Error al eliminar procesador ID $id";
                }
            }

            // Construir mensaje informativo
            $messages = [];
            if ($eliminados > 0) {
                $messages[] = "Se eliminaron $eliminados procesadores exitosamente";
            }
            if ($protegidos > 0) {
                $messages[] = "$protegidos procesadores del catálogo fueron protegidos (no se eliminan)";
            }
            if (count($errores) > 0) {
                $messages[] = "Errores: " . implode(', ', array_slice($errores, 0, 3));
                if (count($errores) > 3) {
                    $messages[count($messages)-1] .= " y " . (count($errores) - 3) . " más";
                }
            }

            if (!empty($messages)) {
                $finalMessage = implode('. ', $messages);
                if ($eliminados > 0 || $protegidos > 0) {
                    Yii::$app->session->setFlash('success', $finalMessage);
                } else {
                    Yii::$app->session->setFlash('error', $finalMessage);
                }
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo procesar ningún procesador');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al procesar eliminación: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/procesador-listar']);
    }
    
    // ==================== ACCIONES PARA ALMACENAMIENTO ====================
    public function actionAlmacenamientoEliminar()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/almacenamiento-catalogo-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/almacenamiento-catalogo-listar']);
        }

        try {
            $almacenamiento = Almacenamiento::findOne(['idAlmacenamiento' => $id]);
            
            if (!$almacenamiento) {
                Yii::$app->session->setFlash('error', "Dispositivo con ID $id no encontrado");
                return $this->redirect(['site/almacenamiento-catalogo-listar']);
            }
            
            // PROTECCIÓN: No permitir eliminar items del catálogo
            if (!empty($almacenamiento->ubicacion_detalle) && stripos($almacenamiento->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se pueden eliminar items del catálogo. Los items del catálogo son reutilizables infinitamente.');
                return $this->redirect(['site/almacenamiento-catalogo-listar']);
            }
            
            $marca = $almacenamiento->MARCA ?? 'Sin marca';
            $modelo = $almacenamiento->MODELO ?? 'Sin modelo';
            
            // Eliminar el dispositivo
            if ($almacenamiento->delete()) {
                Yii::$app->session->setFlash('success', "Dispositivo $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el dispositivo');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/almacenamiento-catalogo-listar']);
    }

    // Acciones específicas para eliminación desde el listado de almacenamiento
    public function actionEliminarAlmacenamiento()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/almacenamiento-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/almacenamiento-listar']);
        }

        try {
            $almacenamiento = Almacenamiento::findOne(['idAlmacenamiento' => $id]);
            
            if (!$almacenamiento) {
                Yii::$app->session->setFlash('error', "Dispositivo de almacenamiento con ID $id no encontrado");
                return $this->redirect(['site/almacenamiento-listar']);
            }
            
            // Verificar si es un elemento de catálogo y protegerlo
            if (strpos($almacenamiento->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se puede eliminar un dispositivo de almacenamiento del catálogo. Los elementos del catálogo están protegidos para reutilización infinita.');
                return $this->redirect(['site/almacenamiento-listar']);
            }
            
            $marca = $almacenamiento->MARCA ?? 'Sin marca';
            $modelo = $almacenamiento->MODELO ?? 'Sin modelo';
            
            // Eliminar el dispositivo
            if ($almacenamiento->delete()) {
                Yii::$app->session->setFlash('success', "Dispositivo de almacenamiento $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el dispositivo de almacenamiento');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/almacenamiento-listar']);
    }

    public function actionEliminarAlmacenamientoMasivo()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/almacenamiento-listar']);
        }

        $idsJson = $request->post('ids');
        
        if (empty($idsJson)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron dispositivos para eliminar');
            return $this->redirect(['site/almacenamiento-listar']);
        }

        $ids = json_decode($idsJson, true);
        
        if (!is_array($ids) || empty($ids)) {
            Yii::$app->session->setFlash('error', 'Datos de selección inválidos');
            return $this->redirect(['site/almacenamiento-listar']);
        }

        try {
            $eliminados = 0;
            $errores = [];
            $protegidos = 0;

            foreach ($ids as $id) {
                if (empty($id)) continue;
                
                $almacenamiento = Almacenamiento::findOne(['idAlmacenamiento' => $id]);
                
                if (!$almacenamiento) {
                    $errores[] = "Dispositivo de almacenamiento con ID $id no encontrado";
                    continue;
                }

                // Verificar si es un elemento de catálogo y protegerlo
                if (strpos($almacenamiento->ubicacion_detalle, 'Catálogo') !== false) {
                    $protegidos++;
                    continue;
                }

                if ($almacenamiento->delete()) {
                    $eliminados++;
                } else {
                    $errores[] = "Error al eliminar dispositivo de almacenamiento ID $id";
                }
            }

            // Construir mensaje informativo
            $messages = [];
            if ($eliminados > 0) {
                $messages[] = "Se eliminaron $eliminados dispositivos de almacenamiento exitosamente";
            }
            if ($protegidos > 0) {
                $messages[] = "$protegidos dispositivos de almacenamiento del catálogo fueron protegidos (no se eliminan)";
            }
            if (count($errores) > 0) {
                $messages[] = "Errores: " . implode(', ', array_slice($errores, 0, 3));
                if (count($errores) > 3) {
                    $messages[count($messages)-1] .= " y " . (count($errores) - 3) . " más";
                }
            }

            if (!empty($messages)) {
                $finalMessage = implode('. ', $messages);
                if ($eliminados > 0 || $protegidos > 0) {
                    Yii::$app->session->setFlash('success', $finalMessage);
                } else {
                    Yii::$app->session->setFlash('error', $finalMessage);
                }
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo procesar ningún dispositivo de almacenamiento');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al procesar eliminación: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/almacenamiento-listar']);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO FUENTES DE PODER ====================
    public function actionFuentesCatalogoListar()
    {
        try {
            // Obtener solo fuentes de poder de catálogo
            $fuentes = FuentesDePoder::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $fuentes = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/fuentes-listar', [
            'fuentes' => $fuentes,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO MONITOR ====================
    public function actionMonitorCatalogoListar()
    {
        try {
            // Obtener solo monitores de catálogo
            $monitores = Monitor::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $monitores = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/monitor-listar', [
            'monitores' => $monitores,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO NO BREAK ====================
    public function actionNobreakCatalogoListar()
    {
        try {
            // Obtener solo No Break de catálogo
            $nobreaks = Nobreak::find()
                ->where(['!=', 'Estado', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('Estado ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $nobreaks = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/nobreak-listar', [
            'nobreaks' => $nobreaks,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO BATERÍAS ====================
    public function actionBateriasCatalogoListar()
    {
        try {
            // Obtener solo baterías de catálogo
            $baterias = Bateria::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $baterias = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/baterias-listar', [
            'baterias' => $baterias,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO EQUIPO DE SONIDO ====================
    public function actionSonidoCatalogoListar()
    {
        try {
            // Obtener solo equipos de sonido de catálogo
            $sonidos = Sonido::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $sonidos = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/sonido-listar', [
            'sonidos' => $sonidos,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO CONECTIVIDAD ====================
    public function actionConectividadCatalogoListar()
    {
        try {
            // Obtener solo equipos de conectividad de catálogo
            $conectividades = Conectividad::find()
                ->where(['!=', 'Estado', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('Estado ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $conectividades = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/conectividad-listar', [
            'conectividades' => $conectividades,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO TELEFONÍA ====================
    public function actionTelefoniaCatalogoListar()
    {
        try {
            // Obtener solo equipos de telefonía de catálogo
            $telefonias = Telefonia::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $telefonias = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/telefonia-listar', [
            'telefonias' => $telefonias,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO VIDEO VIGILANCIA ====================
    public function actionVideovigilanciaCatalogoListar()
    {
        try {
            // Obtener solo equipos de video vigilancia de catálogo
            $videovigilancias = VideoVigilancia::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $videovigilancias = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/videovigilancia-listar', [
            'videovigilancias' => $videovigilancias,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO IMPRESORAS ====================
    public function actionImpresorasCatalogoListar()
    {
        try {
            // Obtener solo impresoras de catálogo
            $impresoras = Impresora::find()
                ->where(['!=', 'Estado', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('Estado ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $impresoras = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/impresoras-listar', [
            'impresoras' => $impresoras,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO ADAPTADORES ====================
    public function actionAdaptadoresCatalogoListar()
    {
        try {
            // Obtener solo adaptadores de catálogo
            $adaptadores = Adaptador::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $adaptadores = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/adaptadores-listar', [
            'adaptadores' => $adaptadores,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES PARA CATÁLOGO EQUIPOS DE CÓMPUTO ====================
    public function actionEquiposCatalogoListar()
    {
        try {
            // Obtener solo equipos de cómputo de catálogo
            $equipos = Equipo::find()
                ->where(['!=', 'Estado', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('Estado ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $equipos = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/equipos-listar', [
            'equipos' => $equipos,
            'error' => $error
        ]);
    }
    
    // ==================== ACCIONES DE ELIMINACIÓN PARA FUENTES DE PODER ====================
    public function actionFuenteEliminar()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/fuentes-catalogo-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/fuentes-catalogo-listar']);
        }

        try {
            $fuente = FuentesDePoder::findOne(['idFuentePoder' => $id]);
            
            if (!$fuente) {
                Yii::$app->session->setFlash('error', "Fuente de poder con ID $id no encontrada");
                return $this->redirect(['site/fuentes-catalogo-listar']);
            }
            
            // PROTECCIÓN: No permitir eliminar items del catálogo
            if (!empty($fuente->ubicacion_detalle) && stripos($fuente->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se pueden eliminar items del catálogo. Los items del catálogo son reutilizables infinitamente.');
                return $this->redirect(['site/fuentes-catalogo-listar']);
            }
            
            $marca = $fuente->MARCA ?? 'Sin marca';
            $modelo = $fuente->MODELO ?? 'Sin modelo';
            
            // Eliminar la fuente
            if ($fuente->delete()) {
                Yii::$app->session->setFlash('success', "Fuente de poder $marca $modelo eliminada exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar la fuente de poder');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/fuentes-catalogo-listar']);
    }

    public function actionFuenteEliminarMultiple()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/fuentes-catalogo-listar']);
        }

        $ids = $request->post('ids');
        
        if (!$ids || !is_array($ids) || empty($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron fuentes para eliminar');
            return $this->redirect(['site/fuentes-catalogo-listar']);
        }

        try {
            $eliminados = 0;
            $errores = [];

            foreach ($ids as $id) {
                if (empty($id)) continue;
                
                $fuente = FuentesDePoder::findOne(['idFuentePoder' => $id]);
                
                if (!$fuente) {
                    $errores[] = "Fuente con ID $id no encontrada";
                    continue;
                }

                if ($fuente->delete()) {
                    $eliminados++;
                } else {
                    $errores[] = "Error al eliminar fuente ID $id";
                }
            }

            if ($eliminados > 0) {
                $message = "Se eliminaron $eliminados fuentes exitosamente";
                if (count($errores) > 0) {
                    $message .= ". Algunos errores: " . implode(', ', $errores);
                }
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo eliminar ninguna fuente. ' . implode(', ', $errores));
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al procesar eliminación: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/fuentes-catalogo-listar']);
    }
    
    // ==================== ACCIONES DE ELIMINACIÓN PARA MONITORES ====================
    public function actionMonitorEliminar()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/monitor-catalogo-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/monitor-catalogo-listar']);
        }

        try {
            $monitor = Monitor::findOne(['idMonitor' => $id]);
            
            if (!$monitor) {
                Yii::$app->session->setFlash('error', "Monitor con ID $id no encontrado");
                return $this->redirect(['site/monitor-catalogo-listar']);
            }
            
            // PROTECCIÓN: No permitir eliminar items del catálogo
            if (!empty($monitor->ubicacion_detalle) && stripos($monitor->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se pueden eliminar items del catálogo. Los items del catálogo son reutilizables infinitamente.');
                return $this->redirect(['site/monitor-catalogo-listar']);
            }
            
            $marca = $monitor->MARCA ?? 'Sin marca';
            $modelo = $monitor->MODELO ?? 'Sin modelo';
            
            // Eliminar el monitor
            if ($monitor->delete()) {
                Yii::$app->session->setFlash('success', "Monitor $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el monitor');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/monitor-catalogo-listar']);
    }

    // Acciones específicas para eliminación desde el listado de monitores
    public function actionEliminarMonitor()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/monitor-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['site/monitor-listar']);
        }

        try {
            $monitor = Monitor::findOne(['idMonitor' => $id]);
            
            if (!$monitor) {
                Yii::$app->session->setFlash('error', "Monitor con ID $id no encontrado");
                return $this->redirect(['site/monitor-listar']);
            }
            
            // Verificar si es un elemento de catálogo y protegerlo
            if (strpos($monitor->ubicacion_detalle, 'Catálogo') !== false) {
                Yii::$app->session->setFlash('error', 'No se puede eliminar un monitor del catálogo. Los elementos del catálogo están protegidos para reutilización infinita.');
                return $this->redirect(['site/monitor-listar']);
            }
            
            $marca = $monitor->MARCA ?? 'Sin marca';
            $modelo = $monitor->MODELO ?? 'Sin modelo';
            
            // Eliminar el monitor
            if ($monitor->delete()) {
                Yii::$app->session->setFlash('success', "Monitor $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el monitor');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/monitor-listar']);
    }

    public function actionEliminarMonitoresMasivo()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['site/monitor-listar']);
        }

        $idsJson = $request->post('ids');
        
        if (empty($idsJson)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron monitores para eliminar');
            return $this->redirect(['site/monitor-listar']);
        }

        $ids = json_decode($idsJson, true);
        
        if (!is_array($ids) || empty($ids)) {
            Yii::$app->session->setFlash('error', 'Datos de selección inválidos');
            return $this->redirect(['site/monitor-listar']);
        }

        try {
            $eliminados = 0;
            $errores = [];
            $protegidos = 0;

            foreach ($ids as $id) {
                if (empty($id)) continue;
                
                $monitor = Monitor::findOne(['idMonitor' => $id]);
                
                if (!$monitor) {
                    $errores[] = "Monitor con ID $id no encontrado";
                    continue;
                }

                // Verificar si es un elemento de catálogo y protegerlo
                if (strpos($monitor->ubicacion_detalle, 'Catálogo') !== false) {
                    $protegidos++;
                    continue;
                }

                if ($monitor->delete()) {
                    $eliminados++;
                } else {
                    $errores[] = "Error al eliminar monitor ID $id";
                }
            }

            // Construir mensaje informativo
            $messages = [];
            if ($eliminados > 0) {
                $messages[] = "Se eliminaron $eliminados monitores exitosamente";
            }
            if ($protegidos > 0) {
                $messages[] = "$protegidos monitores del catálogo fueron protegidos (no se eliminan)";
            }
            if (count($errores) > 0) {
                $messages[] = "Errores: " . implode(', ', array_slice($errores, 0, 3));
                if (count($errores) > 3) {
                    $messages[count($messages)-1] .= " y " . (count($errores) - 3) . " más";
                }
            }

            if (!empty($messages)) {
                $finalMessage = implode('. ', $messages);
                if ($eliminados > 0 || $protegidos > 0) {
                    Yii::$app->session->setFlash('success', $finalMessage);
                } else {
                    Yii::$app->session->setFlash('error', $finalMessage);
                }
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo procesar ningún monitor');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al procesar eliminación: ' . $e->getMessage());
        }
        
        return $this->redirect(['site/monitor-listar']);
    }
    
    // ==================== ACCIONES PARA EQUIPO DE SONIDO ====================
    public function actionEquipoDeSonido()
    {
        $model = new Sonido();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo de sonido agregado exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['sonido-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('equipo-de-sonido', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionSonidoListar()
    {
        try {
            $sonidos = Sonido::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idSonido ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $sonidos = [];
            $error = $e->getMessage();
        }

        return $this->render('sonido/listar', [
            'sonidos' => $sonidos,
            'error' => $error
        ]);
    }
    
    public function actionSonidoVer($id)
    {
        $model = Sonido::findOne(['idSonido' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El equipo de sonido no existe.');
        }
        return $this->render('sonido/ver', ['model' => $model]);
    }

    public function actionSonidoEliminarMultiple()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }
        
        // Obtener datos JSON del body
        $rawBody = $request->getRawBody();
        $data = json_decode($rawBody, true);
        $ids = $data['ids'] ?? $request->post('ids');
        
        if (!$ids || !is_array($ids)) {
            return ['success' => false, 'message' => 'No se seleccionaron equipos de sonido'];
        }
        
        $eliminados = 0;
        $catalogoEncontrado = false;
        foreach ($ids as $id) {
            $model = Sonido::findOne(['idSonido' => $id]);
            if ($model) {
                // PROTECCIÓN: No permitir eliminar items del catálogo
                if (!empty($model->ubicacion_detalle) && stripos($model->ubicacion_detalle, 'Catálogo') !== false) {
                    $catalogoEncontrado = true;
                    continue;
                }
                if ($model->delete()) {
                    $eliminados++;
                }
            }
        }
        
        $mensaje = "Se eliminaron $eliminados equipo(s) de sonido";
        if ($catalogoEncontrado) {
            $mensaje .= '. Se omitieron items del catálogo (no se pueden eliminar)';
        }
        
        return ['success' => true, 'message' => $mensaje];
    }

    public function actionSonidoEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de equipo de sonido no proporcionado.');
            return $this->redirect(['sonido-listar']);
        }

        $model = Sonido::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Equipo de sonido no encontrado.');
            return $this->redirect(['sonido-listar']);
        }

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            // Remover campos de auditoría del POST para evitar conflictos
            if (isset($postData['Sonido']['fecha_creacion'])) {
                unset($postData['Sonido']['fecha_creacion']);
            }
            if (isset($postData['Sonido']['fecha_ultima_edicion'])) {
                unset($postData['Sonido']['fecha_ultima_edicion']);
            }
            
            if ($model->load($postData) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo de sonido actualizado exitosamente.');
                return $this->redirect(['sonido-listar']);
            } else {
                // Mostrar errores específicos de validación
                $errors = [];
                foreach ($model->getErrors() as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errors[] = "$field: $error";
                    }
                }
                $errorMessage = empty($errors) ? 'Error desconocido al actualizar el equipo de sonido.' : 'Errores de validación: ' . implode('; ', $errors);
                Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        return $this->render('sonido/editar', ['model' => $model]);
    }
    
    // ==================== ACCIONES PARA PROCESADORES ====================
    public function actionProcesadores()
    {
        $model = new Procesador();
        
        // Verificar si viene desde el formulario de equipo (modo simplificado)
        $modoSimplificado = Yii::$app->request->get('simple', false) || 
                           (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'computo') !== false);
        
        if ($model->load(Yii::$app->request->post())) {
            if ($modoSimplificado) {
                // Establecer escenario simplificado ANTES de cargar los datos
                $model->scenario = 'simplificado';
                
                // Solo procesar MARCA y MODELO del POST
                $postData = Yii::$app->request->post('Procesador', []);
                if (isset($postData['MARCA'])) $model->MARCA = $postData['MARCA'];
                if (isset($postData['MODELO'])) $model->MODELO = $postData['MODELO'];
                
                // CATÁLOGO: Solo guardar marca y modelo, sin datos adicionales
                $model->Estado = 'Activo';
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'A';
                $model->ubicacion_detalle = 'Catálogo';
                $model->DESCRIPCION = 'Entrada de catálogo';
                
                // Campos técnicos con valores mínimos para catálogo (evitar NULL)
                $timestamp = time() . rand(100, 999);
                $model->NUMERO_SERIE = 'CAT-' . $timestamp;
                $model->NUMERO_INVENTARIO = 'CAT-' . $timestamp;
                $model->FRECUENCIA_BASE = 'No especificada';
                $model->NUCLEOS = 0; // Usar 0 en lugar de NULL para indicar "no especificado"
                $model->HILOS = 0;   // Usar 0 en lugar de NULL para indicar "no especificado"
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Procesador agregado al catálogo exitosamente.');
                    return $this->refresh();
                }
            } else {
                // Modo normal - validación completa
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Procesador agregado exitosamente.');
                    return $this->refresh();
                }
            }
        }
        
        return $this->render('procesadores', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionProcesadorListar()
    {
        try {
            $procesadores = Procesador::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idProcesador ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $procesadores = [];
            $error = $e->getMessage();
        }

        return $this->render('procesador/listar', [
            'procesadores' => $procesadores,
            'error' => $error
        ]);
    }

    public function actionProcesadorVer($id)
    {
        $model = Procesador::findOne(['idProcesador' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El procesador no existe.');
        }
        return $this->render('procesador/ver', ['model' => $model]);
    }

    public function actionCatalogoListar()
    {
        try {
            // Obtener solo procesadores de catálogo (FRECUENCIA_BASE = 'No especificada')
            $procesadores = Procesador::find()
                ->where(['FRECUENCIA_BASE' => 'No especificada'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $procesadores = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/listar', [
            'procesadores' => $procesadores,
            'error' => $error
        ]);
    }

    public function actionAlmacenamientoCatalogoListar()
    {
        try {
            // Obtener solo almacenamiento de catálogo
            $almacenamiento = Almacenamiento::find()
                ->where(['!=', 'ESTADO', 'BAJA'])
                ->andWhere(['like', 'ubicacion_detalle', 'Catálogo'])
                ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
                ->all();
            $error = null;
        } catch (Exception $e) {
            $almacenamiento = [];
            $error = $e->getMessage();
        }

        return $this->render('catalogo/almacenamiento-listar', [
            'almacenamiento' => $almacenamiento,
            'error' => $error
        ]);
    }
    
    public function actionProcesadorEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de procesador requerido.');
            return $this->redirect(['procesador-listar']);
        }

        $model = Procesador::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Procesador no encontrado.');
            return $this->redirect(['procesador-listar']);
        }

        if ($model->load(Yii::$app->request->post())) {
            // Verificar si es un procesador de catálogo
            $esCatalogo = ($model->FRECUENCIA_BASE == 'No especificada' && 
                          strpos($model->ubicacion_detalle, 'Catálogo') !== false);
            
            if ($esCatalogo) {
                // Para procesadores de catálogo, solo permitir editar MARCA y MODELO
                $allowedFields = ['MARCA', 'MODELO'];
                $postData = Yii::$app->request->post('Procesador', []);
                foreach ($postData as $field => $value) {
                    if (in_array($field, $allowedFields)) {
                        $model->$field = $value;
                    }
                }
                
                $model->scenario = 'simplificado'; // Usar el escenario de catálogo
            } else {
                // Para procesadores completos, permitir editar todos los campos
                $allowedFields = [
                    'MARCA', 'MODELO', 'FRECUENCIA_BASE', 'NUCLEOS', 'HILOS',
                    'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'DESCRIPCION', 'Estado',
                    'fecha', 'ubicacion_edificio', 'ubicacion_detalle'
                ];
                
                $postData = Yii::$app->request->post('Procesador', []);
                foreach ($postData as $field => $value) {
                    if (in_array($field, $allowedFields)) {
                        $model->$field = $value;
                    }
                }
            }

            if ($model->save()) {
                $mensaje = $esCatalogo ? 'Procesador de catálogo actualizado exitosamente.' : 'Procesador actualizado exitosamente.';
                Yii::$app->session->setFlash('success', $mensaje);
                
                // Si es de catálogo, redirigir al listado de catálogos
                $redireccion = $esCatalogo ? ['catalogo-listar'] : ['procesador-listar'];
                return $this->redirect($redireccion);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar el procesador: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        return $this->render('procesador/editar', [
            'model' => $model,
        ]);
    }
    
    // ==================== ACCIONES PARA FUENTES DE PODER ====================
    public function actionFuentesDePoder()
    {
        $model = new FuentesDePoder();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            if ($modoSimplificado) {
                // Establecer escenario simplificado ANTES de procesar
                $model->scenario = 'simplificado';
                
                // Solo procesar MARCA y MODELO del POST
                $postData = Yii::$app->request->post('FuentesDePoder', []);
                if (isset($postData['MARCA'])) $model->MARCA = $postData['MARCA'];
                if (isset($postData['MODELO'])) $model->MODELO = $postData['MODELO'];
                
                // Establecer ubicacion_detalle como Catálogo automáticamente
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                
                // NO asignar otros campos - dejar que se manejen automáticamente
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Fuente de poder agregada al catálogo exitosamente.');
                    return $this->redirect(['fuentes-catalogo-listar']);
                }
            } else {
                // Modo normal - validación completa
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Fuente de poder agregada exitosamente.');
                    return $this->redirect(['fuentes-de-poder']);
                }
            }
        }

        return $this->render('fuentes-de-poder', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    // ==================== ACCIONES PARA CONECTIVIDAD ====================
    public function actionConectividad()
    {
        $model = new Conectividad();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->Estado = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo de conectividad agregado exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['conectividad-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('conectividad', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionConectividadListar()
    {
        try {
            $conectividades = Conectividad::find()->where(['!=', 'Estado', 'BAJA'])->orderBy('idCONECTIVIDAD ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $conectividades = [];
            $error = $e->getMessage();
        }

        return $this->render('conectividad/listar', [
            'conectividades' => $conectividades,
            'error' => $error
        ]);
    }
    
    public function actionConectividadVer($id)
    {
        $model = Conectividad::findOne(['idCONECTIVIDAD' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El equipo de conectividad no existe.');
        }
        return $this->render('conectividad/ver', ['model' => $model]);
    }

    public function actionConectividadEliminarMultiple()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }
        
        // Obtener datos JSON del body
        $rawBody = $request->getRawBody();
        $data = json_decode($rawBody, true);
        $ids = $data['ids'] ?? $request->post('ids');
        
        if (!$ids || !is_array($ids)) {
            return ['success' => false, 'message' => 'No se seleccionaron equipos de conectividad'];
        }
        
        $eliminados = 0;
        $catalogoEncontrado = false;
        foreach ($ids as $id) {
            $model = Conectividad::findOne(['idCONECTIVIDAD' => $id]);
            if ($model) {
                // PROTECCIÓN: No permitir eliminar items del catálogo
                if (!empty($model->ubicacion_detalle) && stripos($model->ubicacion_detalle, 'Catálogo') !== false) {
                    $catalogoEncontrado = true;
                    continue;
                }
                if ($model->delete()) {
                    $eliminados++;
                }
            }
        }
        
        $mensaje = "Se eliminaron $eliminados equipo(s) de conectividad";
        if ($catalogoEncontrado) {
            $mensaje .= '. Se omitieron items del catálogo (no se pueden eliminar)';
        }
        
        return ['success' => true, 'message' => $mensaje];
    }

    public function actionConectividadEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de conectividad requerido.');
            return $this->redirect(['conectividad-listar']);
        }

        $model = Conectividad::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Equipo de conectividad no encontrado.');
            return $this->redirect(['conectividad-listar']);
        }

        if ($model->load(Yii::$app->request->post())) {
            // Filtrar solo los campos que queremos actualizar
            $allowedFields = [
                'TIPO', 'MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO',
                'CANTIDAD_PUERTOS', 'DESCRIPCION', 'Estado', 'fecha',
                'ubicacion_edificio', 'ubicacion_detalle'
            ];
            
            $postData = Yii::$app->request->post('Conectividad', []);
            foreach ($postData as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $model->$field = $value;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo de conectividad actualizado exitosamente.');
                return $this->redirect(['conectividad-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar el equipo: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        return $this->render('conectividad/editar', [
            'model' => $model,
        ]);
    }
    
    // ==================== ACCIONES PARA TELEFONÍA ====================
    public function actionTelefonia()
    {
        $model = new Telefonia();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo de telefonía agregado exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['telefonia-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('telefonia', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionTelefoniaListar()
    {
        try {
            $telefonias = Telefonia::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idTELEFONIA ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $telefonias = [];
            $error = $e->getMessage();
        }

        return $this->render('telefonia/listar', [
            'telefonias' => $telefonias,
            'error' => $error
        ]);
    }
    
    public function actionTelefoniaVer($id)
    {
        $model = Telefonia::findOne(['idTELEFONIA' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El equipo de telefonía no existe.');
        }
        return $this->render('telefonia/ver', ['model' => $model]);
    }

    public function actionTelefoniaEliminarMultiple()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            return $this->redirect(['site/telefonia-listar']);
        }
        $ids = $request->post('ids');
        if (!$ids || !is_array($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron equipos de telefonía');
            return $this->redirect(['site/telefonia-listar']);
        }
        $eliminados = 0;
        $catalogoEncontrado = false;
        foreach ($ids as $id) {
            $model = Telefonia::findOne(['idTELEFONIA' => $id]);
            if ($model) {
                // PROTECCIÓN: No permitir eliminar items del catálogo
                if (!empty($model->ubicacion_detalle) && stripos($model->ubicacion_detalle, 'Catálogo') !== false) {
                    $catalogoEncontrado = true;
                    continue;
                }
                if ($model->delete()) {
                    $eliminados++;
                }
            }
        }
        if ($catalogoEncontrado) {
            Yii::$app->session->setFlash('warning', 'Se omitieron items del catálogo. Los items del catálogo no se pueden eliminar.');
        }
        if ($eliminados > 0) {
            Yii::$app->session->setFlash('success', "Se eliminaron $eliminados equipo(s) de telefonía");
        }
        return $this->redirect(['site/telefonia-listar']);
    }

    public function actionTelefoniaEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de telefonía requerido.');
            return $this->redirect(['telefonia-listar']);
        }

        $model = Telefonia::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Equipo de telefonía no encontrado.');
            return $this->redirect(['telefonia-listar']);
        }

        if ($model->load(Yii::$app->request->post())) {
            // Filtrar solo los campos que queremos actualizar
            $allowedFields = [
                'MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO',
                'EDIFICIO', 'ESTADO', 'EMISION_INVENTARIO', 'fecha',
                'ubicacion_edificio', 'ubicacion_detalle'
            ];
            
            $postData = Yii::$app->request->post('Telefonia', []);
            foreach ($postData as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $model->$field = $value;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Equipo de telefonía actualizado exitosamente.');
                return $this->redirect(['telefonia-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar el equipo: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        return $this->render('telefonia/editar', [
            'model' => $model,
        ]);
    }
    
    // ==================== ACCIONES PARA CÁMARAS/VIDEO VIGILANCIA ====================
    public function actionCamaras()
    {
        $model = new VideoVigilancia();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Cámara agregada exitosamente.');
            return $this->refresh();
        }
        
        return $this->render('camaras', [
            'model' => $model,
        ]);
    }
    
    public function actionVideovigilanciaListar()
    {
        try {
            $camaras = VideoVigilancia::find()->where(['!=', 'ESTADO', 'BAJA'])->orderBy('idVIDEO_VIGILANCIA ASC')->all();
            $error = null;
        } catch (Exception $e) {
            $camaras = [];
            $error = $e->getMessage();
        }

        return $this->render('videovigilancia/listar', [
            'camaras' => $camaras,
            'error' => $error
        ]);
    }
    
    public function actionVideovigilanciaVer($id)
    {
        $model = VideoVigilancia::findOne(['idVIDEO_VIGILANCIA' => $id]);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El equipo de video vigilancia no existe.');
        }
        return $this->render('videovigilancia/ver', ['model' => $model]);
    }

    public function actionVideovigilanciaEliminarMultiple()
    {
        $request = Yii::$app->request;
        if (!$request->isPost) {
            return $this->redirect(['site/videovigilancia-listar']);
        }
        $ids = $request->post('ids');
        if (!$ids || !is_array($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron equipos de video vigilancia');
            return $this->redirect(['site/videovigilancia-listar']);
        }
        $eliminados = 0;
        $catalogoEncontrado = false;
        foreach ($ids as $id) {
            $model = VideoVigilancia::findOne(['idVIDEO_VIGILANCIA' => $id]);
            if ($model) {
                // PROTECCIÓN: No permitir eliminar items del catálogo
                if (!empty($model->ubicacion_detalle) && stripos($model->ubicacion_detalle, 'Catálogo') !== false) {
                    $catalogoEncontrado = true;
                    continue;
                }
                if ($model->delete()) {
                    $eliminados++;
                }
            }
        }
        if ($catalogoEncontrado) {
            Yii::$app->session->setFlash('warning', 'Se omitieron items del catálogo. Los items del catálogo no se pueden eliminar.');
        }
        if ($eliminados > 0) {
            Yii::$app->session->setFlash('success', "Se eliminaron $eliminados equipo(s) de video vigilancia");
        }
        return $this->redirect(['site/videovigilancia-listar']);
    }

    public function actionVideovigilancia()
    {
        $model = new VideoVigilancia();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->RESOLUCION = $model->RESOLUCION ?: 'N/A';
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Cámara de videovigilancia agregada exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['videovigilancia-catalogo-listar']);
                }
                return $this->redirect(['videovigilancia-listar']);
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('videovigilancia/editar', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }

    public function actionVideovigilanciaEditar($id = null)
    {
        if (!$id) {
            Yii::$app->session->setFlash('error', 'ID de cámara requerido.');
            return $this->redirect(['videovigilancia-listar']);
        }

        $model = VideoVigilancia::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Cámara de videovigilancia no encontrada.');
            return $this->redirect(['videovigilancia-listar']);
        }

        if ($model->load(Yii::$app->request->post())) {
            // Filtrar solo los campos que queremos actualizar
            $allowedFields = [
                'MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'DESCRIPCION',
                'tipo_camara', 'EDIFICIO', 'ESTADO', 'fecha', 'ubicacion_edificio', 
                'ubicacion_detalle', 'EMISION_INVENTARIO', 'VIDEO_VIGILANCIA_COL'
            ];
            
            $postData = Yii::$app->request->post('VideoVigilancia', []);
            foreach ($postData as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $model->$field = $value;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Cámara de videovigilancia actualizada exitosamente.');
                return $this->redirect(['videovigilancia-listar']);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar la cámara: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        return $this->render('videovigilancia/editar', [
            'model' => $model,
        ]);
    }
    
    // ==================== ACCIONES PARA COMPATIBILIDAD CON AGREGAR_NUEVO.PHP ====================
    public function actionMonitores()
    {
        $model = new Monitor();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TAMANIO = $model->TAMANIO ?: 'N/A';
                $model->RESOLUCION = $model->RESOLUCION ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Monitor agregado exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['site/monitor-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('monitores', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionMicrofonos()
    {
        $model = new Microfono();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Micrófono agregado exitosamente.');
            return $this->refresh();
        }
        
        return $this->render('microfonos', [
            'model' => $model,
        ]);
    }
    
    public function actionNoBreak()
    {
        $model = new Nobreak();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer escenario y valores por defecto
            if ($modoSimplificado) {
                $model->scenario = 'catalogo';
                $model->ubicacion_detalle = 'Catálogo';
                $model->Estado = 'Activo';
                // Valores por defecto para campos no requeridos en catálogo
                $timestamp = time() . rand(100, 999);
                $model->CAPACIDAD = $model->CAPACIDAD ?: 'N/A';
                $model->NUMERO_SERIE = 'CAT-' . $timestamp;
                $model->NUMERO_INVENTARIO = 'CAT-' . $timestamp;
                $model->DESCRIPCION = 'Item de catálogo';
                $model->EMISION_INVENTARIO = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'No Break agregado exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['nobreak-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                // Mostrar errores de validación
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error al guardar: ' . print_r($errors, true));
            }
        }
        
        return $this->render('no-break', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionComputo()
    {
        $model = new Equipo();
        
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Actualizar componentes asignados con información del equipo
                    $equipoNombre = $model->MARCA . ' ' . $model->MODELO . ' - ' . $model->NUM_INVENTARIO;
                    
                    // Actualizar procesador - No modificar si es de catálogo (reutilización infinita)
                    if (!empty($model->CPU_ID)) {
                        $procesador = \frontend\models\Procesador::findOne($model->CPU_ID);
                        if ($procesador) {
                            // Si es un procesador de catálogo, mantenerlo disponible para reutilización infinita
                            if ($procesador->FRECUENCIA_BASE == 'No especificada' && strpos($procesador->ubicacion_detalle, 'Catálogo') !== false) {
                                // Mantener estado de catálogo para reutilización infinita
                                Yii::info("Procesador de catálogo {$procesador->NUMERO_INVENTARIO} usado en equipo {$equipoNombre} - mantiene disponibilidad", 'app');
                            } else {
                                // Solo asignar procesador que NO es de catálogo
                                // Si ya estaba asignado a otro equipo, liberar la asignación anterior
                                if ($procesador->Estado == 'Activo' && strpos($procesador->ubicacion_detalle, 'Asignado a equipo:') !== false) {
                                    // Log de reasignación
                                    Yii::info("Reasignando procesador {$procesador->NUMERO_INVENTARIO} de '{$procesador->ubicacion_detalle}' a '{$equipoNombre}'", 'app');
                                }
                                
                                $procesador->Estado = 'Activo';
                                $procesador->ubicacion_detalle = 'Asignado a equipo: ' . $equipoNombre;
                                $procesador->save();
                            }
                            
                            // Actualizar descripción para compatibilidad
                            $model->CPU_DESC = $procesador->MARCA . ' ' . $procesador->MODELO . ' (' . $procesador->FRECUENCIA_BASE . ', ' . $procesador->NUCLEOS . ' núcleos)';
                        }
                    }
                    
                    // Actualizar almacenamiento - No modificar si es de catálogo (reutilización infinita)
                    if (!empty($model->DD_ID)) {
                        $almacenamiento = \frontend\models\Almacenamiento::findOne($model->DD_ID);
                        if ($almacenamiento) {
                            // Si es un almacenamiento de catálogo, mantenerlo disponible para reutilización infinita
                            if (strpos($almacenamiento->ubicacion_detalle, 'Catálogo') !== false) {
                                // Mantener estado de catálogo para reutilización infinita
                                Yii::info("Almacenamiento de catálogo {$almacenamiento->NUMERO_INVENTARIO} usado en equipo {$equipoNombre} - mantiene disponibilidad", 'app');
                            } else {
                                // Solo asignar almacenamiento que NO es de catálogo
                                if ($almacenamiento->ESTADO == 'Activo' && strpos($almacenamiento->ubicacion_detalle, 'Asignado a equipo:') !== false) {
                                    Yii::info("Reasignando almacenamiento {$almacenamiento->NUMERO_INVENTARIO} de '{$almacenamiento->ubicacion_detalle}' a '{$equipoNombre}'", 'app');
                                }
                                $almacenamiento->ESTADO = 'Activo';
                                $almacenamiento->ubicacion_detalle = 'Asignado a equipo: ' . $equipoNombre;
                                $almacenamiento->save();
                            }
                            
                            // Actualizar descripción para compatibilidad (siempre, independiente de si es catálogo)
                            $capacidad = $almacenamiento->CAPACIDAD ?: 'No esp.';
                            $tipo = $almacenamiento->TIPO ?: 'No esp.';
                            $model->DD_DESC = $almacenamiento->MARCA . ' ' . $almacenamiento->MODELO . ' (' . $capacidad . ' ' . $tipo . ')';
                        }
                    }
                    
                    // Actualizar memoria RAM - No modificar si es de catálogo (reutilización infinita)
                    if (!empty($model->RAM_ID)) {
                        $ram = \frontend\models\Ram::findOne($model->RAM_ID);
                        if ($ram) {
                            // Si es una RAM de catálogo, mantenerla disponible para reutilización infinita
                            if (strpos($ram->ubicacion_detalle, 'Catálogo') !== false) {
                                // Mantener estado de catálogo para reutilización infinita
                                $numero_inv = !empty($ram->numero_inventario) ? $ram->numero_inventario : 'Sin N/I';
                                Yii::info("RAM de catálogo {$numero_inv} usada en equipo {$equipoNombre} - mantiene disponibilidad", 'app');
                            } else {
                                // Solo asignar RAM que NO es de catálogo
                                // Si ya estaba asignado a otro equipo, liberar la asignación anterior
                                if ($ram->ESTADO == 'Activo' && strpos($ram->ubicacion_detalle, 'Asignado a equipo:') !== false) {
                                    // Log de reasignación  
                                    $numero_inv = !empty($ram->numero_inventario) ? $ram->numero_inventario : 'Sin N/I';
                                    Yii::info("Reasignando RAM {$numero_inv} de '{$ram->ubicacion_detalle}' a '{$equipoNombre}'", 'app');
                                }
                                
                                $ram->ESTADO = 'Activo';
                                $ram->ubicacion_detalle = 'Asignado a equipo: ' . $equipoNombre;
                                $ram->save();
                            }
                            
                            // Actualizar descripción para compatibilidad
                            $model->RAM_DESC = $ram->MARCA . ' ' . $ram->MODELO . ' (' . $ram->CAPACIDAD . ' ' . $ram->TIPO_DDR . ')';
                        }
                    }
                    
                    // Actualizar fuente de poder - No modificar si es de catálogo (reutilización infinita)
                    if (!empty($model->FUENTE_PODER)) {
                        $fuente = \frontend\models\FuentesDePoder::findOne($model->FUENTE_PODER);
                        if ($fuente) {
                            // Si es una fuente de catálogo, mantenerla disponible para reutilización infinita
                            if (strpos($fuente->ubicacion_detalle, 'Catálogo') !== false) {
                                // Mantener estado de catálogo para reutilización infinita
                                Yii::info("Fuente de poder de catálogo {$fuente->NUMERO_INVENTARIO} usada en equipo {$equipoNombre} - mantiene disponibilidad", 'app');
                            } else {
                                // Solo asignar fuente que NO es de catálogo
                                // Si ya estaba asignado a otro equipo, liberar la asignación anterior
                                if ($fuente->ESTADO == 'Activo' && strpos($fuente->ubicacion_detalle, 'Asignado a equipo:') !== false) {
                                    // Log de reasignación
                                    Yii::info("Reasignando fuente de poder {$fuente->NUMERO_INVENTARIO} de '{$fuente->ubicacion_detalle}' a '{$equipoNombre}'", 'app');
                                }
                                
                                $fuente->ESTADO = 'Activo';
                                $fuente->ubicacion_detalle = 'Asignado a equipo: ' . $equipoNombre;
                                $fuente->save();
                            }
                        }
                    }
                    
                    // Actualizar monitor - No modificar si es de catálogo (reutilización infinita)
                    if (!empty($model->MONITOR_ID)) {
                        $monitor = \frontend\models\Monitor::findOne($model->MONITOR_ID);
                        if ($monitor) {
                            // Si es un monitor de catálogo, mantenerlo disponible para reutilización infinita
                            if (strpos($monitor->ubicacion_detalle, 'Catálogo') !== false) {
                                // Mantener estado de catálogo para reutilización infinita
                                Yii::info("Monitor de catálogo {$monitor->NUMERO_INVENTARIO} usado en equipo {$equipoNombre} - mantiene disponibilidad", 'app');
                            } else {
                                // Solo asignar monitores que NO son de catálogo
                                if ($monitor->ESTADO == 'Activo' && strpos($monitor->ubicacion_detalle, 'Asignado a equipo:') !== false) {
                                    Yii::info("Reasignando monitor {$monitor->NUMERO_INVENTARIO} de '{$monitor->ubicacion_detalle}' a '{$equipoNombre}'", 'app');
                                }
                                $monitor->ESTADO = 'Activo';
                                $monitor->ubicacion_detalle = 'Asignado a equipo: ' . $equipoNombre;
                                $monitor->save();
                            }
                        }
                    }
                    
                    // Actualizar componentes adicionales de almacenamiento
                    $almacenamientoIds = ['DD2_ID', 'DD3_ID', 'DD4_ID'];
                    $almacenamientoFields = ['DD2', 'DD3', 'DD4'];
                    
                    for ($i = 0; $i < count($almacenamientoIds); $i++) {
                        $idField = $almacenamientoIds[$i];
                        $descField = $almacenamientoFields[$i];
                        
                        if (!empty($model->$idField)) {
                            $almacenamiento = \frontend\models\Almacenamiento::findOne($model->$idField);
                            if ($almacenamiento) {
                                // Si es un almacenamiento de catálogo, mantenerlo disponible para reutilización infinita
                                if (strpos($almacenamiento->ubicacion_detalle, 'Catálogo') !== false) {
                                    // Mantener estado de catálogo para reutilización infinita
                                    Yii::info("Almacenamiento adicional de catálogo {$almacenamiento->NUMERO_INVENTARIO} usado en equipo {$equipoNombre} - mantiene disponibilidad", 'app');
                                } else {
                                    // Solo asignar almacenamiento que NO es de catálogo
                                    if ($almacenamiento->ESTADO == 'Activo' && strpos($almacenamiento->ubicacion_detalle, 'Asignado a equipo:') !== false) {
                                        Yii::info("Reasignando almacenamiento {$almacenamiento->NUMERO_INVENTARIO} de '{$almacenamiento->ubicacion_detalle}' a '{$equipoNombre}'", 'app');
                                    }
                                    $almacenamiento->ESTADO = 'Activo';
                                    $almacenamiento->ubicacion_detalle = 'Asignado a equipo: ' . $equipoNombre;
                                    $almacenamiento->save();
                                }
                                
                                // Actualizar campo de descripción para compatibilidad (siempre, independiente de si es catálogo)
                                $capacidad = $almacenamiento->CAPACIDAD ?: 'No esp.';
                                $tipo = $almacenamiento->TIPO ?: 'No esp.';
                                $model->$descField = $almacenamiento->MARCA . ' ' . $almacenamiento->MODELO . ' (' . $capacidad . ' ' . $tipo . ')';
                            }
                        }
                    }
                    
                    // Actualizar componentes adicionales de RAM - No modificar si es de catálogo (reutilización infinita)
                    $ramIds = ['RAM2_ID', 'RAM3_ID', 'RAM4_ID'];
                    $ramFields = ['RAM2', 'RAM3', 'RAM4'];
                    
                    for ($i = 0; $i < count($ramIds); $i++) {
                        $idField = $ramIds[$i];
                        $descField = $ramFields[$i];
                        
                        if (!empty($model->$idField)) {
                            $ram = \frontend\models\Ram::findOne($model->$idField);
                            if ($ram) {
                                // Si es una RAM de catálogo, mantenerla disponible para reutilización infinita
                                if (strpos($ram->ubicacion_detalle, 'Catálogo') !== false) {
                                    // Mantener estado de catálogo para reutilización infinita
                                    $numero_inv = !empty($ram->numero_inventario) ? $ram->numero_inventario : 'Sin N/I';
                                    Yii::info("RAM adicional de catálogo {$numero_inv} usada en equipo {$equipoNombre} - mantiene disponibilidad", 'app');
                                } else {
                                    // Solo asignar RAM que NO es de catálogo
                                    if ($ram->ESTADO == 'Activo' && strpos($ram->ubicacion_detalle, 'Asignado a equipo:') !== false) {
                                        $numero_inv = !empty($ram->numero_inventario) ? $ram->numero_inventario : 'Sin N/I';
                                        Yii::info("Reasignando RAM {$numero_inv} de '{$ram->ubicacion_detalle}' a '{$equipoNombre}'", 'app');
                                    }
                                    
                                    $ram->ESTADO = 'Activo';
                                    $ram->ubicacion_detalle = 'Asignado a equipo: ' . $equipoNombre;
                                    $ram->save();
                                }
                                
                                // Actualizar campo de descripción para compatibilidad
                                $model->$descField = $ram->MARCA . ' ' . $ram->MODELO . ' (' . $ram->CAPACIDAD . ' ' . $ram->TIPO_DDR . ')';
                            }
                        }
                    }
                    
                    // Actualizar campos legacy para compatibilidad con las vistas
                    if (!empty($model->CPU_DESC)) {
                        $model->CPU = $model->CPU_DESC;
                    }
                    if (!empty($model->DD_DESC)) {
                        $model->DD = $model->DD_DESC;
                    }
                    if (!empty($model->RAM_DESC)) {
                        $model->RAM = $model->RAM_DESC;
                    }
                    
                    // Guardar las descripciones actualizadas
                    $model->save();
                    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Equipo de cómputo agregado exitosamente.');
                    return $this->refresh();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error al guardar el equipo: ' . $e->getMessage());
            }
        }
        
        // Obtener solo procesadores de catálogo (los que tienen FRECUENCIA_BASE = 'No especificada')
        $procesadores = \frontend\models\Procesador::find()
            ->where(['!=', 'Estado', 'BAJA'])
            ->andWhere(['FRECUENCIA_BASE' => 'No especificada']) // Solo procesadores de catálogo
            ->orderBy('Estado ASC, MARCA ASC, MODELO ASC')
            ->all();
        
        $memoriaRam = \frontend\models\Ram::find()
            ->where(['!=', 'ESTADO', 'BAJA'])
            ->andWhere(['like', 'ubicacion_detalle', 'Catálogo']) // Solo RAM de catálogo
            ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
            ->all();
            
        $almacenamiento = \frontend\models\Almacenamiento::find()
            ->where(['!=', 'ESTADO', 'BAJA'])
            ->andWhere(['like', 'ubicacion_detalle', 'Catálogo']) // Solo almacenamiento de catálogo
            ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
            ->all();
            
        $fuentesPoder = \frontend\models\FuentesDePoder::find()
            ->where(['!=', 'ESTADO', 'BAJA'])
            ->andWhere(['like', 'ubicacion_detalle', 'Catálogo']) // Solo fuentes de poder de catálogo
            ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
            ->all();
            
        $monitores = \frontend\models\Monitor::find()
            ->where(['!=', 'ESTADO', 'BAJA'])
            ->andWhere(['like', 'ubicacion_detalle', 'Catálogo']) // Solo monitores de catálogo
            ->orderBy('ESTADO ASC, MARCA ASC, MODELO ASC')
            ->all();
        
        return $this->render('computo', [
            'model' => $model,
            'procesadores' => $procesadores,
            'memoriaRam' => $memoriaRam,
            'almacenamiento' => $almacenamiento,
            'fuentesPoder' => $fuentesPoder,
            'monitores' => $monitores,
        ]);
    }
    
    /**
     * Actualizar el estado de componentes cuando se asignan a un equipo
     */
    public function actionUpdateComponentStatus()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (\Yii::$app->request->isPost) {
            $data = json_decode(\Yii::$app->request->getRawBody(), true);
            $tipo = $data['tipo'] ?? '';
            $componentId = $data['componentId'] ?? '';
            $accion = $data['accion'] ?? '';
            
            try {
                switch ($tipo) {
                    case 'cpu':
                        $component = \frontend\models\Procesador::findOne($componentId);
                        if ($component && $accion === 'asignar') {
                            $component->Estado = 'Activo';
                            $component->save();
                        }
                        break;
                        
                    case 'ram':
                        $component = \frontend\models\Ram::findOne($componentId);
                        if ($component && $accion === 'asignar') {
                            $component->ESTADO = 'Activo';
                            $component->save();
                        }
                        break;
                        
                    case 'dd':
                        $component = \frontend\models\Almacenamiento::findOne($componentId);
                        if ($component && $accion === 'asignar') {
                            $component->ESTADO = 'Activo';
                            $component->save();
                        }
                        break;
                        
                    case 'fuente':
                        $component = \frontend\models\FuentesDePoder::findOne($componentId);
                        if ($component && $accion === 'asignar') {
                            $component->ESTADO = 'Activo';
                            $component->save();
                        }
                        break;
                }
                
                return ['success' => true, 'message' => 'Componente actualizado correctamente'];
                
            } catch (\Exception $e) {
                return ['success' => false, 'message' => 'Error al actualizar componente: ' . $e->getMessage()];
            }
        }
        
        return ['success' => false, 'message' => 'Solicitud inválida'];
    }
    
    public function actionImpresora()
    {
        $model = new Impresora();
        $modoSimplificado = Yii::$app->request->get('simple', false);
        
        if ($model->load(Yii::$app->request->post())) {
            // Si es modo catálogo, establecer valores por defecto
            if ($modoSimplificado) {
                $timestamp = time() . rand(100, 999);
                $model->ubicacion_detalle = 'Catálogo';
                $model->ESTADO = 'Activo';
                $model->TIPO = $model->TIPO ?: 'N/A';
                $model->NUMERO_INVENTARIO = $model->NUMERO_INVENTARIO ?: 'CAT-' . $timestamp;
                $model->DESCRIPCION = $model->DESCRIPCION ?: 'Item de catálogo';
                $model->NUMERO_SERIE = $model->NUMERO_SERIE ?: 'CAT-' . $timestamp;
                $model->TONER_MODELO = $model->TONER_MODELO ?: 'N/A';
                $model->TIPO_IMPRESION = $model->TIPO_IMPRESION ?: 'N/A';
                $model->fecha = date('Y-m-d');
                $model->ubicacion_edificio = 'Catálogo';
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Impresora agregada exitosamente.');
                if ($modoSimplificado) {
                    return $this->redirect(['impresoras-catalogo-listar']);
                }
                return $this->refresh();
            } else {
                $errors = $model->getErrors();
                Yii::$app->session->setFlash('error', 'Error: ' . print_r($errors, true));
            }
        }
        
        return $this->render('impresora', [
            'model' => $model,
            'modoSimplificado' => $modoSimplificado,
        ]);
    }
    
    public function actionStock()
    {
        return $this->render('stock');
    }

    /**
     * Muestra la página de reciclaje de piezas de equipos
     */
    public function actionRecicjaPiezas()
    {
        return $this->render('reciclaje-piezas');
    }

    /**
     * Obtiene datos del inventario de piezas para reciclaje
     * Retorna JSON con información de todas las piezas disponibles
     */
    public function actionInventarioPiezas()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            $inventario = [];
            
            // Memoria RAM
            $rams = Ram::find()->where(['!=', 'ESTADO', 'DADO DE BAJA'])->all();
            foreach ($rams as $ram) {
                $inventario[] = [
                    'tipo' => 'Memoria RAM',
                    'descripcion' => $ram->MARCA . ' ' . $ram->MODELO,
                    'especificaciones' => $ram->CAPACIDAD . ' - ' . $ram->TIPO_DDR,
                    'estado' => $this->mapearEstado($ram->ESTADO),
                    'numero_serie' => $ram->NUMERO_SERIE ?? 'N/A',
                    'fecha_registro' => $ram->FECHA ?? date('Y-m-d'),
                    'origen' => 'Equipo de baja',
                    'categoria' => 'memoria'
                ];
            }
            
            // Procesadores
            $procesadores = Procesador::find()->where(['!=', 'ESTADO', 'DADO DE BAJA'])->all();
            foreach ($procesadores as $proc) {
                $inventario[] = [
                    'tipo' => 'Procesador',
                    'descripcion' => $proc->MARCA . ' ' . $proc->MODELO,
                    'especificaciones' => $proc->FRECUENCIA_BASE . ' - ' . $proc->NUCLEOS . ' núcleos',
                    'estado' => $this->mapearEstado($proc->ESTADO),
                    'numero_serie' => $proc->NUMERO_SERIE ?? 'N/A',
                    'fecha_registro' => $proc->FECHA ?? date('Y-m-d'),
                    'origen' => 'Equipo de baja',
                    'categoria' => 'procesador'
                ];
            }
            
            // Almacenamiento
            $almacenamientos = Almacenamiento::find()->where(['!=', 'ESTADO', 'DADO DE BAJA'])->all();
            foreach ($almacenamientos as $alm) {
                $inventario[] = [
                    'tipo' => 'Almacenamiento',
                    'descripcion' => $alm->MARCA . ' ' . $alm->MODELO,
                    'especificaciones' => $alm->CAPACIDAD . ' - ' . $alm->TIPO_INTERFAZ,
                    'estado' => $this->mapearEstado($alm->ESTADO),
                    'numero_serie' => $alm->NUMERO_SERIE ?? 'N/A',
                    'fecha_registro' => $alm->FECHA ?? date('Y-m-d'),
                    'origen' => 'Equipo de baja',
                    'categoria' => 'almacenamiento'
                ];
            }
            
            // Monitores
            $monitores = Monitor::find()->where(['!=', 'ESTADO', 'DADO DE BAJA'])->all();
            foreach ($monitores as $mon) {
                $inventario[] = [
                    'tipo' => 'Monitor',
                    'descripcion' => $mon->MARCA . ' ' . $mon->MODELO,
                    'especificaciones' => ($mon->TAMAÑO ?? 'N/A') . ' - ' . ($mon->RESOLUCION ?? 'N/A'),
                    'estado' => $this->mapearEstado($mon->ESTADO),
                    'numero_serie' => $mon->NUMERO_SERIE ?? 'N/A',
                    'fecha_registro' => $mon->FECHA ?? date('Y-m-d'),
                    'origen' => 'Equipo de baja',
                    'categoria' => 'monitor'
                ];
            }
            
            return [
                'success' => true,
                'data' => $inventario,
                'total' => count($inventario),
                'message' => 'Inventario obtenido correctamente'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al obtener el inventario'
            ];
        }
    }
    
    /**
     * Mapea los estados de la base de datos a estados más amigables
     */
    private function mapearEstado($estado)
    {
        $estados = [
            'ACTIVO' => 'Disponible',
            'EN USO' => 'En Uso',
            'INACTIVO' => 'Inactivo (Sin Asignar)',
            'MANTENIMIENTO' => 'En Reparación',
            'RESERVADO' => 'Reservado',
            'REPARACION' => 'En Reparación',
            'DAÑADO' => 'Dañado',
            'DANADO' => 'Dañado',
            'MALO' => 'Dañado',
            'DEFECTUOSO' => 'Dañado',
            'SIN ASIGNAR' => 'Inactivo (Sin Asignar)',
            'NO ASIGNADO' => 'Inactivo (Sin Asignar)'
        ];
        
        return $estados[strtoupper($estado)] ?? 'Disponible';
    }

    /**
     * Devuelve el listado de dispositivos inactivos para una categoría dada.
     * Endpoint: /site/stock-inactivos?categoria=<key>
     * Retorna JSON: { categoria, nombre, items: [...] }
     */
    public function actionStockInactivos($categoria = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($categoria === null) {
            return [
                'error' => true,
                'message' => 'Parámetro "categoria" es requerido'
            ];
        }

        // Mapeo de categorías (debe coincidir con la vista stock)
        $categorias = [
            'nobreak' => ['tabla' => 'nobreak', 'nombre' => 'No Break / UPS', 'id_field' => 'idNOBREAK'],
            'equipo' => ['tabla' => 'equipo', 'nombre' => 'Equipos de Cómputo', 'id_field' => 'idEQUIPO'],
            'impresora' => ['tabla' => 'impresora', 'nombre' => 'Impresoras', 'id_field' => 'idIMPRESORA'],
            'monitor' => ['tabla' => 'monitor', 'nombre' => 'Monitores', 'id_field' => 'idMonitor'],
            'baterias' => ['tabla' => 'baterias', 'nombre' => 'Baterías', 'id_field' => 'id'],
            'almacenamiento' => ['tabla' => 'almacenamiento', 'nombre' => 'Almacenamiento', 'id_field' => 'idAlmacenamiento'],
            'memoria_ram' => ['tabla' => 'memoria_ram', 'nombre' => 'Memoria RAM', 'id_field' => 'id'],
            'equipo_sonido' => ['tabla' => 'equipo_sonido', 'nombre' => 'Equipo de Sonido', 'id_field' => 'id'],
            'procesadores' => ['tabla' => 'procesadores', 'nombre' => 'Procesadores', 'id_field' => 'id'],
            'conectividad' => ['tabla' => 'conectividad', 'nombre' => 'Conectividad', 'id_field' => 'idCONECTIVIDAD'],
            'telefonia' => ['tabla' => 'telefonia', 'nombre' => 'Telefonía', 'id_field' => 'idTELEFONIA'],
            'video_vigilancia' => ['tabla' => 'video_vigilancia', 'nombre' => 'Video Vigilancia', 'id_field' => 'id'],
            'adaptadores' => ['tabla' => 'adaptadores', 'nombre' => 'Adaptadores', 'id_field' => 'id']
        ];

        if (!isset($categorias[$categoria])) {
            return [
                'error' => true,
                'message' => 'Categoría no reconocida: ' . $categoria
            ];
        }

        $desc = $categorias[$categoria];
        $tabla = $desc['tabla'];

        try {
            $connection = Yii::$app->db;

            // Verificar existencia de la tabla
            $tablaExiste = $connection->createCommand("SHOW TABLES LIKE :tabla")->bindValue(':tabla', $tabla)->queryOne();
            if (!$tablaExiste) {
                return [
                    'error' => true,
                    'message' => "Tabla '$tabla' no existe en la base de datos"
                ];
            }

            // Determinar columna de estado disponible
            $cols = $connection->createCommand("SHOW COLUMNS FROM $tabla")->queryAll();
            $colNames = array_column($cols, 'Field');
            $candidatoEstado = null;
            foreach (['Estado', 'ESTADO', 'estado'] as $c) {
                if (in_array($c, $colNames)) { $candidatoEstado = $c; break; }
            }

            if ($candidatoEstado === null) {
                return [
                    'error' => true,
                    'message' => "No se encontró columna de estado en la tabla '$tabla'"
                ];
            }

            // Obtener filas que no estén activas (case-insensitive)
            $sql = "SELECT * FROM $tabla WHERE LOWER(COALESCE($candidatoEstado, '')) != 'activo' ORDER BY 1 LIMIT 1000";
            $rows = $connection->createCommand($sql)->queryAll();

            $items = [];
            foreach ($rows as $r) {
                $items[] = [
                    'id' => isset($r[$desc['id_field']]) ? $r[$desc['id_field']] : (isset($r['id']) ? $r['id'] : null),
                    'MARCA' => $r['MARCA'] ?? ($r['marca'] ?? null),
                    'MODELO' => $r['MODELO'] ?? ($r['modelo'] ?? null),
                    'NUMERO_SERIE' => $r['NUMERO_SERIE'] ?? ($r['numero_serie'] ?? null),
                    'NUMERO_INVENTARIO' => $r['NUMERO_INVENTARIO'] ?? ($r['numero_inventario'] ?? null),
                    'Estado' => $r[$candidatoEstado] ?? null,
                    'ubicacion_edificio' => $r['ubicacion_edificio'] ?? ($r['ubicacion'] ?? null),
                    'ubicacion_detalle' => $r['ubicacion_detalle'] ?? null,
                    'data' => $r
                ];
            }

            return [
                'error' => false,
                'categoria' => $categoria,
                'nombre' => $desc['nombre'],
                'count' => count($items),
                'items' => $items
            ];

        } catch (Exception $e) {
            Yii::error('Error actionStockInactivos: ' . $e->getMessage(), __METHOD__);
            return [
                'error' => true,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ];
        }
    }
    
    public function actionDownloadResumen()
    {
        // Definir categorías (misma estructura usada en la vista)
        $categorias = [
            'nobreak' => ['tabla' => 'nobreak'],
            'equipo' => ['tabla' => 'equipo'],
            'impresora' => ['tabla' => 'impresora'],
            'monitor' => ['tabla' => 'monitor'],
            'adaptadores' => ['tabla' => 'adaptadores']
        ];

        // Mapeo campos estado por tabla (igual que en la vista)
        $camposEstado = [
            'nobreak' => 'Estado',
            'equipo' => 'Estado',
            'impresora' => 'Estado',
            'monitor' => 'ESTADO',
            'adaptadores' => 'estado'
        ];

        $connection = Yii::$app->db;
        $totalGeneral = $activosGeneral = $disponiblesGeneral = $danadosGeneral = $bajasGeneral = $mantenimientoGeneral = 0;

        foreach ($categorias as $key => $cat) {
            $tabla = $cat['tabla'];
            $campoEstado = isset($camposEstado[$tabla]) ? $camposEstado[$tabla] : 'Estado';

            // Si la tabla no existe, saltar
            $tablaExiste = $connection->createCommand("SHOW TABLES LIKE :t", [':t' => $tabla])->queryOne();
            if (!$tablaExiste) {
                continue;
            }

            $sql = "
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN $campoEstado = 'Activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN $campoEstado IN ('Inactivo', 'Inactivo(Sin Asignar)', 'Disponible') THEN 1 ELSE 0 END) as disponibles,
                    SUM(CASE WHEN $campoEstado = 'dañado(Proceso de baja)' THEN 1 ELSE 0 END) as danados,
                    SUM(CASE WHEN $campoEstado = 'BAJA' THEN 1 ELSE 0 END) as bajas,
                    SUM(CASE WHEN $campoEstado LIKE '%mantenimiento%' OR $campoEstado = 'Reparación' OR $campoEstado LIKE '%Mantenimiento%' THEN 1 ELSE 0 END) as mantenimiento
                FROM $tabla
            ";

            $res = $connection->createCommand($sql)->queryOne();
            $total = (int)$res['total'];
            $activos = (int)$res['activos'];
            $disponibles = (int)$res['disponibles'];
            $danados = (int)$res['danados'];
            $bajas = (int)$res['bajas'];
            $mantenimiento = (int)$res['mantenimiento'];

            $totalGeneral += $total;
            $activosGeneral += $activos;
            $disponiblesGeneral += $disponibles;
            $danadosGeneral += $danados;
            $bajasGeneral += $bajas;
            $mantenimientoGeneral += $mantenimiento;
        }

        // Preparar CSV en memoria
        $fp = fopen('php://temp', 'r+');
        fputcsv($fp, ['Métrica', 'Valor']);
        fputcsv($fp, ['Total Equipos', $totalGeneral]);
        fputcsv($fp, ['En Uso', $activosGeneral]);
        fputcsv($fp, ['Disponibles', $disponiblesGeneral]);
        fputcsv($fp, ['Mantenimiento', $mantenimientoGeneral]);
        fputcsv($fp, ['Dañados', $danadosGeneral]);
        fputcsv($fp, ['Baja', $bajasGeneral]);
        $dispPct = $totalGeneral > 0 ? round(($disponiblesGeneral / $totalGeneral) * 100, 1) . '%' : '0%';
        fputcsv($fp, ['Disponibilidad', $dispPct]);
        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);

        // Añadir BOM para compatibilidad con Excel
        $content = "\xEF\xBB\xBF" . $content;
        $filename = 'resumen_general_inventario_' . date('Ymd_His') . '.csv';

        return Yii::$app->response->sendContentAsFile($content, $filename, [
            'mimeType' => 'text/csv; charset=UTF-8',
            'inline' => false
        ]);
    }
    
    public function actionCategoriaData()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $raw = Yii::$app->request->getRawBody();
        $data = json_decode($raw, true);
        $categoria = isset($data['categoria']) ? $data['categoria'] : null;
        $filtro = isset($data['filtro']) ? $data['filtro'] : null;

        // Mapeo seguro de categorias a tablas y campo estado
        $map = [
            'nobreak' => ['tabla' => 'nobreak', 'campo' => 'Estado'],
            'equipo' => ['tabla' => 'equipo', 'campo' => 'Estado'],
            'impresora' => ['tabla' => 'impresora', 'campo' => 'Estado'],
            'monitor' => ['tabla' => 'monitor', 'campo' => 'ESTADO'],
            'adaptadores' => ['tabla' => 'adaptadores', 'campo' => 'estado'],
            'video_vigilancia' => ['tabla' => 'video_vigilancia', 'campo' => 'estado'],
            'conectividad' => ['tabla' => 'conectividad', 'campo' => 'Estado'],
            'telefonia' => ['tabla' => 'telefonia', 'campo' => 'ESTADO'],
            'procesadores' => ['tabla' => 'procesadores', 'campo' => 'estado'],
            'almacenamiento' => ['tabla' => 'almacenamiento', 'campo' => 'ESTADO'],
            'memoria_ram' => ['tabla' => 'memoria_ram', 'campo' => 'estado'],
            'sonido' => ['tabla' => 'equipo_sonido', 'campo' => 'estado'],
            'baterias' => ['tabla' => 'baterias', 'campo' => 'estado'],
            'fuentes_de_poder' => ['tabla' => 'fuentes_de_poder', 'campo' => 'ESTADO'],
        ];

        if (!$categoria || !isset($map[$categoria])) {
            return ['success' => false, 'html' => '<div class="alert alert-warning">Categoría no válida.</div>'];
        }

        $tabla = $map[$categoria]['tabla'];
        $campoEstado = $map[$categoria]['campo'];

        try {
            $db = Yii::$app->db;
            if ($filtro === 'inactivo_sin_asignar') {
                $sql = "SELECT * FROM `{$tabla}` WHERE `{$campoEstado}` = :est AND `{$campoEstado}` != 'BAJA'";
                $rows = $db->createCommand($sql)->bindValue(':est', 'Inactivo(Sin Asignar)')->queryAll();
            } elseif ($filtro === 'activo') {
                $sql = "SELECT * FROM `{$tabla}` WHERE `{$campoEstado}` = :est AND `{$campoEstado}` != 'BAJA'";
                $rows = $db->createCommand($sql)->bindValue(':est', 'Activo')->queryAll();
            } else {
                // Para "ambos" o cualquier otro filtro, excluir BAJA
                $sql = "SELECT * FROM `{$tabla}` WHERE `{$campoEstado}` != 'BAJA' LIMIT 500";
                $rows = $db->createCommand($sql)->queryAll();
            }

            if (empty($rows)) {
                $mensaje = $filtro === 'activo'
                    ? 'No hay registros Activo en esta categoría.'
                    : 'No hay registros Inactivo(Sin Asignar) en esta categoría.';
                $html = '<div class="alert alert-info">' . $mensaje . '</div>';
                return ['success' => true, 'html' => $html];
            }

            // Construir tabla HTML simple (puedes refinar columnas)
            $headers = array_keys($rows[0]);
            $html = '<div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr>';
            foreach ($headers as $h) {
                $html .= '<th>' . Html::encode($h) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            foreach ($rows as $r) {
                $html .= '<tr>';
                foreach ($headers as $h) {
                    $html .= '<td>' . Html::encode((string)($r[$h] ?? '')) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table></div>';

            return ['success' => true, 'html' => $html];

        } catch (\Throwable $e) {
            return ['success' => false, 'html' => '<div class="alert alert-danger">Error al consultar datos.</div>'];
        }
    }

    /**
     * Muestra el historial de bajas por categoría
     */
    public function actionHistorialBajas()
    {
        Yii::info('Iniciando historial de bajas');
        
        $categorias = [
            'nobreak' => ['model' => Nobreak::class, 'campo_estado' => 'Estado'],
            'equipo' => ['model' => Equipo::class, 'campo_estado' => 'Estado'],
            'impresora' => ['model' => Impresora::class, 'campo_estado' => 'Estado'],
            'videovigilancia' => ['model' => VideoVigilancia::class, 'campo_estado' => 'ESTADO'],
            'conectividad' => ['model' => Conectividad::class, 'campo_estado' => 'Estado'],
            'telefonia' => ['model' => Telefonia::class, 'campo_estado' => 'ESTADO'],
            'procesadores' => ['model' => Procesador::class, 'campo_estado' => 'estado'],
            'almacenamiento' => ['model' => Almacenamiento::class, 'campo_estado' => 'ESTADO'],
            'memoria_ram' => ['model' => Ram::class, 'campo_estado' => 'estado'],
            'equipo_sonido' => ['model' => Sonido::class, 'campo_estado' => 'estado'],
            'monitor' => ['model' => Monitor::class, 'campo_estado' => 'ESTADO'],
            'baterias' => ['model' => Bateria::class, 'campo_estado' => 'ESTADO'],
            'adaptadores' => ['model' => Adaptador::class, 'campo_estado' => 'estado']
        ];

        $equiposBaja = [];
        foreach ($categorias as $key => $config) {
            $modelClass = $config['model'];
            try {
                Yii::info("Consultando equipos de baja para categoría: $key");
                
                // Verificar si la clase existe
                if (!class_exists($modelClass)) {
                    Yii::error("La clase $modelClass no existe");
                    continue;
                }

                // Crear la consulta
                $query = $modelClass::find()
                    ->where([$config['campo_estado'] => 'BAJA']);
                
                // Log de la consulta SQL
                Yii::info("SQL para $key: " . $query->createCommand()->getRawSql());
                
                // Ejecutar la consulta
                $equipos = $query->all();
                
                // Log del resultado
                Yii::info("Encontrados " . count($equipos) . " equipos para $key");
                
                $equiposBaja[$key] = $equipos;
                
            } catch (\Exception $e) {
                Yii::error("Error al obtener datos de $key: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                $equiposBaja[$key] = [];
            }
        }

        Yii::info("Total de categorías con equipos: " . count(array_filter($equiposBaja)));

        return $this->render('historial-bajas', [
            'equiposBaja' => $equiposBaja,
        ]);
    }
    
    /**
     * Actualiza múltiples registros de una categoría específica
     */
    public function actionActualizarCategoria()
    {
        if (!Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => false,
                'message' => 'Método no permitido'
            ];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $data = json_decode(Yii::$app->request->getRawBody(), true);
            
            if (!$data || !isset($data['categoria']) || !isset($data['cambios'])) {
                return [
                    'success' => false,
                    'message' => 'Datos incompletos'
                ];
            }

            $categoria = $data['categoria'];
            $cambios = $data['cambios'];

            // Mapeo de categorías a modelos y tablas
            $modelosMap = [
                'nobreak' => ['model' => 'frontend\models\Nobreak', 'tabla' => 'nobreak', 'pk' => 'idNOBREAK'],
                'equipo' => ['model' => 'frontend\models\Equipo', 'tabla' => 'equipo', 'pk' => 'idEQUIPO'],
                'impresora' => ['model' => 'frontend\models\Impresora', 'tabla' => 'impresora', 'pk' => 'idIMPRESORA'],
                'monitor' => ['model' => 'frontend\models\Monitor', 'tabla' => 'monitor', 'pk' => 'idMonitor'],
                'adaptadores' => ['model' => 'frontend\models\Adaptador', 'tabla' => 'adaptadores', 'pk' => 'idAdaptador'],
                'baterias' => ['model' => 'frontend\models\Bateria', 'tabla' => 'baterias', 'pk' => 'idBateria'],
                'almacenamiento' => ['model' => 'frontend\models\Almacenamiento', 'tabla' => 'almacenamiento', 'pk' => 'idAlmacenamiento'],
                'memoria_ram' => ['model' => 'frontend\models\Ram', 'tabla' => 'memoria_ram', 'pk' => 'idRAM'],
                'sonido' => ['model' => 'frontend\models\Sonido', 'tabla' => 'equipo_sonido', 'pk' => 'idSonido'],
                'procesadores' => ['model' => 'frontend\models\Procesador', 'tabla' => 'procesadores', 'pk' => 'idProcesador'],
                'conectividad' => ['model' => 'frontend\models\Conectividad', 'tabla' => 'conectividad', 'pk' => 'idCONECTIVIDAD'],
                'telefonia' => ['model' => 'frontend\models\Telefonia', 'tabla' => 'telefonia', 'pk' => 'idTELEFONIA'],
                'video_vigilancia' => ['model' => 'frontend\models\VideoVigilancia', 'tabla' => 'video_vigilancia', 'pk' => 'idVIDEO_VIGILANCIA'],
            ];

            if (!isset($modelosMap[$categoria])) {
                return [
                    'success' => false,
                    'message' => 'Categoría no válida: ' . $categoria
                ];
            }

            $config = $modelosMap[$categoria];
            $modelClass = $config['model'];

            $actualizados = 0;
            $errores = [];

            foreach ($cambios as $cambio) {
                try {
                    $id = $cambio['id'];
                    $campo = $this->mapearNombreCampo($cambio['columna'], $categoria);
                    $valorNuevo = $cambio['valorNuevo'];

                    // Buscar el registro por ID
                    $model = $modelClass::findOne($id);
                    
                    if (!$model) {
                        $errores[] = "Registro con ID {$id} no encontrado";
                        continue;
                    }

                    // Verificar que el campo existe en el modelo
                    if (!$model->hasAttribute($campo)) {
                        $errores[] = "Campo '{$campo}' no existe en el modelo";
                        continue;
                    }

                    // Actualizar el campo
                    $model->$campo = $valorNuevo;

                    // Agregar información de auditoría si los campos existen
                    if ($model->hasAttribute('fecha_ultima_edicion')) {
                        $model->fecha_ultima_edicion = date('Y-m-d H:i:s');
                    }
                    if ($model->hasAttribute('ultimo_editor')) {
                        $model->ultimo_editor = Yii::$app->user->identity->username ?? 'Sistema';
                    }

                    // Guardar el modelo
                    if ($model->save()) {
                        $actualizados++;
                    } else {
                        $errores[] = "Error al guardar ID {$id}: " . implode(', ', $model->getFirstErrors());
                    }

                } catch (\Exception $e) {
                    $errores[] = "Error en ID {$cambio['id']}: " . $e->getMessage();
                }
            }

            $mensaje = "Se actualizaron {$actualizados} registro(s) exitosamente.";
            if (!empty($errores)) {
                $mensaje .= " Errores: " . implode('; ', $errores);
            }

            return [
                'success' => true,
                'message' => $mensaje,
                'actualizados' => $actualizados,
                'errores' => $errores
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mapea nombres de columnas de la interfaz a nombres de campos del modelo
     * Solo permite edición de campos específicos: DESCRIPCION, Estado, ubicacion_edificio, ubicacion_detalle
     */
    private function mapearNombreCampo($nombreColumna, $categoria)
    {
        // Lista de campos permitidos para edición
        $camposPermitidos = ['descripcion', 'estado', 'ubicacion_edificio', 'ubicacion_detalle'];
        
        // Verificar si el campo está permitido
        $nombreColumnaNormalizado = strtolower($nombreColumna);
        $esPermitido = false;
        foreach ($camposPermitidos as $campo) {
            if (strpos($nombreColumnaNormalizado, $campo) !== false) {
                $esPermitido = true;
                break;
            }
        }
        
        if (!$esPermitido) {
            throw new \Exception("Campo '$nombreColumna' no está permitido para edición");
        }

        // Mapeo específico por categoría solo para campos permitidos
        $mapeoEspecifico = [
            'nobreak' => [
                'Estado' => 'Estado',
                'DESCRIPCION' => 'DESCRIPCION',
                'descripcion' => 'descripcion',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'equipo' => [
                'Estado' => 'Estado',
                'DESCRIPCION' => 'descripcion',
                'descripcion' => 'descripcion',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'monitor' => [
                'ESTADO' => 'ESTADO',
                'DESCRIPCION' => 'DESCRIPCION',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'impresora' => [
                'Estado' => 'Estado',
                'DESCRIPCION' => 'DESCRIPCION',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'adaptadores' => [
                'estado' => 'estado',
                'descripcion' => 'descripcion',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'video_vigilancia' => [
                'estado' => 'estado',
                'DESCRIPCION' => 'DESCRIPCION',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'conectividad' => [
                'Estado' => 'Estado',
                'DESCRIPCION' => 'DESCRIPCION',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'telefonia' => [
                'ESTADO' => 'ESTADO',
                'DESCRIPCION' => 'DESCRIPCION',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'procesadores' => [
                'estado' => 'estado',
                'DESCRIPCION' => 'DESCRIPCION',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'almacenamiento' => [
                'ESTADO' => 'ESTADO',
                'DESCRIPCION' => 'DESCRIPCION',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'memoria_ram' => [
                'estado' => 'estado',
                'descripcion' => 'descripcion',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'sonido' => [
                'estado' => 'estado',
                'descripcion' => 'descripcion',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
            'baterias' => [
                'estado' => 'estado',
                'descripcion' => 'descripcion',
                'ubicacion_edificio' => 'ubicacion_edificio',
                'ubicacion_detalle' => 'ubicacion_detalle',
            ],
        ];

        // Si existe mapeo específico para la categoría, usarlo
        if (isset($mapeoEspecifico[$categoria]) && isset($mapeoEspecifico[$categoria][$nombreColumna])) {
            return $mapeoEspecifico[$categoria][$nombreColumna];
        }

        // Mapeo genérico para campos permitidos
        $nombreColumnaNormalizado = strtolower($nombreColumna);
        if (strpos($nombreColumnaNormalizado, 'estado') !== false) {
            return strpos($nombreColumnaNormalizado, 'estado') === 0 ? 'ESTADO' : 'Estado';
        }
        if (strpos($nombreColumnaNormalizado, 'descripcion') !== false) {
            return 'DESCRIPCION';
        }
        if (strpos($nombreColumnaNormalizado, 'ubicacion_edificio') !== false) {
            return 'ubicacion_edificio';
        }
        if (strpos($nombreColumnaNormalizado, 'ubicacion_detalle') !== false) {
            return 'ubicacion_detalle';
        }

        throw new \Exception("No se pudo mapear el campo '$nombreColumna' para la categoría '$categoria'");
    }

    public function actionEquipoEliminar()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['equipo-listar']);
        }

        $id = $request->post('id');
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID no proporcionado');
            return $this->redirect(['equipo-listar']);
        }

        try {
            $equipo = Equipo::findOne(['idEQUIPO' => $id]);
            
            if (!$equipo) {
                Yii::$app->session->setFlash('error', "Equipo con ID $id no encontrado");
                return $this->redirect(['equipo-listar']);
            }
            
            $marca = $equipo->MARCA ?? 'Sin marca';
            $modelo = $equipo->MODELO ?? 'Sin modelo';
            
            // Eliminar el equipo
            if ($equipo->delete()) {
                Yii::$app->session->setFlash('success', "Equipo $marca $modelo eliminado exitosamente");
            } else {
                Yii::$app->session->setFlash('error', 'Error al eliminar el equipo');
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al eliminar: ' . $e->getMessage());
        }
        
        return $this->redirect(['equipo-listar']);
    }

    public function actionEquipoEliminarMultiple()
    {
        $request = Yii::$app->request;
        
        if (!$request->isPost) {
            Yii::$app->session->setFlash('error', 'Método no permitido');
            return $this->redirect(['equipo-listar']);
        }

        $ids = $request->post('ids');
        
        if (!$ids || !is_array($ids) || empty($ids)) {
            Yii::$app->session->setFlash('error', 'No se seleccionaron equipos para eliminar');
            return $this->redirect(['equipo-listar']);
        }

        try {
            $eliminados = 0;
            $errores = [];

            foreach ($ids as $id) {
                if (empty($id)) continue;
                
                $equipo = Equipo::findOne(['idEQUIPO' => $id]);
                
                if (!$equipo) {
                    $errores[] = "Equipo con ID $id no encontrado";
                    continue;
                }

                if ($equipo->delete()) {
                    $eliminados++;
                } else {
                    $errores[] = "Error al eliminar equipo ID $id";
                }
            }

            if ($eliminados > 0) {
                $message = "Se eliminaron $eliminados equipos exitosamente";
                if (count($errores) > 0) {
                    $message .= ". Algunos errores: " . implode(', ', $errores);
                }
                Yii::$app->session->setFlash('success', $message);
            } else {
                Yii::$app->session->setFlash('error', 'No se pudo eliminar ningún equipo. ' . implode(', ', $errores));
            }

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Error al procesar eliminación: ' . $e->getMessage());
        }
        
        return $this->redirect(['equipo-listar']);
    }

    // Método temporal para diagnosticar problemas de eliminación
    public function actionTestEliminar()
    {
        $equipo = Equipo::find()->limit(1)->one();
        if ($equipo) {
            \Yii::error('Equipo encontrado para prueba: ' . json_encode($equipo->attributes));
            try {
                if ($equipo->delete()) {
                    return $this->asJson(['success' => true, 'message' => 'Prueba exitosa']);
                } else {
                    return $this->asJson(['success' => false, 'message' => 'No se pudo eliminar', 'errors' => $equipo->getErrors()]);
                }
            } catch (Exception $e) {
                return $this->asJson(['success' => false, 'message' => 'Excepción: ' . $e->getMessage()]);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => 'No hay equipos para probar']);
        }
    }

    // =====================================================
    // ACCIONES PARA RECICLAJE DE PIEZAS
    // =====================================================

    /**
     * Registra una nueva pieza en el inventario de reciclaje
     * Recibe datos via POST y devuelve JSON
     */
    public function actionRegistrarPiezaReciclaje()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!Yii::$app->request->isPost) {
            return [
                'success' => false,
                'message' => 'Método no permitido'
            ];
        }

        try {
            $data = Yii::$app->request->post();
            
            // Validar datos requeridos
            if (empty($data['tipo_pieza']) || empty($data['marca'])) {
                return [
                    'success' => false,
                    'message' => 'Tipo de pieza y marca son requeridos'
                ];
            }
            
            $pieza = new PiezaReciclaje();
            $pieza->tipo_pieza = trim($data['tipo_pieza']);
            $pieza->marca = trim($data['marca']);
            $pieza->modelo = !empty($data['modelo']) ? trim($data['modelo']) : null;
            $pieza->especificaciones = !empty($data['especificaciones']) ? trim($data['especificaciones']) : null;
            $pieza->numero_serie = !empty($data['numero_serie']) ? trim($data['numero_serie']) : null;
            $pieza->numero_inventario = !empty($data['numero_inventario']) ? trim($data['numero_inventario']) : null;
            $pieza->estado_pieza = !empty($data['estado_pieza']) ? trim($data['estado_pieza']) : PiezaReciclaje::ESTADO_DISPONIBLE;
            $pieza->condicion = !empty($data['condicion']) ? trim($data['condicion']) : PiezaReciclaje::CONDICION_BUENO;
            $pieza->equipo_origen = !empty($data['equipo_origen']) ? trim($data['equipo_origen']) : null;
            $pieza->equipo_origen_descripcion = !empty($data['equipo_origen_descripcion']) ? trim($data['equipo_origen_descripcion']) : null;
            $pieza->componente_defectuoso = !empty($data['componente_defectuoso']) ? trim($data['componente_defectuoso']) : null;
            $pieza->motivo_recuperacion = !empty($data['motivo_recuperacion']) ? trim($data['motivo_recuperacion']) : null;
            $pieza->ubicacion_almacen = !empty($data['ubicacion_almacen']) ? trim($data['ubicacion_almacen']) : null;
            $pieza->observaciones = !empty($data['observaciones']) ? trim($data['observaciones']) : null;
            $pieza->fecha_recuperacion = !empty($data['fecha_recuperacion']) ? $data['fecha_recuperacion'] : date('Y-m-d');
            
            if ($pieza->save()) {
                return [
                    'success' => true,
                    'message' => 'Pieza registrada exitosamente',
                    'data' => [
                        'id' => $pieza->id,
                        'tipo_pieza' => $pieza->tipo_pieza,
                        'marca' => $pieza->marca,
                        'modelo' => $pieza->modelo,
                        'estado_pieza' => $pieza->estado_pieza
                    ]
                ];
            } else {
                $erroresTexto = [];
                foreach ($pieza->errors as $campo => $errores) {
                    $erroresTexto[] = $campo . ': ' . implode(', ', $errores);
                }
                return [
                    'success' => false,
                    'message' => 'Error de validación: ' . implode('; ', $erroresTexto),
                    'errors' => $pieza->errors
                ];
            }
        } catch (Exception $e) {
            Yii::error('Error en registrarPiezaReciclaje: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene el inventario completo de piezas de reciclaje
     * Retorna JSON con todas las piezas y estadísticas
     */
    public function actionInventarioPiezasReciclaje()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $piezas = PiezaReciclaje::find()
                ->orderBy(['fecha_creacion' => SORT_DESC])
                ->all();
            
            $inventario = [];
            foreach ($piezas as $pieza) {
                $inventario[] = [
                    'id' => $pieza->id,
                    'tipo' => $pieza->tipo_pieza,
                    'descripcion' => $pieza->marca . ' ' . ($pieza->modelo ?? ''),
                    'especificaciones' => $pieza->especificaciones ?? 'N/A',
                    'estado' => $pieza->estado_pieza,
                    'condicion' => $pieza->condicion,
                    'numero_serie' => $pieza->numero_serie ?? 'N/A',
                    'numero_inventario' => $pieza->numero_inventario ?? 'N/A',
                    'equipo_origen' => $pieza->equipo_origen ?? 'N/A',
                    'equipo_origen_descripcion' => $pieza->equipo_origen_descripcion ?? '',
                    'componente_defectuoso' => $pieza->componente_defectuoso ?? 'N/A',
                    'ubicacion_almacen' => $pieza->ubicacion_almacen ?? 'Sin asignar',
                    'fecha_registro' => $pieza->fecha_recuperacion,
                    'categoria' => $this->categorizarTipoPieza($pieza->tipo_pieza)
                ];
            }
            
            // Obtener estadísticas
            $estadisticas = PiezaReciclaje::getEstadisticas();
            
            // Contar por categoría
            $conteosCategorias = [
                'memoria' => PiezaReciclaje::find()->where(['tipo_pieza' => PiezaReciclaje::TIPO_RAM])->count(),
                'procesador' => PiezaReciclaje::find()->where(['tipo_pieza' => PiezaReciclaje::TIPO_PROCESADOR])->count(),
                'almacenamiento' => PiezaReciclaje::find()->where(['like', 'tipo_pieza', 'Disco'])->orWhere(['tipo_pieza' => PiezaReciclaje::TIPO_SSD])->count(),
                'monitor' => PiezaReciclaje::find()->where(['tipo_pieza' => PiezaReciclaje::TIPO_MONITOR])->count(),
                'fuente' => PiezaReciclaje::find()->where(['tipo_pieza' => PiezaReciclaje::TIPO_FUENTE])->count(),
            ];
            
            return [
                'success' => true,
                'data' => $inventario,
                'total' => count($inventario),
                'estadisticas' => $estadisticas,
                'conteos' => $conteosCategorias,
                'message' => 'Inventario obtenido correctamente'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener el inventario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Categoriza el tipo de pieza para filtros
     */
    private function categorizarTipoPieza($tipo)
    {
        $categorias = [
            'Memoria RAM' => 'memoria',
            'Procesador' => 'procesador',
            'Disco Duro' => 'almacenamiento',
            'SSD' => 'almacenamiento',
            'Monitor' => 'monitor',
            'Fuente de Poder' => 'fuente',
            'Tarjeta de Video' => 'componente',
            'Tarjeta Madre' => 'componente',
        ];
        return $categorias[$tipo] ?? 'otro';
    }

    /**
     * Actualiza una pieza de reciclaje
     */
    public function actionActualizarPiezaReciclaje()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        try {
            $data = Yii::$app->request->post();
            $id = $data['id'] ?? null;
            
            if (!$id) {
                return ['success' => false, 'message' => 'ID de pieza no proporcionado'];
            }
            
            $pieza = PiezaReciclaje::findOne($id);
            
            if (!$pieza) {
                return ['success' => false, 'message' => 'Pieza no encontrada'];
            }
            
            // Guardar estado anterior para historial
            $estadoAnterior = $pieza->estado_pieza;
            
            // Actualizar campos
            if (isset($data['tipo_pieza'])) $pieza->tipo_pieza = $data['tipo_pieza'];
            if (isset($data['marca'])) $pieza->marca = $data['marca'];
            if (isset($data['modelo'])) $pieza->modelo = $data['modelo'];
            if (isset($data['especificaciones'])) $pieza->especificaciones = $data['especificaciones'];
            if (isset($data['numero_serie'])) $pieza->numero_serie = $data['numero_serie'];
            if (isset($data['numero_inventario'])) $pieza->numero_inventario = $data['numero_inventario'];
            if (isset($data['estado_pieza'])) $pieza->estado_pieza = $data['estado_pieza'];
            if (isset($data['condicion'])) $pieza->condicion = $data['condicion'];
            if (isset($data['equipo_origen'])) $pieza->equipo_origen = $data['equipo_origen'];
            if (isset($data['equipo_origen_descripcion'])) $pieza->equipo_origen_descripcion = $data['equipo_origen_descripcion'];
            if (isset($data['componente_defectuoso'])) $pieza->componente_defectuoso = $data['componente_defectuoso'];
            if (isset($data['motivo_recuperacion'])) $pieza->motivo_recuperacion = $data['motivo_recuperacion'];
            if (isset($data['ubicacion_almacen'])) $pieza->ubicacion_almacen = $data['ubicacion_almacen'];
            if (isset($data['observaciones'])) $pieza->observaciones = $data['observaciones'];
            if (isset($data['asignado_a'])) $pieza->asignado_a = $data['asignado_a'];
            
            if ($pieza->save()) {
                return [
                    'success' => true,
                    'message' => 'Pieza actualizada exitosamente',
                    'data' => $pieza->attributes
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al actualizar la pieza',
                    'errors' => $pieza->errors
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Elimina una pieza de reciclaje
     */
    public function actionEliminarPiezaReciclaje()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        try {
            $id = Yii::$app->request->post('id');
            
            if (!$id) {
                return ['success' => false, 'message' => 'ID de pieza no proporcionado'];
            }
            
            $pieza = PiezaReciclaje::findOne($id);
            
            if (!$pieza) {
                return ['success' => false, 'message' => 'Pieza no encontrada'];
            }
            
            if ($pieza->delete()) {
                return [
                    'success' => true,
                    'message' => 'Pieza eliminada exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al eliminar la pieza'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene los detalles de una pieza específica
     */
    public function actionDetallePiezaReciclaje($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $pieza = PiezaReciclaje::findOne($id);
            
            if (!$pieza) {
                return ['success' => false, 'message' => 'Pieza no encontrada'];
            }
            
            // Obtener historial
            $historial = HistorialPiezaReciclaje::find()
                ->where(['pieza_id' => $id])
                ->orderBy(['fecha_movimiento' => SORT_DESC])
                ->all();
            
            $historialData = [];
            foreach ($historial as $h) {
                $historialData[] = [
                    'accion' => $h->accion,
                    'estado_anterior' => $h->estado_anterior,
                    'estado_nuevo' => $h->estado_nuevo,
                    'equipo_destino' => $h->equipo_destino,
                    'observaciones' => $h->observaciones,
                    'usuario' => $h->usuario,
                    'fecha' => $h->fecha_movimiento
                ];
            }
            
            return [
                'success' => true,
                'data' => $pieza->attributes,
                'historial' => $historialData
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las estadísticas del módulo de reciclaje
     */
    public function actionEstadisticasReciclaje()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $estadisticas = PiezaReciclaje::getEstadisticas();
            
            // Piezas por tipo
            $porTipo = [];
            foreach (PiezaReciclaje::getTiposPieza() as $tipo => $label) {
                $count = PiezaReciclaje::find()->where(['tipo_pieza' => $tipo])->count();
                if ($count > 0) {
                    $porTipo[$tipo] = $count;
                }
            }
            
            // Piezas por condición
            $porCondicion = [];
            foreach (PiezaReciclaje::getCondiciones() as $condicion => $label) {
                $count = PiezaReciclaje::find()->where(['condicion' => $condicion])->count();
                if ($count > 0) {
                    $porCondicion[$condicion] = $count;
                }
            }
            
            return [
                'success' => true,
                'estadisticas' => $estadisticas,
                'porTipo' => $porTipo,
                'porCondicion' => $porCondicion
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las opciones para los selectores del formulario
     */
    public function actionOpcionesPiezaReciclaje()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return [
            'success' => true,
            'tiposPieza' => PiezaReciclaje::getTiposPieza(),
            'estados' => PiezaReciclaje::getEstados(),
            'condiciones' => PiezaReciclaje::getCondiciones()
        ];
    }

    /**
     * Obtiene datos del catálogo existente para preselección
     * según el tipo de pieza seleccionado
     */
    public function actionObtenerCatalogoPiezas($tipo = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $catalogo = [];
            
            switch ($tipo) {
                case 'Memoria RAM':
                    $items = Ram::find()
                        ->select(['MARCA', 'MODELO', 'CAPACIDAD', 'TIPO_DDR', 'numero_serie', 'numero_inventario'])
                        ->distinct()
                        ->all();
                    foreach ($items as $item) {
                        $catalogo[] = [
                            'marca' => $item->MARCA,
                            'modelo' => $item->MODELO,
                            'especificaciones' => $item->CAPACIDAD . ' ' . $item->TIPO_DDR,
                            'numero_serie' => $item->numero_serie,
                            'numero_inventario' => $item->numero_inventario,
                            'descripcion' => $item->MARCA . ' ' . $item->MODELO . ' - ' . $item->CAPACIDAD . ' ' . $item->TIPO_DDR
                        ];
                    }
                    break;
                    
                case 'Procesador':
                    $items = Procesador::find()
                        ->select(['MARCA', 'MODELO', 'FRECUENCIA_BASE', 'NUCLEOS', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])
                        ->distinct()
                        ->all();
                    foreach ($items as $item) {
                        $catalogo[] = [
                            'marca' => $item->MARCA,
                            'modelo' => $item->MODELO,
                            'especificaciones' => $item->FRECUENCIA_BASE . ' - ' . $item->NUCLEOS . ' núcleos',
                            'numero_serie' => $item->NUMERO_SERIE,
                            'numero_inventario' => $item->NUMERO_INVENTARIO,
                            'descripcion' => $item->MARCA . ' ' . $item->MODELO . ' - ' . $item->FRECUENCIA_BASE
                        ];
                    }
                    break;
                    
                case 'Disco Duro':
                case 'SSD':
                    $items = Almacenamiento::find()
                        ->select(['MARCA', 'MODELO', 'CAPACIDAD', 'TIPO', 'INTERFAZ', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])
                        ->distinct()
                        ->all();
                    foreach ($items as $item) {
                        $catalogo[] = [
                            'marca' => $item->MARCA,
                            'modelo' => $item->MODELO,
                            'especificaciones' => $item->CAPACIDAD . ' ' . $item->TIPO . ' ' . $item->INTERFAZ,
                            'numero_serie' => $item->NUMERO_SERIE,
                            'numero_inventario' => $item->NUMERO_INVENTARIO,
                            'descripcion' => $item->MARCA . ' ' . $item->MODELO . ' - ' . $item->CAPACIDAD . ' ' . $item->TIPO
                        ];
                    }
                    break;
                    
                case 'Fuente de Poder':
                    $items = FuentesDePoder::find()
                        ->select(['MARCA', 'MODELO', 'POTENCIA', 'TIPO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])
                        ->distinct()
                        ->all();
                    foreach ($items as $item) {
                        $catalogo[] = [
                            'marca' => $item->MARCA,
                            'modelo' => $item->MODELO,
                            'especificaciones' => ($item->POTENCIA ?? '') . ' ' . ($item->TIPO ?? ''),
                            'numero_serie' => $item->NUMERO_SERIE,
                            'numero_inventario' => $item->NUMERO_INVENTARIO,
                            'descripcion' => $item->MARCA . ' ' . $item->MODELO . ' - ' . ($item->POTENCIA ?? '')
                        ];
                    }
                    break;
                    
                case 'Monitor':
                    $items = Monitor::find()
                        ->select(['MARCA', 'MODELO', 'TAMANIO', 'RESOLUCION', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])
                        ->distinct()
                        ->all();
                    foreach ($items as $item) {
                        $catalogo[] = [
                            'marca' => $item->MARCA,
                            'modelo' => $item->MODELO,
                            'especificaciones' => ($item->TAMANIO ?? '') . ' - ' . ($item->RESOLUCION ?? ''),
                            'numero_serie' => $item->NUMERO_SERIE,
                            'numero_inventario' => $item->NUMERO_INVENTARIO,
                            'descripcion' => $item->MARCA . ' ' . $item->MODELO . ' - ' . ($item->TAMANIO ?? '')
                        ];
                    }
                    break;
                    
                default:
                    // Para tipos sin catálogo específico, devolver lista vacía
                    break;
            }
            
            // Obtener marcas únicas para sugerencias
            $marcasUnicas = array_values(array_unique(array_column($catalogo, 'marca')));
            $modelosUnicos = array_values(array_unique(array_column($catalogo, 'modelo')));
            
            return [
                'success' => true,
                'catalogo' => $catalogo,
                'marcas' => $marcasUnicas,
                'modelos' => $modelosUnicos,
                'total' => count($catalogo)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener el catálogo: ' . $e->getMessage(),
                'catalogo' => [],
                'marcas' => [],
                'modelos' => []
            ];
        }
    }

    /**
     * Obtiene los equipos dados de baja o inactivos para seleccionar como origen
     */
    public function actionObtenerEquiposOrigen()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $equipos = [];
            
            // Equipos de cómputo inactivos o dados de baja
            $equiposComputo = Equipo::find()
                ->where(['like', 'Estado', 'Inactivo'])
                ->orWhere(['like', 'Estado', 'Baja'])
                ->orWhere(['like', 'Estado', 'Dañado'])
                ->all();
                
            foreach ($equiposComputo as $eq) {
                $equipos[] = [
                    'id' => 'EQ-' . $eq->idEQUIPO,
                    'tipo' => 'Equipo de Cómputo',
                    'descripcion' => $eq->MARCA . ' ' . $eq->MODELO . ' - ' . $eq->tipoequipo,
                    'numero_serie' => $eq->NUM_SERIE,
                    'numero_inventario' => $eq->NUM_INVENTARIO,
                    'estado' => $eq->Estado
                ];
            }
            
            // NoBreaks inactivos
            $nobreaks = Nobreak::find()
                ->where(['like', 'Estado', 'Inactivo'])
                ->orWhere(['like', 'Estado', 'Baja'])
                ->orWhere(['like', 'Estado', 'Dañado'])
                ->all();
                
            foreach ($nobreaks as $nb) {
                $equipos[] = [
                    'id' => 'NB-' . $nb->idNOBREAK,
                    'tipo' => 'NoBreak/UPS',
                    'descripcion' => $nb->MARCA . ' ' . $nb->MODELO . ' - ' . $nb->CAPACIDAD,
                    'numero_serie' => $nb->NUMERO_SERIE,
                    'numero_inventario' => $nb->NUMERO_INVENTARIO,
                    'estado' => $nb->Estado
                ];
            }
            
            // Impresoras inactivas
            $impresoras = Impresora::find()
                ->where(['like', 'Estado', 'Inactivo'])
                ->orWhere(['like', 'Estado', 'Baja'])
                ->orWhere(['like', 'Estado', 'Dañado'])
                ->all();
                
            foreach ($impresoras as $imp) {
                $equipos[] = [
                    'id' => 'IMP-' . $imp->idIMPRESORA,
                    'tipo' => 'Impresora',
                    'descripcion' => $imp->MARCA . ' ' . $imp->MODELO,
                    'numero_serie' => $imp->NUMERO_SERIE,
                    'numero_inventario' => $imp->NUMERO_INVENTARIO,
                    'estado' => $imp->Estado
                ];
            }
            
            return [
                'success' => true,
                'equipos' => $equipos,
                'total' => count($equipos)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'equipos' => []
            ];
        }
    }

    /**
     * Obtiene componentes del catálogo existente para sugerencias en reciclaje
     * Agrupa por tipo de pieza y devuelve marcas, modelos y especificaciones
     */
    public function actionCatalogoPiezasExistentes($tipo = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $catalogo = [];
            
            // Memoria RAM
            if (!$tipo || $tipo === 'Memoria RAM') {
                $rams = Ram::find()->select(['MARCA', 'MODELO', 'CAPACIDAD', 'TIPO_DDR', 'numero_serie', 'numero_inventario'])->distinct()->all();
                $catalogo['Memoria RAM'] = [];
                foreach ($rams as $ram) {
                    $catalogo['Memoria RAM'][] = [
                        'marca' => $ram->MARCA,
                        'modelo' => $ram->MODELO,
                        'especificaciones' => $ram->CAPACIDAD . ' ' . $ram->TIPO_DDR,
                        'numero_serie' => $ram->numero_serie,
                        'numero_inventario' => $ram->numero_inventario
                    ];
                }
            }
            
            // Procesadores
            if (!$tipo || $tipo === 'Procesador') {
                $procesadores = Procesador::find()->select(['MARCA', 'MODELO', 'FRECUENCIA_BASE', 'NUCLEOS', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Procesador'] = [];
                foreach ($procesadores as $proc) {
                    $catalogo['Procesador'][] = [
                        'marca' => $proc->MARCA,
                        'modelo' => $proc->MODELO,
                        'especificaciones' => $proc->FRECUENCIA_BASE . ' - ' . $proc->NUCLEOS . ' núcleos',
                        'numero_serie' => $proc->NUMERO_SERIE,
                        'numero_inventario' => $proc->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Almacenamiento (Disco Duro / SSD)
            if (!$tipo || $tipo === 'Disco Duro' || $tipo === 'SSD') {
                $almacenamientos = Almacenamiento::find()->select(['MARCA', 'MODELO', 'TIPO', 'CAPACIDAD', 'INTERFAZ', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Disco Duro'] = [];
                $catalogo['SSD'] = [];
                foreach ($almacenamientos as $alm) {
                    $item = [
                        'marca' => $alm->MARCA,
                        'modelo' => $alm->MODELO,
                        'especificaciones' => $alm->CAPACIDAD . ' ' . $alm->INTERFAZ,
                        'numero_serie' => $alm->NUMERO_SERIE,
                        'numero_inventario' => $alm->NUMERO_INVENTARIO
                    ];
                    if (stripos($alm->TIPO, 'SSD') !== false) {
                        $catalogo['SSD'][] = $item;
                    } else {
                        $catalogo['Disco Duro'][] = $item;
                    }
                }
            }
            
            // Fuentes de Poder
            if (!$tipo || $tipo === 'Fuente de Poder') {
                $fuentes = FuentesDePoder::find()->select(['MARCA', 'MODELO', 'POTENCIA', 'VOLTAJE', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Fuente de Poder'] = [];
                foreach ($fuentes as $fuente) {
                    $catalogo['Fuente de Poder'][] = [
                        'marca' => $fuente->MARCA,
                        'modelo' => $fuente->MODELO,
                        'especificaciones' => ($fuente->POTENCIA ?? '') . ' ' . ($fuente->VOLTAJE ?? ''),
                        'numero_serie' => $fuente->NUMERO_SERIE,
                        'numero_inventario' => $fuente->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Monitores
            if (!$tipo || $tipo === 'Monitor') {
                $monitores = Monitor::find()->select(['MARCA', 'MODELO', 'TAMANIO', 'RESOLUCION', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Monitor'] = [];
                foreach ($monitores as $mon) {
                    $catalogo['Monitor'][] = [
                        'marca' => $mon->MARCA,
                        'modelo' => $mon->MODELO,
                        'especificaciones' => ($mon->TAMANIO ?? '') . ' ' . ($mon->RESOLUCION ?? ''),
                        'numero_serie' => $mon->NUMERO_SERIE,
                        'numero_inventario' => $mon->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Baterías
            if (!$tipo || $tipo === 'Batería' || $tipo === 'Bateria') {
                $baterias = Bateria::find()->select(['MARCA', 'MODELO', 'TIPO', 'VOLTAJE', 'CAPACIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Batería'] = [];
                foreach ($baterias as $bat) {
                    $catalogo['Batería'][] = [
                        'marca' => $bat->MARCA,
                        'modelo' => $bat->MODELO,
                        'especificaciones' => ($bat->VOLTAJE ?? '') . ' ' . ($bat->CAPACIDAD ?? ''),
                        'numero_serie' => $bat->NUMERO_SERIE,
                        'numero_inventario' => $bat->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // NoBreak/UPS
            if (!$tipo || $tipo === 'NoBreak' || $tipo === 'UPS') {
                $nobreaks = Nobreak::find()->select(['MARCA', 'MODELO', 'CAPACIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['NoBreak'] = [];
                foreach ($nobreaks as $nb) {
                    $catalogo['NoBreak'][] = [
                        'marca' => $nb->MARCA,
                        'modelo' => $nb->MODELO,
                        'especificaciones' => $nb->CAPACIDAD ?? '',
                        'numero_serie' => $nb->NUMERO_SERIE,
                        'numero_inventario' => $nb->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Conectividad
            if (!$tipo || $tipo === 'Conectividad' || $tipo === 'Cable/Adaptador') {
                $conectividad = Conectividad::find()->select(['MARCA', 'MODELO', 'TIPO', 'CANTIDAD_PUERTOS', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Conectividad'] = [];
                foreach ($conectividad as $con) {
                    $catalogo['Conectividad'][] = [
                        'marca' => $con->MARCA,
                        'modelo' => $con->MODELO,
                        'especificaciones' => ($con->TIPO ?? '') . ' - ' . ($con->CANTIDAD_PUERTOS ?? '') . ' puertos',
                        'numero_serie' => $con->NUMERO_SERIE,
                        'numero_inventario' => $con->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Adaptadores
            if (!$tipo || $tipo === 'Adaptador' || $tipo === 'Cable/Adaptador') {
                $adaptadores = Adaptador::find()->select(['MARCA', 'MODELO', 'TIPO', 'VOLTAJE', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Adaptador'] = [];
                foreach ($adaptadores as $adp) {
                    $catalogo['Adaptador'][] = [
                        'marca' => $adp->MARCA,
                        'modelo' => $adp->MODELO,
                        'especificaciones' => ($adp->TIPO ?? '') . ' ' . ($adp->VOLTAJE ?? ''),
                        'numero_serie' => $adp->NUMERO_SERIE,
                        'numero_inventario' => $adp->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Equipo de Sonido
            if (!$tipo || $tipo === 'Equipo de Sonido' || $tipo === 'Bocinas') {
                $sonido = Sonido::find()->select(['MARCA', 'MODELO', 'TIPO', 'POTENCIA', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Equipo de Sonido'] = [];
                foreach ($sonido as $son) {
                    $catalogo['Equipo de Sonido'][] = [
                        'marca' => $son->MARCA,
                        'modelo' => $son->MODELO,
                        'especificaciones' => ($son->TIPO ?? '') . ' ' . ($son->POTENCIA ?? ''),
                        'numero_serie' => $son->NUMERO_SERIE,
                        'numero_inventario' => $son->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Micrófonos
            if (!$tipo || $tipo === 'Micrófono') {
                $microfonos = Microfono::find()->select(['MARCA', 'MODELO', 'TIPO', 'CONECTIVIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Micrófono'] = [];
                foreach ($microfonos as $mic) {
                    $catalogo['Micrófono'][] = [
                        'marca' => $mic->MARCA,
                        'modelo' => $mic->MODELO,
                        'especificaciones' => ($mic->TIPO ?? '') . ' - ' . ($mic->CONECTIVIDAD ?? ''),
                        'numero_serie' => $mic->NUMERO_SERIE,
                        'numero_inventario' => $mic->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Telefonía
            if (!$tipo || $tipo === 'Teléfono') {
                $telefonos = Telefonia::find()->select(['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Teléfono'] = [];
                foreach ($telefonos as $tel) {
                    $catalogo['Teléfono'][] = [
                        'marca' => $tel->MARCA,
                        'modelo' => $tel->MODELO,
                        'especificaciones' => '',
                        'numero_serie' => $tel->NUMERO_SERIE,
                        'numero_inventario' => $tel->NUMERO_INVENTARIO
                    ];
                }
            }
            
            // Video Vigilancia
            if (!$tipo || $tipo === 'Cámara' || $tipo === 'Video Vigilancia') {
                $camaras = VideoVigilancia::find()->select(['MARCA', 'MODELO', 'tipo_camara', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'])->distinct()->all();
                $catalogo['Cámara'] = [];
                foreach ($camaras as $cam) {
                    $catalogo['Cámara'][] = [
                        'marca' => $cam->MARCA,
                        'modelo' => $cam->MODELO,
                        'especificaciones' => $cam->tipo_camara ?? '',
                        'numero_serie' => $cam->NUMERO_SERIE,
                        'numero_inventario' => $cam->NUMERO_INVENTARIO
                    ];
                }
            }
            
            return [
                'success' => true,
                'catalogo' => $catalogo,
                'message' => 'Catálogo cargado correctamente'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'catalogo' => []
            ];
        }
    }

    /**
     * Verifica si un número de serie o inventario ya existe en el sistema
     */
    public function actionVerificarDuplicado()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $tipo = Yii::$app->request->post('tipo'); // 'serie' o 'inventario'
        $valor = Yii::$app->request->post('valor');
        $modelo = Yii::$app->request->post('modelo'); // 'Nobreak', 'Equipo', etc.
        $id = Yii::$app->request->post('id'); // ID del registro actual (para edición)
        
        if (empty($tipo) || empty($valor) || empty($modelo)) {
            return ['existe' => false, 'mensaje' => ''];
        }
        
        // Mapeo de modelos
        $modelos = [
            'Nobreak' => Nobreak::class,
            'Equipo' => Equipo::class,
            'Monitor' => Monitor::class,
            'Impresora' => Impresora::class,
            'Conectividad' => Conectividad::class,
            'Telefonia' => Telefonia::class,
            'VideoVigilancia' => VideoVigilancia::class,
            'Ram' => Ram::class,
            'Almacenamiento' => Almacenamiento::class,
            'Sonido' => Sonido::class,
            'Adaptador' => Adaptador::class,
            'Microfono' => Microfono::class,
            'Bateria' => Bateria::class,
            'Procesador' => Procesador::class,
            'FuentesDePoder' => FuentesDePoder::class,
        ];
        
        if (!isset($modelos[$modelo])) {
            return ['existe' => false, 'mensaje' => ''];
        }
        
        $modelClass = $modelos[$modelo];
        
        // Determinar el nombre del campo según el tipo y modelo
        if ($tipo === 'serie') {
            // Mapeo específico de campos para cada modelo
            if ($modelo === 'Equipo') {
                $campo = 'NUM_SERIE';
            } elseif (in_array($modelo, ['Ram', 'Almacenamiento', 'Procesador', 'Microfono', 'FuentesDePoder'])) {
                $campo = 'numero_serie';
            } else {
                $campo = 'NUMERO_SERIE';
            }
        } else {
            // Campo de inventario
            if ($modelo === 'Equipo') {
                $campo = 'NUM_INVENTARIO';
            } elseif (in_array($modelo, ['Ram', 'Almacenamiento', 'Procesador', 'Microfono', 'FuentesDePoder'])) {
                $campo = 'numero_inventario';
            } else {
                $campo = 'NUMERO_INVENTARIO';
            }
        }
        
        // Verificar si existe
        $query = $modelClass::find()->where([$campo => $valor]);
        
        // Si estamos editando, excluir el registro actual
        if (!empty($id)) {
            $primaryKey = $modelClass::primaryKey()[0];
            $query->andWhere(['!=', $primaryKey, $id]);
        }
        
        $existe = $query->exists();
        
        if ($existe) {
            $nombreModelo = $this->getNombreModelo($modelo);
            $registro = $query->one();
            
            // Obtener información del dispositivo duplicado
            $infoDispositivo = $this->obtenerInfoDispositivo($modelo, $registro);
            
            $mensaje = $tipo === 'serie' 
                ? "⚠️ Este número de serie ya está registrado en otro {$nombreModelo}."
                : "⚠️ Este número de inventario ya está registrado en otro {$nombreModelo}.";
            
            return [
                'existe' => true, 
                'mensaje' => $mensaje,
                'dispositivo' => $infoDispositivo
            ];
        }
        
        return ['existe' => false, 'mensaje' => '', 'dispositivo' => ''];
    }
    
    /**
     * Obtiene el nombre amigable del modelo
     */
    private function getNombreModelo($modelo)
    {
        $nombres = [
            'Nobreak' => 'No Break / UPS',
            'Equipo' => 'equipo de cómputo',
            'Monitor' => 'monitor',
            'Impresora' => 'impresora',
            'Conectividad' => 'dispositivo de conectividad',
            'Telefonia' => 'dispositivo de telefonía',
            'VideoVigilancia' => 'cámara de videovigilancia',
            'Ram' => 'memoria RAM',
            'Almacenamiento' => 'dispositivo de almacenamiento',
            'Sonido' => 'equipo de sonido',
            'Adaptador' => 'adaptador',
            'Microfono' => 'micrófono',
            'Bateria' => 'batería',
            'Procesador' => 'procesador',
            'FuentesDePoder' => 'fuente de poder',
        ];
        
        return $nombres[$modelo] ?? 'equipo';
    }
    
    /**
     * Obtiene información descriptiva del dispositivo duplicado
     */
    private function obtenerInfoDispositivo($modelo, $registro)
    {
        if (!$registro) {
            return 'Dispositivo no identificado';
        }
        
        switch ($modelo) {
            case 'Equipo':
                return sprintf(
                    '%s - %s (Serie: %s, Inventario: %s)',
                    $registro->MARCA ?? 'Sin marca',
                    $registro->MODELO ?? 'Sin modelo',
                    $registro->NUM_SERIE ?? 'N/A',
                    $registro->NUM_INVENTARIO ?? 'N/A'
                );
                
            case 'Nobreak':
            case 'Monitor':
            case 'Impresora':
            case 'Conectividad':
            case 'Telefonia':
            case 'VideoVigilancia':
            case 'Sonido':
            case 'Adaptador':
            case 'Microfono':
            case 'Bateria':
            case 'FuentesDePoder':
                return sprintf(
                    '%s - %s (Serie: %s, Inventario: %s)',
                    $registro->MARCA ?? 'Sin marca',
                    $registro->MODELO ?? 'Sin modelo',
                    $registro->NUMERO_SERIE ?? 'N/A',
                    $registro->NUMERO_INVENTARIO ?? 'N/A'
                );
                
            case 'Procesador':
            case 'Ram':
            case 'Almacenamiento':
                return sprintf(
                    '%s - %s (Serie: %s, Inventario: %s)',
                    $registro->marca ?? 'Sin marca',
                    $registro->modelo ?? 'Sin modelo',
                    $registro->numero_serie ?? 'N/A',
                    $registro->numero_inventario ?? 'N/A'
                );
                
            default:
                return 'Dispositivo registrado en el sistema';
        }
    }

    /**
     * Obtiene todos los dispositivos con status BAJA de todas las categorías
     */
    public function actionObtenerDispositivosBaja()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $dispositivos = [];
            $contadores = [
                'equipo' => 0,
                'monitor' => 0,
                'impresora' => 0,
                'telefonia' => 0,
                'videovigilancia' => 0,
                'conectividad' => 0,
                'bateria' => 0,
                'nobreak' => 0
            ];
            
            // 1. Equipos de Cómputo
            $equipos = Equipo::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($equipos as $eq) {
                $dispositivos[] = [
                    'id' => $eq->idEQUIPO,
                    'categoria' => 'Equipo',
                    'descripcion' => $eq->tipoequipo ?? 'Equipo de Cómputo',
                    'marca' => $eq->MARCA ?? 'N/A',
                    'modelo' => $eq->MODELO ?? 'N/A',
                    'numero_serie' => $eq->NUM_SERIE ?? 'N/A',
                    'fecha_baja' => $eq->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $eq->descripcion ?? 'Sin detalles',
                ];
                $contadores['equipo']++;
            }
            
            // 2. Monitores
            $monitores = Monitor::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($monitores as $mon) {
                $dispositivos[] = [
                    'id' => $mon->idMONITOR,
                    'categoria' => 'Monitor',
                    'descripcion' => ($mon->TAMANIO ?? 'N/A') . '" - ' . ($mon->TIPO_PANTALLA ?? 'Monitor'),
                    'marca' => $mon->MARCA ?? 'N/A',
                    'modelo' => $mon->MODELO ?? 'N/A',
                    'numero_serie' => $mon->NUMERO_SERIE ?? 'N/A',
                    'fecha_baja' => $mon->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $mon->DESCRIPCION ?? 'Sin detalles',
                ];
                $contadores['monitor']++;
            }
            
            // 3. Impresoras
            $impresoras = Impresora::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($impresoras as $imp) {
                $dispositivos[] = [
                    'id' => $imp->idIMPRESORA,
                    'categoria' => 'Impresora',
                    'descripcion' => ($imp->TIPO ?? 'Impresora'),
                    'marca' => $imp->MARCA ?? 'N/A',
                    'modelo' => $imp->MODELO ?? 'N/A',
                    'numero_serie' => $imp->NUMERO_SERIE ?? 'N/A',
                    'fecha_baja' => $imp->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $imp->DESCRIPCION ?? 'Sin detalles',
                ];
                $contadores['impresora']++;
            }
            
            // 4. Telefonía
            $telefonos = Telefonia::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($telefonos as $tel) {
                $dispositivos[] = [
                    'id' => $tel->idTELEFONIA,
                    'categoria' => 'Telefonia',
                    'descripcion' => ($tel->TIPO ?? 'Teléfono'),
                    'marca' => $tel->MARCA ?? 'N/A',
                    'modelo' => $tel->MODELO ?? 'N/A',
                    'numero_serie' => $tel->NUMERO_SERIE ?? 'N/A',
                    'fecha_baja' => $tel->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $tel->DESCRIPCION ?? 'Sin detalles',
                ];
                $contadores['telefonia']++;
            }
            
            // 5. Video Vigilancia
            $camaras = VideoVigilancia::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($camaras as $cam) {
                $dispositivos[] = [
                    'id' => $cam->idVIDEOVIGILANCIA,
                    'categoria' => 'VideoVigilancia',
                    'descripcion' => ($cam->TIPO ?? 'Cámara'),
                    'marca' => $cam->MARCA ?? 'N/A',
                    'modelo' => $cam->MODELO ?? 'N/A',
                    'numero_serie' => $cam->NUMERO_SERIE ?? 'N/A',
                    'fecha_baja' => $cam->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $cam->DESCRIPCION ?? 'Sin detalles',
                ];
                $contadores['videovigilancia']++;
            }
            
            // 6. Conectividad
            $conectividad = Conectividad::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($conectividad as $con) {
                $dispositivos[] = [
                    'id' => $con->idCONECTIVIDAD,
                    'categoria' => 'Conectividad',
                    'descripcion' => ($con->TIPO ?? 'Dispositivo de Red'),
                    'marca' => $con->MARCA ?? 'N/A',
                    'modelo' => $con->MODELO ?? 'N/A',
                    'numero_serie' => $con->NUMERO_SERIE ?? 'N/A',
                    'fecha_baja' => $con->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $con->DESCRIPCION ?? 'Sin detalles',
                ];
                $contadores['conectividad']++;
            }
            
            // 7. Baterías
            $baterias = Bateria::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($baterias as $bat) {
                $dispositivos[] = [
                    'id' => $bat->idBATERIA,
                    'categoria' => 'Bateria',
                    'descripcion' => ($bat->TIPO ?? 'Batería') . ' - ' . ($bat->CAPACIDAD ?? 'N/A'),
                    'marca' => $bat->MARCA ?? 'N/A',
                    'modelo' => $bat->MODELO ?? 'N/A',
                    'numero_serie' => $bat->NUMERO_SERIE ?? 'N/A',
                    'fecha_baja' => $bat->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $bat->DESCRIPCION ?? 'Sin detalles',
                ];
                $contadores['bateria']++;
            }
            
            // 8. No Break
            $nobreaks = Nobreak::find()->where(['Estado' => 'BAJA'])->all();
            foreach ($nobreaks as $nb) {
                $dispositivos[] = [
                    'id' => $nb->idNOBREAK,
                    'categoria' => 'NoBreak',
                    'descripcion' => 'No Break - ' . ($nb->CAPACIDAD ?? 'N/A'),
                    'marca' => $nb->MARCA ?? 'N/A',
                    'modelo' => $nb->MODELO ?? 'N/A',
                    'numero_serie' => $nb->NUMERO_SERIE ?? 'N/A',
                    'fecha_baja' => $nb->FECHA_BAJA ?? date('Y-m-d'),
                    'detalles' => $nb->DESCRIPCION ?? 'Sin detalles',
                ];
                $contadores['nobreak']++;
            }
            
            // Ordenar por fecha de baja descendente
            usort($dispositivos, function($a, $b) {
                return strtotime($b['fecha_baja']) - strtotime($a['fecha_baja']);
            });
            
            return [
                'success' => true,
                'data' => $dispositivos,
                'total' => count($dispositivos),
                'contadores' => $contadores,
                'message' => 'Dispositivos dados de baja obtenidos correctamente'
            ];
            
        } catch (Exception $e) {
            Yii::error("Error obteniendo dispositivos de baja: " . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener dispositivos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene los detalles de un dispositivo dado de baja específico
     */
    public function actionDetalleDispositivoBaja()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $categoria = Yii::$app->request->get('categoria');
        $id = Yii::$app->request->get('id');
        
        if (!$categoria || !$id) {
            return [
                'success' => false,
                'message' => 'Parámetros incompletos'
            ];
        }
        
        try {
            $dispositivo = null;
            $data = [];
            
            switch (strtolower($categoria)) {
                case 'equipo':
                    $dispositivo = Equipo::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'Equipo de Cómputo',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUM_SERIE,
                            'descripcion' => $dispositivo->tipoequipo,
                            'especificaciones' => "Procesador: {$dispositivo->PROCESADOR}, RAM: {$dispositivo->RAM}, Almacenamiento: {$dispositivo->DISCO_DURO}",
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
                    
                case 'monitor':
                    $dispositivo = Monitor::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'Monitor',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUMERO_SERIE,
                            'descripcion' => "{$dispositivo->TAMANIO}\" {$dispositivo->TIPO_PANTALLA}",
                            'especificaciones' => "Resolución: {$dispositivo->RESOLUCION}, Conexión: {$dispositivo->CONEXION}",
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
                    
                case 'impresora':
                    $dispositivo = Impresora::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'Impresora',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUMERO_SERIE,
                            'descripcion' => $dispositivo->TIPO,
                            'especificaciones' => "Conexión: {$dispositivo->CONEXION}",
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
                    
                case 'telefonia':
                    $dispositivo = Telefonia::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'Telefonía',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUMERO_SERIE,
                            'descripcion' => $dispositivo->TIPO,
                            'especificaciones' => $dispositivo->CARACTERISTICAS ?? 'N/A',
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
                    
                case 'videovigilancia':
                    $dispositivo = VideoVigilancia::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'Video Vigilancia',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUMERO_SERIE,
                            'descripcion' => $dispositivo->TIPO,
                            'especificaciones' => "Resolución: {$dispositivo->RESOLUCION}",
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
                    
                case 'conectividad':
                    $dispositivo = Conectividad::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'Conectividad',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUMERO_SERIE,
                            'descripcion' => $dispositivo->TIPO,
                            'especificaciones' => $dispositivo->CARACTERISTICAS ?? 'N/A',
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
                    
                case 'bateria':
                    $dispositivo = Bateria::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'Batería',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUMERO_SERIE,
                            'descripcion' => "{$dispositivo->TIPO} - {$dispositivo->CAPACIDAD}",
                            'especificaciones' => "Voltaje: {$dispositivo->VOLTAJE}",
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
                    
                case 'nobreak':
                    $dispositivo = Nobreak::findOne($id);
                    if ($dispositivo) {
                        $data = [
                            'categoria' => 'No Break',
                            'marca' => $dispositivo->MARCA,
                            'modelo' => $dispositivo->MODELO,
                            'numero_serie' => $dispositivo->NUMERO_SERIE,
                            'descripcion' => "No Break - {$dispositivo->CAPACIDAD}",
                            'especificaciones' => "Capacidad: {$dispositivo->CAPACIDAD}",
                            'fecha_baja' => $dispositivo->FECHA_BAJA,
                            'motivo_baja' => $dispositivo->MOTIVO_BAJA ?? 'No especificado',
                            'responsable' => $dispositivo->NOMBRE_USUARIO ?? 'N/A',
                            'observaciones' => $dispositivo->OBSERVACIONES ?? ''
                        ];
                    }
                    break;
            }
            
            if ($dispositivo) {
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Dispositivo no encontrado'
                ];
            }
            
        } catch (Exception $e) {
            Yii::error("Error obteniendo detalle de dispositivo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al obtener detalles: ' . $e->getMessage()
            ];
        }
    }
}