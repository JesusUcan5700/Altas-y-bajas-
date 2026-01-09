<?php

namespace frontend\models;

use Yii;
use DateTime;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * This is the model class for table "baterias".
 *
 * @property int $idBateria
 * @property string $MARCA
 * @property string $MODELO
 * @property string $TIPO
 * @property string $FORMATO_PILA
 * @property string $VOLTAJE
 * @property string|null $CAPACIDAD
 * @property string|null $USO
 * @property int $RECARGABLE
 * @property string $NUMERO_INVENTARIO
 * @property string|null $DESCRIPCION
 * @property string $ESTADO
 * @property string $FECHA
 * @property string|null $FECHA_VENCIMIENTO
 * @property string|null $FECHA_REEMPLAZO
 * @property string|null $ubicacion_edificio Ubicación completa del equipo (edificio, piso, oficina, etc.)
 * @property string|null $ubicacion_detalle Descripción detallada de la ubicación del equipo
 * @property string|null $USO_PERSONALIZADO
 * @property string|null $NUMERO_SERIE
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $ultimo_editor
 *
 * @property User $ultimoEditor
 */
class Bateria extends \yii\db\ActiveRecord
{
    // Constantes para estados estandarizados
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
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
                'value' => new Expression('NOW()'),
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
            return true;
        }
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'baterias';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['CAPACIDAD', 'USO', 'DESCRIPCION', 'FECHA_VENCIMIENTO', 'FECHA_REEMPLAZO', 'ubicacion_edificio', 'ubicacion_detalle', 'USO_PERSONALIZADO', 'NUMERO_SERIE', 'ultimo_editor'], 'default', 'value' => null],
            [['RECARGABLE'], 'default', 'value' => 0],
            [['MARCA', 'MODELO', 'TIPO', 'FORMATO_PILA', 'VOLTAJE', 'NUMERO_INVENTARIO', 'ESTADO', 'FECHA'], 'required'],
            [['RECARGABLE'], 'integer'],
            [['FECHA', 'FECHA_VENCIMIENTO', 'FECHA_REEMPLAZO', 'fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['MARCA', 'MODELO', 'USO', 'NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['TIPO', 'CAPACIDAD'], 'string', 'max' => 20],
            [['FORMATO_PILA', 'VOLTAJE'], 'string', 'max' => 10],
            [['DESCRIPCION', 'USO_PERSONALIZADO', 'ultimo_editor'], 'string', 'max' => 100],
            [['ESTADO'], 'string', 'max' => 100],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255],
            [['NUMERO_SERIE'], 'string', 'max' => 50],
            [['ESTADO'], 'in', 'range' => [
                self::ESTADO_ACTIVO, 
                self::ESTADO_INACTIVO, 
                self::ESTADO_DANADO, 
                self::ESTADO_MANTENIMIENTO,
                self::ESTADO_BAJA
            ]],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otra batería.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otra batería.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idBateria' => 'Id Bateria',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TIPO' => 'Tipo',
            'FORMATO_PILA' => 'Formato Pila',
            'VOLTAJE' => 'Voltaje',
            'CAPACIDAD' => 'Capacidad',
            'USO' => 'Uso',
            'RECARGABLE' => 'Recargable',
            'NUMERO_INVENTARIO' => 'Numero Inventario',
            'DESCRIPCION' => 'Descripcion',
            'ESTADO' => 'Estado',
            'FECHA' => 'Fecha',
            'FECHA_VENCIMIENTO' => 'Fecha Vencimiento',
            'FECHA_REEMPLAZO' => 'Fecha Reemplazo',
            'ubicacion_edificio' => 'Ubicación completa del equipo (edificio, piso, oficina, etc.)',
            'ubicacion_detalle' => 'Descripción detallada de la ubicación del equipo',
            'USO_PERSONALIZADO' => 'Uso Personalizado',
            'NUMERO_SERIE' => 'Numero Serie',
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
     * Obtener fecha de creación formateada
     */
    public function getFechaCreacionFormateada()
    {
        return $this->fecha_creacion ? Yii::$app->formatter->asDatetime($this->fecha_creacion) : '';
    }

    /**
     * Obtener fecha de última edición formateada
     */
    public function getFechaUltimaEdicionFormateada()
    {
        return $this->fecha_ultima_edicion ? Yii::$app->formatter->asDatetime($this->fecha_ultima_edicion) : '';
    }

    /**
     * Obtener tiempo activo desde la creación
     */
    public function getTiempoActivo()
    {
        // Usar FECHA del usuario si está disponible, sino fecha_creacion del sistema
        $fechaBase = $this->FECHA ?: $this->fecha_creacion;
        
        if ($fechaBase) {
            $fechaInicio = new DateTime($fechaBase);
            $fechaActual = new DateTime();
            $diferencia = $fechaActual->diff($fechaInicio);
            
            if ($diferencia->days > 0) {
                return $diferencia->days . ' días';
            } else {
                return 'Menos de 1 día';
            }
        }
        return 'N/A';
    }

    /**
     * Obtener tiempo desde la última edición
     */
    public function getTiempoUltimaEdicion()
    {
        if (!$this->fecha_ultima_edicion) {
            return 'No definido';
        }
        
        $fechaEdicion = new \DateTime($this->fecha_ultima_edicion);
        $ahora = new \DateTime();
        $diferencia = $ahora->diff($fechaEdicion);
        
        $partes = [];
        
        if ($diferencia->y > 0) {
            $partes[] = $diferencia->y . ' año' . ($diferencia->y > 1 ? 's' : '');
        }
        if ($diferencia->m > 0) {
            $partes[] = $diferencia->m . ' mes' . ($diferencia->m > 1 ? 'es' : '');
        }
        if ($diferencia->d > 0) {
            $partes[] = $diferencia->d . ' día' . ($diferencia->d > 1 ? 's' : '');
        }
        if (empty($partes)) {
            if ($diferencia->h > 0) {
                $partes[] = $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
            }
            if ($diferencia->i > 0) {
                $partes[] = $diferencia->i . ' minuto' . ($diferencia->i > 1 ? 's' : '');
            }
            if (empty($partes)) {
                return 'Hace menos de 1 minuto';
            }
        }
        
        return 'Hace ' . implode(', ', array_slice($partes, 0, 2));
    }

    /**
     * Obtener lista de marcas disponibles
     */
    public static function getMarcas()
    {
        return [
            'Duracell' => 'Duracell',
            'Energizer' => 'Energizer',
            'Panasonic' => 'Panasonic',
            'Sony' => 'Sony',
            'Varta' => 'Varta',
            'Rayovac' => 'Rayovac',
            'Kodak' => 'Kodak',
            'Philips' => 'Philips',
            'GP' => 'GP',
            'Maxwell' => 'Maxwell',
            'Yuasa' => 'Yuasa',
            'CSB' => 'CSB',
            'APC' => 'APC',
            'Tripp Lite' => 'Tripp Lite',
            'CyberPower' => 'CyberPower',
            'Otra' => 'Otra',
        ];
    }

    /**
     * Obtener lista de tipos de batería
     */
    public static function getTipos()
    {
        return [
            'Alcalina' => 'Alcalina',
            'Litio' => 'Litio',
            'NiMH' => 'NiMH',
            'NiCd' => 'NiCd',
            'Plomo Ácido' => 'Plomo Ácido',
            'Ion de Litio' => 'Ion de Litio',
            'Zinc Carbón' => 'Zinc Carbón',
            'AGM' => 'AGM',
            'Gel' => 'Gel',
        ];
    }

    /**
     * Obtener lista de formatos de pila
     */
    public static function getFormatos()
    {
        return [
            'AA' => 'AA',
            'AAA' => 'AAA',
            'C' => 'C',
            'D' => 'D',
            '9V' => '9V',
            'CR2032' => 'CR2032',
            'CR2025' => 'CR2025',
            'CR123A' => 'CR123A',
            '18650' => '18650',
            '12V' => '12V',
            '6V' => '6V',
            'Otro' => 'Otro',
        ];
    }

    /**
     * Obtener lista de voltajes
     */
    public static function getVoltajes()
    {
        return [
            '1.2V' => '1.2V',
            '1.5V' => '1.5V',
            '3V' => '3V',
            '3.6V' => '3.6V',
            '3.7V' => '3.7V',
            '6V' => '6V',
            '9V' => '9V',
            '12V' => '12V',
            '24V' => '24V',
            'Otro' => 'Otro',
        ];
    }

    /**
     * Obtener lista de capacidades
     */
    public static function getCapacidades()
    {
        return [
            '500mAh' => '500mAh',
            '700mAh' => '700mAh',
            '1000mAh' => '1000mAh',
            '1200mAh' => '1200mAh',
            '1500mAh' => '1500mAh',
            '2000mAh' => '2000mAh',
            '2500mAh' => '2500mAh',
            '3000mAh' => '3000mAh',
            '5000mAh' => '5000mAh',
            '7Ah' => '7Ah',
            '9Ah' => '9Ah',
            '12Ah' => '12Ah',
            '17Ah' => '17Ah',
            '20Ah' => '20Ah',
            'Otra' => 'Otra',
        ];
    }

    /**
     * Obtener lista de usos
     */
    public static function getUsos()
    {
        return [
            'Control Remoto' => 'Control Remoto',
            'Juguete' => 'Juguete',
            'Reloj' => 'Reloj',
            'Calculadora' => 'Calculadora',
            'Radio' => 'Radio',
            'Linterna' => 'Linterna',
            'UPS' => 'UPS',
            'Equipo Médico' => 'Equipo Médico',
            'Seguridad' => 'Seguridad',
            'Cámara' => 'Cámara',
            'Mouse/Teclado' => 'Mouse/Teclado',
            'Smartphone' => 'Smartphone',
            'Tablet' => 'Tablet',
            'Laptop' => 'Laptop',
            'Otro' => 'Otro',
        ];
    }

    /**
     * Obtener opciones de recargable
     */
    public static function getRecargableOptions()
    {
        return [
            1 => 'Sí',
            0 => 'No',
        ];
    }

    /**
     * Obtener lista de estados estandarizados
     */
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
     * Obtener lista de edificios
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
