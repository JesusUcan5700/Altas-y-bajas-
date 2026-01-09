<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Modelo para la tabla "historial_piezas_reciclaje"
 * Registra los movimientos y cambios de las piezas de reciclaje
 *
 * @property int $id
 * @property int $pieza_id
 * @property string $accion
 * @property string|null $estado_anterior
 * @property string|null $estado_nuevo
 * @property string|null $equipo_destino
 * @property string|null $observaciones
 * @property string|null $usuario
 * @property string|null $fecha_movimiento
 */
class HistorialPiezaReciclaje extends ActiveRecord
{
    // Constantes de acciones
    const ACCION_REGISTRO = 'Registro';
    const ACCION_ASIGNACION = 'Asignación';
    const ACCION_DEVOLUCION = 'Devolución';
    const ACCION_BAJA = 'Baja';
    const ACCION_EDICION = 'Edición';
    const ACCION_CAMBIO_ESTADO = 'Cambio Estado';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historial_piezas_reciclaje';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pieza_id', 'accion'], 'required'],
            [['pieza_id'], 'integer'],
            [['observaciones'], 'string'],
            [['fecha_movimiento'], 'safe'],
            [['accion', 'estado_anterior', 'estado_nuevo'], 'string', 'max' => 50],
            [['equipo_destino', 'usuario'], 'string', 'max' => 100],
            [['pieza_id'], 'exist', 'skipOnError' => true, 'targetClass' => PiezaReciclaje::class, 'targetAttribute' => ['pieza_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pieza_id' => 'Pieza',
            'accion' => 'Acción',
            'estado_anterior' => 'Estado Anterior',
            'estado_nuevo' => 'Estado Nuevo',
            'equipo_destino' => 'Equipo Destino',
            'observaciones' => 'Observaciones',
            'usuario' => 'Usuario',
            'fecha_movimiento' => 'Fecha',
        ];
    }

    /**
     * Obtiene la pieza relacionada
     */
    public function getPieza()
    {
        return $this->hasOne(PiezaReciclaje::class, ['id' => 'pieza_id']);
    }

    /**
     * Lista de acciones disponibles
     */
    public static function getAcciones()
    {
        return [
            self::ACCION_REGISTRO => self::ACCION_REGISTRO,
            self::ACCION_ASIGNACION => self::ACCION_ASIGNACION,
            self::ACCION_DEVOLUCION => self::ACCION_DEVOLUCION,
            self::ACCION_BAJA => self::ACCION_BAJA,
            self::ACCION_EDICION => self::ACCION_EDICION,
            self::ACCION_CAMBIO_ESTADO => self::ACCION_CAMBIO_ESTADO,
        ];
    }

    /**
     * Obtiene el ícono según la acción
     */
    public function getIconoAccion()
    {
        $iconos = [
            self::ACCION_REGISTRO => 'fa-plus-circle text-success',
            self::ACCION_ASIGNACION => 'fa-arrow-right text-primary',
            self::ACCION_DEVOLUCION => 'fa-arrow-left text-info',
            self::ACCION_BAJA => 'fa-times-circle text-danger',
            self::ACCION_EDICION => 'fa-edit text-warning',
            self::ACCION_CAMBIO_ESTADO => 'fa-exchange-alt text-secondary',
        ];
        return $iconos[$this->accion] ?? 'fa-circle text-muted';
    }
}
