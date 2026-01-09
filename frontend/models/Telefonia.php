<?php
namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * This is the model class for table "telefonia".
 *
 * @property int $idTELEFONIA
 * @property string $MARCA
 * @property string $MODELO
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string $EDIFICIO
 * @property string $ESTADO
 * @property string $EMISION_INVENTARIO
 * @property string $TIEMPO_TRANSCURRIDO
 * @property string $fecha
 * @property string $ubicacion_edificio
 * @property string $ubicacion_detalle
 * @property string $fecha_creacion
 * @property string $fecha_ultima_edicion
 * @property string $ultimo_editor
 *
 * @property User $ultimoEditorUser
 */
class Telefonia extends \yii\db\ActiveRecord
{
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
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
    public static function tableName()
    {
        return 'telefonia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'EMISION_INVENTARIO'], 'required'],
            [['fecha'], 'date', 'format' => 'yyyy-MM-dd'],
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'EDIFICIO', 'EMISION_INVENTARIO', 'TIEMPO_TRANSCURRIDO'], 'string', 'max' => 45],
            [['ESTADO'], 'string', 'max' => 100],
            [['ubicacion_edificio'], 'string', 'max' => 15],
            [['ubicacion_detalle'], 'string', 'max' => 255],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otro equipo de telefonía.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otro equipo de telefonía.'],
            [['NUMERO_INVENTARIO'], 'unique'],
            [['ESTADO'], 'in', 'range' => [
                self::ESTADO_ACTIVO, 
                self::ESTADO_INACTIVO, 
                self::ESTADO_MANTENIMIENTO, 
                self::ESTADO_DANADO,
                self::ESTADO_BAJA
            ]],
            [['ESTADO'], 'default', 'value' => self::ESTADO_ACTIVO],
            [['fecha'], 'default', 'value' => date('Y-m-d')],
            [['ubicacion_edificio'], 'in', 'range' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idTELEFONIA' => 'ID Telefonía',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'EDIFICIO' => 'Edificio (Campo Anterior)',
            'ESTADO' => 'Estado',
            'EMISION_INVENTARIO' => 'Emisión de Inventario',
            'TIEMPO_TRANSCURRIDO' => 'Tiempo Transcurrido',
            'fecha' => 'Fecha de Registro',
            'ubicacion_edificio' => 'Edificio',
            'ubicacion_detalle' => 'Ubicación Detallada',
            'fecha_creacion' => 'Fecha de Creación',
            'fecha_ultima_edicion' => 'Última Edición',
            'ultimo_editor' => 'Último Editor',
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
        return self::find()->where(['ESTADO' => self::ESTADO_DANADO])->all();
    }

    /**
     * Contar equipos con estado dañado (proceso de baja)
     */
    public static function countEquiposDanados()
    {
        return self::find()->where(['ESTADO' => self::ESTADO_DANADO])->count();
    }

    /**
     * Obtiene las opciones de ubicación de edificios
     */
    public static function getUbicacionesEdificio()
    {
        return [
            'A' => 'Edificio A',
            'B' => 'Edificio B',
            'C' => 'Edificio C',
            'D' => 'Edificio D',
            'E' => 'Edificio E',
            'F' => 'Edificio F',
            'G' => 'Edificio G',
            'H' => 'Edificio H',
            'I' => 'Edificio I',
            'J' => 'Edificio J',
            'K' => 'Edificio K',
            'L' => 'Edificio L',
            'M' => 'Edificio M',
            'N' => 'Edificio N',
            'O' => 'Edificio O',
            'P' => 'Edificio P',
            'Q' => 'Edificio Q',
            'R' => 'Edificio R',
            'S' => 'Edificio S',
            'T' => 'Edificio T',
            'U' => 'Edificio U',
        ];
    }

    /**
     * Inicializa el modelo con valores por defecto
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            if (empty($this->ESTADO)) {
                $this->ESTADO = self::ESTADO_ACTIVO;
            }
            if (empty($this->fecha)) {
                $this->fecha = date('Y-m-d');
            }
        }
    }

    /**
     * Antes de guardar, asegurar valores por defecto
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->ESTADO)) {
                $this->ESTADO = self::ESTADO_ACTIVO;
            }
            if (empty($this->fecha)) {
                $this->fecha = date('Y-m-d');
            }
            return true;
        }
        return false;
    }

    /**
     * Relación con el usuario que fue el último editor
     */
    public function getUltimoEditorUser()
    {
        return $this->hasOne(User::class, ['username' => 'ultimo_editor']);
    }

    /**
     * Obtiene el tiempo que lleva activo este equipo de telefonía
     * Calcula basándose en el campo 'fecha' (fecha definida por el usuario)
     */
    public function getTiempoActivo()
    {
        if (empty($this->fecha)) {
            return 'Fecha no definida';
        }

        try {
            $fechaInicio = new \DateTime($this->fecha);
            $fechaActual = new \DateTime();
            $intervalo = $fechaInicio->diff($fechaActual);

            $years = $intervalo->y;
            $months = $intervalo->m;
            $days = $intervalo->d;

            $partes = [];
            if ($years > 0) {
                $partes[] = $years . ($years == 1 ? ' año' : ' años');
            }
            if ($months > 0) {
                $partes[] = $months . ($months == 1 ? ' mes' : ' meses');
            }
            if ($days > 0 && $years == 0) { // Solo mostrar días si no hay años
                $partes[] = $days . ($days == 1 ? ' día' : ' días');
            }

            if (empty($partes)) {
                return 'Menos de 1 día';
            }

            return implode(', ', $partes);
        } catch (\Exception $e) {
            return 'Error en fecha';
        }
    }

    /**
     * Obtiene el tiempo transcurrido desde la última edición
     */
    public function getTiempoUltimaEdicion()
    {
        if (empty($this->fecha_ultima_edicion)) {
            return 'No disponible';
        }

        try {
            $fechaEdicion = new \DateTime($this->fecha_ultima_edicion);
            $fechaActual = new \DateTime();
            $intervalo = $fechaEdicion->diff($fechaActual);

            $days = $intervalo->days;
            $hours = $intervalo->h;
            $minutes = $intervalo->i;

            if ($days > 0) {
                return $days . ($days == 1 ? ' día' : ' días');
            } elseif ($hours > 0) {
                return $hours . ($hours == 1 ? ' hora' : ' horas') . 
                       ($minutes > 0 ? ', ' . $minutes . ($minutes == 1 ? ' minuto' : ' minutos') : '');
            } elseif ($minutes > 0) {
                return $minutes . ($minutes == 1 ? ' minuto' : ' minutos');
            } else {
                return 'Menos de 1 minuto';
            }
        } catch (\Exception $e) {
            return 'Error en fecha';
        }
    }

    /**
     * Obtiene información del último editor
     */
    public function getInfoUltimoEditor()
    {
        if (empty($this->ultimo_editor)) {
            return 'No definido';
        }

        // Intentar obtener información del usuario
        $usuario = $this->ultimoEditorUser;
        if ($usuario) {
            return $this->ultimo_editor;
        }

        return $this->ultimo_editor;
    }
}