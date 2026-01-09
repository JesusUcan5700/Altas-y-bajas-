<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "adaptadores".
 *
 * @property int $idAdaptador
 * @property string $MARCA
 * @property string $MODELO
 * @property string $NUMERO_SERIE
 * @property string $TIPO
 * @property string $ENTRADA
 * @property string $SALIDA
 * @property string $VOLTAJE
 * @property string $AMPERAJE
 * @property string $POTENCIA_WATTS
 * @property string $COMPATIBILIDAD
 * @property string $ESTADO
 * @property string $NUMERO_INVENTARIO
 * @property string $EMISION_INVENTARIO
 * @property string $DESCRIPCION
 * @property string $ubicacion_edificio
 * @property string $ubicacion_detalle
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $ultimo_editor
 */
class Adaptador extends \yii\db\ActiveRecord
{
    // Constantes para estados estandarizados
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_BAJA = 'BAJA';

    public static function tableName()
    {
        return 'adaptadores';
    }

    public function rules()
    {
        return [
            [['MARCA', 'MODELO', 'TIPO', 'ESTADO', 'NUMERO_INVENTARIO', 'EMISION_INVENTARIO'], 'required'],
            [['NUMERO_SERIE', 'ENTRADA', 'SALIDA', 'VOLTAJE', 'AMPERAJE', 'POTENCIA_WATTS', 'COMPATIBILIDAD', 'DESCRIPCION', 'ubicacion_edificio', 'ubicacion_detalle'], 'safe'],
            [['EMISION_INVENTARIO', 'fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['MARCA', 'MODELO', 'TIPO', 'NUMERO_SERIE', 'ENTRADA', 'SALIDA'], 'string', 'max' => 45],
            [['VOLTAJE', 'AMPERAJE', 'POTENCIA_WATTS'], 'string', 'max' => 20],
            [['COMPATIBILIDAD', 'DESCRIPCION'], 'string', 'max' => 100],
            [['ESTADO'], 'string', 'max' => 100],
            [['NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['ubicacion_edificio'], 'string', 'max' => 100],
            [['ubicacion_detalle'], 'string', 'max' => 255],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['ESTADO'], 'in', 'range' => [
                self::ESTADO_ACTIVO, 
                self::ESTADO_INACTIVO, 
                self::ESTADO_DANADO, 
                self::ESTADO_MANTENIMIENTO,
                self::ESTADO_BAJA
            ]],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otro adaptador.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otro adaptador.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'idAdaptador' => 'ID',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'NUMERO_SERIE' => 'Número de Serie',
            'TIPO' => 'Tipo',
            'ENTRADA' => 'Entrada',
            'SALIDA' => 'Salida',
            'VOLTAJE' => 'Voltaje',
            'AMPERAJE' => 'Amperaje',
            'POTENCIA_WATTS' => 'Potencia (Watts)',
            'COMPATIBILIDAD' => 'Compatibilidad',
            'ESTADO' => 'Estado',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'EMISION_INVENTARIO' => 'Fecha de Emisión de Inventario',
            'DESCRIPCION' => 'Descripción',
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
     * Antes de guardar, establecer el usuario editor
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->ultimo_editor = $this->getCurrentUser();
            return true;
        }
        return false;
    }

    /**
     * Obtener el usuario actual
     */
    private function getCurrentUser()
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->user->identity->username ?? 'Usuario';
        }
        return 'Sistema';
    }

    /**
     * Obtener información del usuario editor
     */
    public function getInfoUsuarioEditor()
    {
        if (empty($this->ultimo_editor)) {
            return 'No especificado';
        }
        
        // Si es 'Sistema', retornar tal como está
        if ($this->ultimo_editor === 'Sistema') {
            return 'Sistema';
        }
        
        // Buscar información del usuario usando la relación
        $usuario = $this->usuarioEditor;
        if ($usuario) {
            return $this->ultimo_editor . ' (' . ($usuario->email ?? 'Sin email') . ')';
        }
        
        return $this->ultimo_editor;
    }

    /**
     * Relación con la tabla user para obtener datos del editor
     */
    public function getUsuarioEditor()
    {
        return $this->hasOne(\common\models\User::class, ['username' => 'ultimo_editor']);
    }

    /**
     * Calcular días activos desde la emisión del inventario
     */
    public function getDiasActivo()
    {
        if (empty($this->EMISION_INVENTARIO)) {
            return 0;
        }
        
        try {
            $fechaEmision = new \DateTime($this->EMISION_INVENTARIO);
            $fechaActual = new \DateTime();
            $diferencia = $fechaActual->diff($fechaEmision);
            return $diferencia->days;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calcular años activos desde la emisión del inventario
     */
    public function getAnosActivo()
    {
        $dias = $this->getDiasActivo();
        return round($dias / 365.25, 1); // 365.25 para considerar años bisiestos
    }

    /**
     * Obtener texto formateado del tiempo activo
     */
    public function getTiempoActivoFormateado()
    {
        $dias = $this->getDiasActivo();
        
        if ($dias == 0) {
            return 'No especificado';
        }
        
        $anos = floor($dias / 365.25);
        $diasRestantes = $dias % 365;
        
        if ($anos > 0) {
            if ($diasRestantes > 0) {
                return "{$anos} año" . ($anos > 1 ? 's' : '') . " y {$diasRestantes} día" . ($diasRestantes > 1 ? 's' : '');
            } else {
                return "{$anos} año" . ($anos > 1 ? 's' : '');
            }
        } else {
            return "{$dias} día" . ($dias > 1 ? 's' : '');
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
            $this->EMISION_INVENTARIO = date('Y-m-d');
        }
    }

    /**
     * Obtiene las marcas disponibles
     */
    public static function getMarcas()
    {
        return [
            'Apple' => 'Apple',
            'Dell' => 'Dell',
            'HP' => 'HP',
            'Lenovo' => 'Lenovo',
            'ASUS' => 'ASUS',
            'Acer' => 'Acer',
            'Samsung' => 'Samsung',
            'Anker' => 'Anker',
            'Belkin' => 'Belkin',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene los tipos disponibles
     */
    public static function getTipos()
    {
        return [
            'Cargador de laptop' => 'Cargador de laptop',
            'Adaptador USB' => 'Adaptador USB',
            'Adaptador HDMI' => 'Adaptador HDMI',
            'Adaptador VGA' => 'Adaptador VGA',
            'Adaptador USB-C' => 'Adaptador USB-C',
            'Adaptador de corriente' => 'Adaptador de corriente',
            'Convertidor' => 'Convertidor',
            'Hub USB' => 'Hub USB',
            'Otro' => 'Otro'
        ];
    }

    /**
     * Obtiene tipos de entrada
     */
    public static function getEntradas()
    {
        return [
            'AC 100-240V' => 'AC 100-240V',
            'USB-A' => 'USB-A',
            'USB-C' => 'USB-C',
            'HDMI' => 'HDMI',
            'VGA' => 'VGA',
            'DisplayPort' => 'DisplayPort',
            'Thunderbolt' => 'Thunderbolt',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene tipos de salida
     */
    public static function getSalidas()
    {
        return [
            'DC 19V' => 'DC 19V',
            'DC 20V' => 'DC 20V',
            'USB-A' => 'USB-A',
            'USB-C' => 'USB-C',
            'HDMI' => 'HDMI',
            'VGA' => 'VGA',
            'DisplayPort' => 'DisplayPort',
            'Barrel Jack' => 'Barrel Jack',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene voltajes comunes
     */
    public static function getVoltajes()
    {
        return [
            '5V' => '5V',
            '12V' => '12V',
            '19V' => '19V',
            '20V' => '20V',
            '24V' => '24V',
            '100-240V' => '100-240V (AC)',
            'Otro' => 'Otro'
        ];
    }

    /**
     * Obtiene amperajes comunes
     */
    public static function getAmperajes()
    {
        return [
            '1A' => '1A',
            '2A' => '2A',
            '3A' => '3A',
            '4.5A' => '4.5A',
            '6A' => '6A',
            'Otro' => 'Otro'
        ];
    }

    public static function getEdificios()
    {
        $edificios = [];
        foreach (range('A', 'U') as $letra) {
            $edificios["Edificio $letra"] = "Edificio $letra";
        }
        return $edificios;
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
}