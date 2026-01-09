<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * Modelo para la tabla "piezas_reciclaje"
 * Gestiona las piezas recuperadas de equipos dados de baja
 *
 * @property int $id
 * @property string $tipo_pieza
 * @property string $marca
 * @property string|null $modelo
 * @property string|null $especificaciones
 * @property string|null $numero_serie
 * @property string|null $numero_inventario
 * @property string $estado_pieza
 * @property string $condicion
 * @property string|null $equipo_origen
 * @property string|null $equipo_origen_descripcion
 * @property string|null $componente_defectuoso
 * @property string|null $motivo_recuperacion
 * @property string|null $ubicacion_almacen
 * @property string|null $observaciones
 * @property string|null $asignado_a
 * @property string $fecha_recuperacion
 * @property string|null $fecha_asignacion
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $usuario_registro
 * @property string|null $ultimo_editor
 */
class PiezaReciclaje extends \yii\db\ActiveRecord
{
    // Constantes de tipos de pieza
    const TIPO_RAM = 'Memoria RAM';
    const TIPO_PROCESADOR = 'Procesador';
    const TIPO_ALMACENAMIENTO = 'Disco Duro';
    const TIPO_SSD = 'SSD';
    const TIPO_FUENTE = 'Fuente de Poder';
    const TIPO_MONITOR = 'Monitor';
    const TIPO_TARJETA_VIDEO = 'Tarjeta de Video';
    const TIPO_TARJETA_MADRE = 'Tarjeta Madre';
    const TIPO_GABINETE = 'Gabinete';
    const TIPO_TECLADO = 'Teclado';
    const TIPO_MOUSE = 'Mouse';
    const TIPO_CABLE = 'Cable/Adaptador';
    const TIPO_OTRO = 'Otro';

    // Constantes de estados
    const ESTADO_DISPONIBLE = 'Disponible';
    const ESTADO_EN_USO = 'En Uso';
    const ESTADO_RESERVADO = 'Reservado';
    const ESTADO_DANADO = 'Dañado';
    const ESTADO_BAJA = 'Dado de Baja';

    // Constantes de condición
    const CONDICION_EXCELENTE = 'Excelente';
    const CONDICION_BUENO = 'Bueno';
    const CONDICION_REGULAR = 'Regular';
    const CONDICION_MALO = 'Malo';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'piezas_reciclaje';
    }

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
    public function rules()
    {
        return [
            // Campos requeridos
            [['tipo_pieza', 'marca', 'estado_pieza', 'condicion', 'fecha_recuperacion'], 'required'],
            
            // Tipos de datos
            [['motivo_recuperacion', 'observaciones'], 'string'],
            [['fecha_recuperacion', 'fecha_asignacion', 'fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            
            // Longitudes máximas
            [['tipo_pieza', 'estado_pieza'], 'string', 'max' => 50],
            [['marca', 'modelo', 'numero_serie', 'numero_inventario', 'equipo_origen', 'ubicacion_almacen', 'usuario_registro', 'ultimo_editor'], 'string', 'max' => 100],
            [['especificaciones', 'componente_defectuoso', 'asignado_a'], 'string', 'max' => 255],
            [['equipo_origen_descripcion'], 'string', 'max' => 255],
            [['condicion'], 'string', 'max' => 100],
            
            // Valores por defecto
            [['estado_pieza'], 'default', 'value' => self::ESTADO_DISPONIBLE],
            [['condicion'], 'default', 'value' => self::CONDICION_BUENO],
            
            // Validaciones de rango
            [['estado_pieza'], 'in', 'range' => [
                self::ESTADO_DISPONIBLE, 
                self::ESTADO_EN_USO, 
                self::ESTADO_RESERVADO, 
                self::ESTADO_DANADO,
                self::ESTADO_BAJA
            ]],
            [['condicion'], 'in', 'range' => [
                self::CONDICION_EXCELENTE,
                self::CONDICION_BUENO,
                self::CONDICION_REGULAR,
                self::CONDICION_MALO
            ]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_pieza' => 'Tipo de Pieza',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'especificaciones' => 'Especificaciones',
            'numero_serie' => 'Número de Serie',
            'numero_inventario' => 'Número de Inventario',
            'estado_pieza' => 'Estado',
            'condicion' => 'Condición',
            'equipo_origen' => 'Equipo de Origen',
            'equipo_origen_descripcion' => 'Descripción del Equipo Origen',
            'componente_defectuoso' => 'Componente Defectuoso',
            'motivo_recuperacion' => 'Motivo de Recuperación',
            'ubicacion_almacen' => 'Ubicación en Almacén',
            'observaciones' => 'Observaciones',
            'asignado_a' => 'Asignado A',
            'fecha_recuperacion' => 'Fecha de Recuperación',
            'fecha_asignacion' => 'Fecha de Asignación',
            'fecha_creacion' => 'Fecha de Creación',
            'fecha_ultima_edicion' => 'Última Edición',
            'usuario_registro' => 'Registrado Por',
            'ultimo_editor' => 'Último Editor',
        ];
    }

    /**
     * Obtiene el historial de movimientos de esta pieza
     */
    public function getHistorial()
    {
        return $this->hasMany(HistorialPiezaReciclaje::class, ['pieza_id' => 'id'])
            ->orderBy(['fecha_movimiento' => SORT_DESC]);
    }

    /**
     * Lista de tipos de pieza disponibles
     */
    public static function getTiposPieza()
    {
        return [
            self::TIPO_RAM => self::TIPO_RAM,
            self::TIPO_PROCESADOR => self::TIPO_PROCESADOR,
            self::TIPO_ALMACENAMIENTO => self::TIPO_ALMACENAMIENTO,
            self::TIPO_SSD => self::TIPO_SSD,
            self::TIPO_FUENTE => self::TIPO_FUENTE,
            self::TIPO_MONITOR => self::TIPO_MONITOR,
            self::TIPO_TARJETA_VIDEO => self::TIPO_TARJETA_VIDEO,
            self::TIPO_TARJETA_MADRE => self::TIPO_TARJETA_MADRE,
            self::TIPO_GABINETE => self::TIPO_GABINETE,
            self::TIPO_TECLADO => self::TIPO_TECLADO,
            self::TIPO_MOUSE => self::TIPO_MOUSE,
            self::TIPO_CABLE => self::TIPO_CABLE,
            self::TIPO_OTRO => self::TIPO_OTRO,
        ];
    }

    /**
     * Lista de estados disponibles
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_DISPONIBLE => self::ESTADO_DISPONIBLE,
            self::ESTADO_EN_USO => self::ESTADO_EN_USO,
            self::ESTADO_RESERVADO => self::ESTADO_RESERVADO,
            self::ESTADO_DANADO => self::ESTADO_DANADO,
            self::ESTADO_BAJA => self::ESTADO_BAJA,
        ];
    }

    /**
     * Lista de condiciones disponibles
     */
    public static function getCondiciones()
    {
        return [
            self::CONDICION_EXCELENTE => self::CONDICION_EXCELENTE,
            self::CONDICION_BUENO => self::CONDICION_BUENO,
            self::CONDICION_REGULAR => self::CONDICION_REGULAR,
            self::CONDICION_MALO => self::CONDICION_MALO,
        ];
    }

    /**
     * Obtiene la clase CSS según el estado
     */
    public function getEstadoClass()
    {
        $clases = [
            self::ESTADO_DISPONIBLE => 'bg-success',
            self::ESTADO_EN_USO => 'bg-info',
            self::ESTADO_RESERVADO => 'bg-warning',
            self::ESTADO_DANADO => 'bg-danger',
            self::ESTADO_BAJA => 'bg-secondary',
        ];
        return $clases[$this->estado_pieza] ?? 'bg-secondary';
    }

    /**
     * Obtiene la clase CSS según la condición
     */
    public function getCondicionClass()
    {
        $clases = [
            self::CONDICION_EXCELENTE => 'text-success',
            self::CONDICION_BUENO => 'text-primary',
            self::CONDICION_REGULAR => 'text-warning',
            self::CONDICION_MALO => 'text-danger',
        ];
        return $clases[$this->condicion] ?? 'text-muted';
    }

    /**
     * Registra un movimiento en el historial
     */
    public function registrarMovimiento($accion, $estadoAnterior = null, $estadoNuevo = null, $observaciones = null, $equipoDestino = null)
    {
        $historial = new HistorialPiezaReciclaje();
        $historial->pieza_id = $this->id;
        $historial->accion = $accion;
        $historial->estado_anterior = $estadoAnterior;
        $historial->estado_nuevo = $estadoNuevo;
        $historial->equipo_destino = $equipoDestino;
        $historial->observaciones = $observaciones;
        $historial->usuario = Yii::$app->user->isGuest ? 'Sistema' : Yii::$app->user->identity->username;
        return $historial->save();
    }

    /**
     * Cuenta piezas por estado
     */
    public static function contarPorEstado($estado)
    {
        return self::find()->where(['estado_pieza' => $estado])->count();
    }

    /**
     * Cuenta piezas por tipo
     */
    public static function contarPorTipo($tipo)
    {
        return self::find()->where(['tipo_pieza' => $tipo])->count();
    }

    /**
     * Obtiene estadísticas generales
     */
    public static function getEstadisticas()
    {
        $total = self::find()->count();
        $disponibles = self::contarPorEstado(self::ESTADO_DISPONIBLE);
        $enUso = self::contarPorEstado(self::ESTADO_EN_USO);
        $reservadas = self::contarPorEstado(self::ESTADO_RESERVADO);
        $danadas = self::contarPorEstado(self::ESTADO_DANADO);
        
        $tasaReciclaje = $total > 0 ? round((($disponibles + $enUso + $reservadas) / $total) * 100) : 0;
        
        return [
            'total' => $total,
            'disponibles' => $disponibles,
            'enUso' => $enUso,
            'reservadas' => $reservadas,
            'danadas' => $danadas,
            'tasaReciclaje' => $tasaReciclaje,
        ];
    }

    /**
     * Antes de guardar
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // Establecer usuario que registra o edita
        $username = Yii::$app->user->isGuest ? 'Sistema' : Yii::$app->user->identity->username;
        
        if ($insert) {
            $this->usuario_registro = $username;
        }
        $this->ultimo_editor = $username;

        return true;
    }

    /**
     * Después de guardar
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Registrar movimiento en historial (con manejo de errores)
        try {
            if ($insert) {
                $this->registrarMovimiento('Registro', null, $this->estado_pieza, 'Pieza registrada en inventario de reciclaje');
            } elseif (isset($changedAttributes['estado_pieza'])) {
                $this->registrarMovimiento(
                    'Cambio Estado',
                    $changedAttributes['estado_pieza'],
                    $this->estado_pieza,
                    'Estado actualizado',
                    $this->asignado_a
                );
            }
        } catch (\Exception $e) {
            // Log error pero no interrumpir el guardado
            Yii::error('Error al registrar historial: ' . $e->getMessage());
        }
    }
}
