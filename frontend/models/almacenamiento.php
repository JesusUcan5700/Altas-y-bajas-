<?php

namespace frontend\models;

use Yii;
use DateTime;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * This is the model class for table "almacenamiento".
 *
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $ultimo_editor
 *
 * @property User $ultimoEditor
 */
class Almacenamiento extends \yii\db\ActiveRecord
{
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
            
            // Si estamos en escenario simplificado, completar campos faltantes
            if ($this->scenario === 'simplificado' && $insert) {
                // TIPO y CAPACIDAD ahora vienen del formulario, solo llenar otros campos
                $this->INTERFAZ = $this->INTERFAZ ?: 'No especificado';
                $this->ESTADO = $this->ESTADO ?: 'Inactivo(Sin Asignar)';
                $this->FECHA = $this->FECHA ?: date('Y-m-d');
                $this->ubicacion_edificio = $this->ubicacion_edificio ?: 'A';
                $this->ubicacion_detalle = $this->ubicacion_detalle ?: 'Catálogo - Solo marca y modelo';
                $this->DESCRIPCION = $this->DESCRIPCION ?: 'Entrada de catálogo';
                
                // Generar números únicos solo si no existen
                if (empty($this->NUMERO_SERIE)) {
                    $timestamp = time() . rand(100, 999);
                    $this->NUMERO_SERIE = 'CAT-ALM-' . $timestamp;
                }
                if (empty($this->NUMERO_INVENTARIO)) {
                    $timestamp = time() . rand(100, 999);
                    $this->NUMERO_INVENTARIO = 'CAT-ALM-' . $timestamp;
                }
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
        return 'almacenamiento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Campos requeridos - todos los campos en modo normal
            [['MARCA', 'MODELO', 'TIPO'], 'required', 'except' => 'simplificado'],
            
            // Campos requeridos - marca, modelo, capacidad y tipo en modo simplificado
            [['MARCA', 'MODELO', 'CAPACIDAD', 'TIPO'], 'required', 'on' => 'simplificado'],
            
            [['FECHA', 'fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            
            // Validaciones de longitud - aplicar a todos los modos
            [['MARCA', 'MODELO'], 'string', 'max' => 45],
            [['CAPACIDAD'], 'string', 'max' => 30],
            [['TIPO'], 'string', 'max' => 100],
            // Validaciones solo para modo normal
            [['INTERFAZ', 'ESTADO'], 'string', 'max' => 100, 'except' => 'simplificado'],
            [['NUMERO_SERIE', 'NUMERO_INVENTARIO'], 'string', 'max' => 45, 'except' => 'simplificado'],
            [['DESCRIPCION', 'ultimo_editor'], 'string', 'max' => 100, 'except' => 'simplificado'],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255, 'except' => 'simplificado'],
            
            [['INTERFAZ', 'CAPACIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'DESCRIPCION', 'ESTADO', 'ubicacion_edificio', 'ubicacion_detalle', 'ultimo_editor'], 'safe'],
        ];
    }

    /**
     * Define scenarios para diferentes contextos
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        // Scenario para modo simplificado (catálogo)
        $scenarios['simplificado'] = ['MARCA', 'MODELO', 'TIPO', 'CAPACIDAD', 'INTERFAZ', 'ESTADO', 'FECHA', 'ubicacion_edificio', 'ubicacion_detalle', 'DESCRIPCION', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'];
        
        return $scenarios;
    }

    /**
     * Establece valores por defecto antes de validar
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            // Si FECHA está vacía, establecer la fecha actual
            if (empty($this->FECHA)) {
                $this->FECHA = date('Y-m-d');
            }
            
            return true;
        }
        return false;
    }

    /**
     * Establece valores por defecto para nuevos registros
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->ESTADO = 'activo';
            $this->FECHA = date('Y-m-d');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TIPO' => 'Tipo',
            'INTERFAZ' => 'Interfaz',
            'CAPACIDAD' => 'Capacidad',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'DESCRIPCION' => 'Descripción',
            'FECHA' => 'Fecha',
            'ESTADO' => 'Estado',
            'ubicacion_edificio' => 'Edificio',
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
        
        if (!$fechaBase) {
            return 'No definido';
        }
        
        $fechaCreacion = new \DateTime($fechaBase);
        $ahora = new \DateTime();
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
     * Obtiene las marcas disponibles
     * @return array
     */
    public static function getMarcas()
    {
        return [
            'Western Digital' => 'Western Digital',
            'Seagate' => 'Seagate',
            'Samsung' => 'Samsung',
            'Kingston' => 'Kingston',
            'SanDisk' => 'SanDisk',
            'Toshiba' => 'Toshiba',
            'Crucial' => 'Crucial',
            'Intel' => 'Intel',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene los tipos de almacenamiento disponibles
     * @return array
     */
    public static function getTipos()
    {
        return [
            'HDD' => 'Disco Duro (HDD)',
            'SSD' => 'Disco Sólido (SSD)',
            'USB' => 'Memoria USB',
            'SD' => 'Tarjeta SD',
            'M.2' => 'M.2 NVMe',
            'Otro' => 'Otro'
        ];
    }

    /**
     * Obtiene las interfaces disponibles
     * @return array
     */
    public static function getInterfaces()
    {
        return [
            'SATA' => 'SATA',
            'USB 2.0' => 'USB 2.0',
            'USB 3.0' => 'USB 3.0',
            'USB-C' => 'USB-C',
            'M.2' => 'M.2',
            'NVMe' => 'NVMe',
            'IDE' => 'IDE',
            'eSATA' => 'eSATA',
            'Thunderbolt' => 'Thunderbolt',
            'Firewire' => 'Firewire',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene los estados disponibles estandarizados
     * @return array
     */
    public static function getEstados()
    {
        return [
            'Activo' => 'Activo',
            'Inactivo(Sin Asignar)' => 'Inactivo(Sin Asignar)',
            'dañado(Proceso de baja)' => 'dañado(Proceso de baja)',
            'En Mantenimiento' => 'En Mantenimiento',
            'BAJA' => 'BAJA',
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
     * Obtiene los edificios disponibles (A-U)
     * @return array
     */
    public static function getEdificios()
    {
        $edificios = [];
        for ($i = ord('A'); $i <= ord('U'); $i++) {
            $letra = chr($i);
            $edificios[$letra] = "Edificio $letra";
        }
        return $edificios;
    }
}