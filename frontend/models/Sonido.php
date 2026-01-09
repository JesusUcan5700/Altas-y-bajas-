<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * This is the model class for table "equipo_sonido".
 *
 * @property int $idSonido
 * @property string $MARCA
 * @property string $MODELO
 * @property string $TIPO
 * @property string|null $POTENCIA
 * @property string|null $CONEXIONES
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string|null $DESCRIPCION
 * @property string $ESTADO
 * @property string $FECHA
 * @property string|null $ubicacion_edificio
 * @property string|null $ubicacion_detalle
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $ultimo_editor
 *
 * @property User $ultimoEditor
 */
class Sonido extends \yii\db\ActiveRecord
{
    // Constantes de estados estandarizados
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DAÑADO = 'dañado(Proceso de baja)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_BAJA = 'BAJA';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'fecha_creacion',
                'updatedAtAttribute' => 'fecha_ultima_edicion',
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
                'skipUpdateOnClean' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Guardar el usuario que está editando
            if (!Yii::$app->user->isGuest) {
                $this->ultimo_editor = Yii::$app->user->identity->username;
            } else {
                $this->ultimo_editor = 'Sistema';
            }
            
            // Para inserciones, asegurar que fecha_creacion se establezca
            if ($insert && empty($this->fecha_creacion)) {
                $this->fecha_creacion = date('Y-m-d H:i:s');
            }
            
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'equipo_sonido';
    }

    /**
     * Forzar la regeneración del esquema de la tabla
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        // Forzar limpieza de caché de esquema si existe
        if (method_exists($this->getDb()->getSchema(), 'refresh')) {
            $this->getDb()->getSchema()->refresh();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Campos requeridos
            [['MARCA', 'MODELO', 'TIPO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'ESTADO', 'FECHA'], 'required'],
            
            // Validación de fecha solo para FECHA (no para campos de auditoría)
            [['FECHA'], 'date', 'format' => 'php:Y-m-d'],
            
            // Validaciones de longitud
            [['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['TIPO'], 'string', 'max' => 30],
            [['POTENCIA'], 'string', 'max' => 20],
            [['CONEXIONES'], 'string', 'max' => 100],
            [['DESCRIPCION'], 'string', 'max' => 100],
            [['ESTADO'], 'string', 'max' => 100],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255],
            [['ultimo_editor'], 'string', 'max' => 100],
            
            // Validación de estados permitidos
            [['ESTADO'], 'in', 'range' => [
                self::ESTADO_ACTIVO,
                self::ESTADO_INACTIVO,
                self::ESTADO_DAÑADO,
                self::ESTADO_MANTENIMIENTO,
                self::ESTADO_BAJA
            ]],
            
            // Campos de auditoría - solo marcados como seguros, sin validación
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            
            // Otros campos opcionales
            [['POTENCIA', 'CONEXIONES', 'DESCRIPCION', 'ubicacion_edificio', 'ubicacion_detalle'], 'safe'],
            
            // Validaciones de unicidad
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otro equipo de sonido.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otro equipo de sonido.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idSonido' => 'ID',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TIPO' => 'Tipo',
            'POTENCIA' => 'Potencia',
            'CONEXIONES' => 'Conexiones',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'DESCRIPCION' => 'Descripción',
            'ESTADO' => 'Estado',
            'FECHA' => 'Fecha',
            'ubicacion_edificio' => 'Ubicación Edificio',
            'ubicacion_detalle' => 'Detalle de Ubicación',
            'fecha_creacion' => 'Fecha Creación',
            'fecha_ultima_edicion' => 'Fecha Última Edición',
            'ultimo_editor' => 'Último Editor',
        ];
    }

    /**
     * Relación con el usuario que editó por última vez
     */
    public function getUltimoEditor()
    {
        return $this->hasOne(User::class, ['username' => 'ultimo_editor']);
    }

    /**
     * Obtener información del último editor
     */
    public function getInfoUltimoEditor()
    {
        if ($this->ultimoEditor) {
            return $this->ultimoEditor->email . ' (' . $this->ultimoEditor->username . ')';
        }
        return $this->ultimo_editor ?: 'Sistema';
    }

    /**
     * Calcular tiempo activo desde la fecha establecida por el usuario
     */
    public function getTiempoActivo()
    {
        // Priorizar el campo FECHA (establecido por el usuario) sobre fecha_creacion
        $fechaBase = $this->FECHA ?: $this->fecha_creacion;
        
        if (!$fechaBase) {
            return 'No disponible';
        }
        
        $fechaInicio = new \DateTime($fechaBase);
        $ahora = new \DateTime();
        $diferencia = $ahora->diff($fechaInicio);
        
        if ($diferencia->days > 0) {
            return $diferencia->days . ($diferencia->days == 1 ? ' día' : ' días');
        } elseif ($diferencia->h > 0) {
            return $diferencia->h . ($diferencia->h == 1 ? ' hora' : ' horas');
        } elseif ($diferencia->i > 0) {
            return $diferencia->i . ($diferencia->i == 1 ? ' minuto' : ' minutos');
        } else {
            return 'Menos de 1 minuto';
        }
    }

    /**
     * Calcular tiempo desde la última edición
     */
    public function getTiempoUltimaEdicion()
    {
        if (!$this->fecha_ultima_edicion) {
            return 'No disponible';
        }
        
        $fechaEdicion = new \DateTime($this->fecha_ultima_edicion);
        $ahora = new \DateTime();
        $diferencia = $ahora->diff($fechaEdicion);
        
        if ($diferencia->days > 0) {
            return 'Hace ' . $diferencia->days . ' días';
        } elseif ($diferencia->h > 0) {
            return 'Hace ' . $diferencia->h . ' horas';
        } elseif ($diferencia->i > 0) {
            return 'Hace ' . $diferencia->i . ' minutos';
        } else {
            return 'Hace un momento';
        }
    }

    /**
     * Establece valores por defecto
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->ESTADO = self::ESTADO_ACTIVO;
            $this->FECHA = date('Y-m-d');
        }
    }

    /**
     * Obtiene las marcas disponibles
     */
    public static function getMarcas()
    {
        return [
            'Yamaha' => 'Yamaha',
            'Sony' => 'Sony',
            'Bose' => 'Bose',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene los tipos de equipo disponibles (SIN MICRÓFONO)
     */
    public static function getTipos()
    {
        return [
            'Bocina' => 'Bocina',
            'Amplificador' => 'Amplificador',
            'Micrófono' => 'Micrófono',
            'Otro' => 'Otro',
        ];
    }

    /**
     * Obtiene los estados disponibles estandarizados
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_INACTIVO => 'Inactivo(Sin Asignar)',
            self::ESTADO_DAÑADO => 'dañado(Proceso de baja)',
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
     * Obtiene tipos de conexiones comunes
     */
    public static function getConexiones()
    {
        return [
            'Bluetooth' => 'Bluetooth',
            'Cable' => 'Cable',
            'WiFi' => 'WiFi',
            'Otro' => 'Otro',
        ];
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