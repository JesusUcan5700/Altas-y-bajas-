<?php

namespace frontend\models;

use Yii;
use DateTime;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * This is the model class for table "conectividad".
 *
 * @property int $idCONECTIVIDAD
 * @property string $TIPO
 * @property string $MARCA
 * @property string $MODELO
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string $CANTIDAD_PUERTOS
 * @property string $DESCRIPCION
 * @property string|null $Estado
 * @property string|null $ubicacion_edificio
 * @property string|null $ubicacion_detalle
 * @property string|null $fecha
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $ultimo_editor
 *
 * @property User $ultimoEditor
 */
class Conectividad extends \yii\db\ActiveRecord
{
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
    const ESTADO_BAJA = 'BAJA';

    // Tipos de equipos de conectividad
    const TIPO_SWITCH = 'switch';
    const TIPO_ROUTER = 'router';
    const TIPO_ACCESS_POINT = 'access_point';
    const TIPO_MODEM = 'modem';
    const TIPO_FIREWALL = 'firewall';
    const TIPO_HUB = 'hub';
    const TIPO_REPETIDOR = 'repetidor';

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
            // Asignar el usuario actual como último editor
            if (!Yii::$app instanceof \yii\console\Application && !Yii::$app->user->isGuest) {
                $this->ultimo_editor = Yii::$app->user->identity->username;
            } elseif (empty($this->ultimo_editor)) {
                $this->ultimo_editor = 'sistema';
            }
            
            // Asegurar valores por defecto
            if (empty($this->Estado)) {
                $this->Estado = self::ESTADO_ACTIVO;
            }
            if (empty($this->fecha)) {
                $this->fecha = date('Y-m-d');
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
        return 'conectividad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['TIPO', 'MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'CANTIDAD_PUERTOS'], 'required'],
            [['fecha'], 'date', 'format' => 'yyyy-MM-dd'],
            [['TIPO', 'MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'CANTIDAD_PUERTOS', 'DESCRIPCION'], 'string', 'max' => 45],
            [['Estado'], 'string', 'max' => 100],
            
            // Validación de estados permitidos
            [['Estado'], 'in', 'range' => [
                self::ESTADO_ACTIVO,
                self::ESTADO_INACTIVO,
                self::ESTADO_DANADO,
                self::ESTADO_MANTENIMIENTO,
                self::ESTADO_BAJA
            ]],
            
            [['ubicacion_edificio'], 'string', 'max' => 15],
            [['ubicacion_detalle'], 'string', 'max' => 255],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otro equipo de conectividad.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otro equipo de conectividad.'],
            [['NUMERO_INVENTARIO'], 'unique'],
            [['Estado'], 'default', 'value' => self::ESTADO_ACTIVO],
            [['fecha'], 'default', 'value' => date('Y-m-d')],
            
            // Campos de auditoría - solo marcados como seguros, sin validación
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idCONECTIVIDAD' => 'ID Conectividad',
            'TIPO' => 'Tipo de Equipo',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'CANTIDAD_PUERTOS' => 'Cantidad de Puertos',
            'DESCRIPCION' => 'Descripción',
            'Estado' => 'Estado',
            'ubicacion_edificio' => 'Edificio',
            'ubicacion_detalle' => 'Ubicación Detallada',
            'fecha' => 'Fecha de Registro',
        ];
    }

    /**
     * Obtiene los tipos de equipos de conectividad disponibles
     */
    public static function getTipos()
    {
        return [
            self::TIPO_SWITCH => 'Switch',
            self::TIPO_ROUTER => 'Router',
            self::TIPO_ACCESS_POINT => 'Access Point',
            self::TIPO_MODEM => 'Módem',
            self::TIPO_FIREWALL => 'Firewall',
            self::TIPO_HUB => 'Hub',
            self::TIPO_REPETIDOR => 'Repetidor',
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
        return self::find()->where(['Estado' => 'dañado(Proceso de baja)'])->all();
    }

    /**
     * Contar equipos con estado dañado (proceso de baja)
     */
    public static function countEquiposDanados()
    {
        return self::find()->where(['Estado' => 'dañado(Proceso de baja)'])->count();
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
     * Relación con el modelo User para el último editor
     */
    public function getUltimoEditor()
    {
        return $this->hasOne(User::class, ['username' => 'ultimo_editor']);
    }

    /**
     * Obtener información formateada del último editor
     */
    public function getInfoUltimoEditor()
    {
        if ($this->ultimoEditor) {
            return $this->ultimoEditor->username;
        }
        return $this->ultimo_editor ?: 'No definido';
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
        // Usar fecha del usuario si está disponible, sino fecha_creacion del sistema
        $fechaBase = $this->fecha ?: $this->fecha_creacion;
        
        if (!$fechaBase) {
            return 'No definido';
        }
        
        $fechaCreacion = new DateTime($fechaBase);
        $ahora = new DateTime();
        $diferencia = $ahora->diff($fechaCreacion);
        
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
                return 'Menos de 1 minuto';
            }
        }
        
        return implode(', ', array_slice($partes, 0, 2));
    }

    /**
     * Obtener tiempo desde la última edición
     */
    public function getTiempoUltimaEdicion()
    {
        if (!$this->fecha_ultima_edicion) {
            return 'No definido';
        }
        
        $fechaEdicion = new DateTime($this->fecha_ultima_edicion);
        $ahora = new DateTime();
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
                return 'Menos de 1 minuto';
            }
        }
        
        return implode(', ', array_slice($partes, 0, 2));
    }

}