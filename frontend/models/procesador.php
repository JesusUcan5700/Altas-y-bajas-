<?php

namespace frontend\models;

use Yii;
use DateTime;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * This is the model class for table "procesadores".
 *
 * @property int $idProcesador
 * @property string $MARCA
 * @property string $MODELO
 * @property string $FRECUENCIA_BASE
 * @property int $NUCLEOS
 * @property int $HILOS
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string|null $DESCRIPCION
 * @property string $Estado
 * @property string $fecha
 * @property string|null $ubicacion_edificio
 * @property string|null $ubicacion_detalle
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $ultimo_editor
 *
 * @property User $ultimoEditor
 */
class Procesador extends \yii\db\ActiveRecord
{
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_BAJA = 'BAJA';

    const MARCA_INTEL = 'Intel';
    const MARCA_AMD = 'AMD';
    const MARCA_ARM = 'ARM';

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

    public static function tableName()
    {
        return 'procesadores';
    }

    public function rules()
    {
        return [
            // Reglas para escenario por defecto (modo completo)
            [['MARCA', 'MODELO', 'FRECUENCIA_BASE', 'NUCLEOS', 'HILOS', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'ubicacion_edificio'], 'required', 'except' => 'simplificado'],
            
            // Reglas para escenario simplificado (solo marca y modelo)
            [['MARCA', 'MODELO'], 'required', 'on' => 'simplificado'],
            
            // Reglas comunes para todos los escenarios
            [['NUCLEOS', 'HILOS'], 'integer', 'min' => 1, 'except' => 'simplificado'],
            [['NUCLEOS', 'HILOS'], 'integer', 'min' => 0, 'on' => 'simplificado'], // Para catálogo permitir 0
            [['fecha'], 'date', 'format' => 'yyyy-MM-dd'],
            [['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['FRECUENCIA_BASE'], 'string', 'max' => 20],
            [['DESCRIPCION'], 'string', 'max' => 100],
            [['Estado'], 'string'],
            [['Estado'], 'in', 'range' => [self::ESTADO_ACTIVO, self::ESTADO_INACTIVO, self::ESTADO_DANADO, self::ESTADO_MANTENIMIENTO, self::ESTADO_BAJA]],
            [['Estado'], 'default', 'value' => self::ESTADO_ACTIVO],
            [['fecha'], 'default', 'value' => date('Y-m-d')],
            [['ubicacion_edificio'], 'string', 'max' => 15],
            [['ubicacion_detalle'], 'string', 'max' => 255],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['NUMERO_SERIE'], 'unique', 'except' => 'simplificado'],
            [['NUMERO_INVENTARIO'], 'unique', 'except' => 'simplificado'],
            [['FRECUENCIA_BASE'], 'match', 'pattern' => '/^[\d\.]+\s?(GHz|MHz)$/i', 'message' => 'Formato: 3.2 GHz o 2800 MHz', 'except' => 'simplificado'],
            [['ubicacion_edificio'], 'in', 'range' => ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U'], 'except' => 'simplificado'],
            
            // Campos de auditoría - solo marcados como seguros, sin validación
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        // Escenario simplificado: solo marca y modelo son requeridos, campos técnicos pueden estar vacíos
        $scenarios['simplificado'] = ['MARCA', 'MODELO', 'Estado', 'fecha', 'ubicacion_edificio', 'ubicacion_detalle', 'DESCRIPCION', 'FRECUENCIA_BASE', 'NUCLEOS', 'HILOS', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'];
        
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'idProcesador' => 'ID Procesador',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'FRECUENCIA_BASE' => 'Frecuencia Base',
            'NUCLEOS' => 'Núcleos',
            'HILOS' => 'Hilos',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'DESCRIPCION' => 'Descripción',
            'Estado' => 'Estado',
            'fecha' => 'Fecha de Registro',
            'ubicacion_edificio' => 'Edificio',
            'ubicacion_detalle' => 'Ubicación Detallada',
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
        return self::find()->where(['Estado' => 'dañado(Proceso de baja)'])->all();
    }

    /**
     * Contar equipos con estado dañado (proceso de baja)
     */
    public static function countEquiposDanados()
    {
        return self::find()->where(['Estado' => 'dañado(Proceso de baja)'])->count();
    }

    public static function getMarcas()
    {
        return [
            self::MARCA_INTEL => 'Intel',
            self::MARCA_AMD => 'AMD',
            self::MARCA_ARM => 'ARM',
        ];
    }

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

    public function estaDañado()
    {
        return $this->Estado === self::ESTADO_DANADO;
    }

    public function getEstadoTexto()
    {
        $estados = self::getEstados();
        return isset($estados[$this->Estado]) ? $estados[$this->Estado] : $this->Estado;
    }

    public function getFechaFormateada()
    {
        return $this->fecha ? Yii::$app->formatter->asDate($this->fecha, 'dd/MM/yyyy') : '';
    }

    public function getUbicacionCompleta()
    {
        $ubicacion = $this->ubicacion_edificio;
        if (!empty($this->ubicacion_detalle)) {
            $ubicacion .= ' - ' . $this->ubicacion_detalle;
        }
        return $ubicacion;
    }

    public function marcarDanado()
    {
        $this->Estado = self::ESTADO_DANADO;
        return $this->save();
    }

    /**
     * Inicializa el modelo con valores por defecto
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            if (empty($this->Estado)) {
                $this->Estado = self::ESTADO_ACTIVO;
            }
            if (empty($this->fecha)) {
                $this->fecha = date('Y-m-d');
            }
        }
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