<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "monitor".
 *
 * @property int $idMonitor
 * @property string $MARCA
 * @property string $MODELO
 * @property string $RESOLUCION
 * @property string $TIPO_PANTALLA
 * @property int $FRECUENCIA_HZ
 * @property string $ENTRADAS_VIDEO
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string $EMISION_INVENTARIO
 * @property string $DESCRIPCION
 * @property string $ESTADO
 * @property string $ubicacion_edificio
 * @property string $ubicacion_detalle
 * @property string $TAMANIO
 * @property string $fecha_creacion
 * @property string $fecha_ultima_edicion
 * @property string $ultimo_editor
 */
class Monitor extends ActiveRecord
{
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_BAJA = 'BAJA';

    /**
     * Define explicitly that ESTADO is a safe attribute
     */
    public function __get($name)
    {
        if ($name === 'ESTADO') {
            return $this->getAttribute('ESTADO');
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if ($name === 'ESTADO') {
            $this->setAttribute('ESTADO', $value);
            return;
        }
        parent::__set($name, $value);
    }

    public function __isset($name)
    {
        if ($name === 'ESTADO') {
            return true;
        }
        return parent::__isset($name);
    }

    public static function tableName()
    {
        return 'monitor';
    }

    public static function primaryKey()
    {
        return ['idMonitor'];
    }

    public function rules()
    {
        return [
            [['MARCA', 'MODELO'], 'required'],
            [['TAMANIO', 'RESOLUCION', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'ESTADO', 'EMISION_INVENTARIO', 'TIPO_PANTALLA', 'FRECUENCIA_HZ', 'ENTRADAS_VIDEO', 'DESCRIPCION', 'ubicacion_edificio', 'ubicacion_detalle'], 'safe'],
            [['EMISION_INVENTARIO'], 'date', 'format' => 'php:Y-m-d'],
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['TAMANIO'], 'string', 'max' => 20],
            [['RESOLUCION'], 'string', 'max' => 30],
            [['TIPO_PANTALLA'], 'string', 'max' => 25],
            [['FRECUENCIA_HZ'], 'string', 'max' => 10],
            [['ENTRADAS_VIDEO'], 'string', 'max' => 100],
            [['DESCRIPCION'], 'string', 'max' => 100],
            [['ESTADO'], 'string', 'max' => 100],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otro monitor.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otro monitor.'],
            [['ESTADO'], 'in', 'range' => [
                self::ESTADO_ACTIVO, 
                self::ESTADO_INACTIVO, 
                self::ESTADO_DANADO, 
                self::ESTADO_MANTENIMIENTO,
                self::ESTADO_BAJA
            ]],
            [['ESTADO'], 'default', 'value' => self::ESTADO_ACTIVO],
            [['EMISION_INVENTARIO'], 'default', 'value' => date('Y-m-d')],
            [['ultimo_editor'], 'default', 'value' => 'Sistema'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idMonitor' => 'ID Monitor',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TAMANIO' => 'Tamaño',
            'RESOLUCION' => 'Resolución',
            'TIPO_PANTALLA' => 'Tipo de Pantalla',
            'FRECUENCIA_HZ' => 'Frecuencia (Hz)',
            'ENTRADAS_VIDEO' => 'Entradas de Video',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'EMISION_INVENTARIO' => 'Emisión de Inventario',
            'DESCRIPCION' => 'Descripción',
            'ESTADO' => 'Estado',
            'ubicacion_edificio' => 'Ubicación Edificio',
            'ubicacion_detalle' => 'Detalle de Ubicación',
            'fecha_creacion' => 'Fecha de Creación',
            'fecha_ultima_edicion' => 'Última Edición',
            'ultimo_editor' => 'Último Editor',
        ];
    }

    /**
     * Comportamientos del modelo
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'fecha_creacion',
                'updatedAtAttribute' => 'fecha_ultima_edicion',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Establece valores por defecto
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->ESTADO = self::ESTADO_ACTIVO;
            $this->EMISION_INVENTARIO = date('Y-m-d');
        }
    }

    /**
     * Antes de guardar, asegurar valores por defecto y configurar auditoría
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Configurar el editor actual
            $this->ultimo_editor = $this->getCurrentUser();
            
            // Completar campos obligatorios automáticamente para formulario simplificado
            if (empty($this->TAMANIO)) {
                $this->TAMANIO = '24"';
            }
            if (empty($this->RESOLUCION)) {
                $this->RESOLUCION = '1920x1080';
            }
            if (empty($this->NUMERO_SERIE)) {
                $this->NUMERO_SERIE = 'MON-' . date('YmdHis');
            }
            if (empty($this->NUMERO_INVENTARIO)) {
                $this->NUMERO_INVENTARIO = 'MON-' . date('YmdHis') . '-INV';
            }
            if (empty($this->ESTADO)) {
                $this->ESTADO = self::ESTADO_INACTIVO;
            }
            if (empty($this->EMISION_INVENTARIO)) {
                $this->EMISION_INVENTARIO = date('Y-m-d');
            }
            if (empty($this->ubicacion_detalle)) {
                $this->ubicacion_detalle = 'Catálogo';
            }
            
            return true;
        }
        return false;
    }

    /**
     * Obtiene el usuario actual que está logueado
     */
    private function getCurrentUser()
    {
        // Verificar si Yii está inicializado y hay un usuario logueado
        if (isset(\Yii::$app) && !\Yii::$app->user->isGuest) {
            // Obtener el usuario logueado actual
            $user = \Yii::$app->user->identity;
            if ($user && isset($user->username)) {
                return $user->username;
            }
        }
        
        // Si no hay usuario logueado, usar 'Sistema' como fallback
        return 'Sistema';
    }

    /**
     * Obtiene información completa del usuario que editó
     */
    public function getInfoUsuarioEditor()
    {
        if (empty($this->ultimo_editor) || $this->ultimo_editor === 'Sistema') {
            return [
                'username' => $this->ultimo_editor ?? 'Sistema',
                'email' => null,
                'id' => null,
                'display_name' => $this->ultimo_editor ?? 'Sistema'
            ];
        }

        // Buscar el usuario usando la relación
        $usuario = $this->usuarioEditor;
        if ($usuario) {
            return [
                'username' => $usuario->username,
                'email' => $usuario->email,
                'id' => $usuario->id,
                'display_name' => $usuario->username . ' (' . $usuario->email . ')'
            ];
        }

        return [
            'username' => $this->ultimo_editor,
            'email' => null,
            'id' => null,
            'display_name' => $this->ultimo_editor
        ];
    }

    /**
     * Relación con la tabla user para obtener datos del editor
     */
    public function getUsuarioEditor()
    {
        return $this->hasOne(\common\models\User::class, ['username' => 'ultimo_editor']);
    }

    /**
     * Obtiene los días que lleva activo el monitor desde la emisión
     * @return int
     */
    public function getDiasActivo()
    {
        if (empty($this->EMISION_INVENTARIO)) {
            return 0;
        }
        
        try {
            // Limpiar y validar la fecha
            $fechaStr = trim($this->EMISION_INVENTARIO);
            if (empty($fechaStr)) {
                return 0;
            }
            
            $fechaEmision = new \DateTime($fechaStr);
            $fechaActual = new \DateTime();
            
            // Calcular diferencia en días
            $diferenciaMilisegundos = $fechaActual->getTimestamp() - $fechaEmision->getTimestamp();
            $dias = floor($diferenciaMilisegundos / (60 * 60 * 24));
            
            // Verificar que la fecha no esté en el futuro
            if ($dias < 0) {
                return 0;
            }
            
            return $dias;
            
        } catch (\Exception $e) {
            // Log del error para debugging
            error_log("Error calculando días activo para monitor {$this->idMonitor}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene los años que lleva activo el monitor desde la emisión
     * @return float
     */
    public function getAnosActivo()
    {
        $dias = $this->getDiasActivo();
        if ($dias == 0) {
            return 0;
        }
        // Usar exactamente la misma división que JavaScript: 365.25
        return round($dias / 365.25, 2);
    }

    /**
     * Obtiene solo el texto de años activo
     * @return string
     */
    public function getAnosActivoTexto()
    {
        $dias = $this->getDiasActivo();
        
        if ($dias == 0) {
            return 'Sin fecha';
        }
        
        $anos = $this->getAnosActivo();
        
        if ($anos < 1) {
            return 'Menos de 1 año';
        } else if ($anos == 1) {
            return '1 año';
        } else {
            return sprintf('%.1f años', $anos);
        }
    }

    /**
     * Obtiene información detallada del último editor
     */
    public function getInfoUltimaEdicion()
    {
        if (empty($this->fecha_ultima_edicion)) {
            return 'Sin ediciones registradas';
        }
        
        $userInfo = $this->getInfoUsuarioEditor();
        
        $fecha = new \DateTime($this->fecha_ultima_edicion);
        $ahora = new \DateTime();
        $diferencia = $ahora->diff($fecha);
        
        $tiempo = '';
        if ($diferencia->days > 0) {
            $tiempo = $diferencia->days . ' día' . ($diferencia->days > 1 ? 's' : '');
        } elseif ($diferencia->h > 0) {
            $tiempo = $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
        } else {
            $tiempo = $diferencia->i . ' minuto' . ($diferencia->i > 1 ? 's' : '');
        }
        
        return "Editado por {$userInfo['display_name']} hace {$tiempo}";
    }

    /**
     * Obtiene las marcas disponibles
     */
    public static function getMarcas()
    {
        return [
            'Samsung' => 'Samsung',
            'LG' => 'LG',
            'Dell' => 'Dell',
            'HP' => 'HP',
            'ASUS' => 'ASUS',
            'Acer' => 'Acer',
            'AOC' => 'AOC',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene los tamaños disponibles
     */
    public static function getTamanios()
    {
        return [
            '19"' => '19"',
            '21"' => '21"',
            '22"' => '22"',
            '24"' => '24"',
            '27"' => '27"',
            '32"' => '32"',
            'Otro' => 'Otro'
        ];
    }

    /**
     * Obtiene las resoluciones disponibles
     */
    public static function getResoluciones()
    {
        return [
            '1366x768' => '1366x768',
            '1920x1080' => '1920x1080 (Full HD)',
            '2560x1440' => '2560x1440 (2K)',
            '3840x2160' => '3840x2160 (4K)',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene tipos de pantalla
     */
    public static function getTiposPantalla()
    {
        return [
            'LCD' => 'LCD',
            'LED' => 'LED',
            'OLED' => 'OLED',
            'Otro' => 'Otro'
        ];
    }

    /**
     * Obtiene frecuencias comunes
     */
    public static function getFrecuencias()
    {
        return [
            '60Hz' => '60Hz',
            '75Hz' => '75Hz',
            '144Hz' => '144Hz',
            '165Hz' => '165Hz',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene entradas de video
     */
    public static function getEntradasVideo()
    {
        return [
            'HDMI' => 'HDMI',
            'VGA' => 'VGA',
            'DisplayPort' => 'DisplayPort',
            'DVI' => 'DVI',
            'USB-C' => 'USB-C',
            'Múltiples' => 'Múltiples'
        ];
    }

    public static function getEstados()
    {
        return [
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_INACTIVO => 'Inactivo(Sin Asignar)',
            self::ESTADO_DANADO => 'dañado(Proceso de baja)',
            self::ESTADO_MANTENIMIENTO => 'En Mantenimiento',
            self::ESTADO_BAJA => 'BAJA',
        ];
    }

    /**
     * Obtener equipos con estado dañado (proceso de baja)
     */
    public static function getEquiposDanados()
    {
        return self::find()->where(['ESTADO' => 'dañado(Proceso de baja)'])->all();
    }

    /**
     * Contar equipos con estado dañado (proceso de baja)
     */
    public static function countEquiposDanados()
    {
        return self::find()->where(['ESTADO' => 'dañado(Proceso de baja)'])->count();
    }

    /**
     * Obtiene los edificios disponibles (A-U)
     * @return array
     */
    public static function getEdificios()
    {
        $edificios = [];
        foreach (range('A', 'U') as $letra) {
            $edificios["Edificio $letra"] = "Edificio $letra";
        }
        return $edificios;
    }
}