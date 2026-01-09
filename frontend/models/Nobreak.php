<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "nobreak".
 *
 * @property int $idNOBREAK
 * @property string $MARCA
 * @property string $MODELO
 * @property string $CAPACIDAD
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string $DESCRIPCION
 * @property string $Estado
 * @property string $EMISION_INVENTARIO
 * @property string|null $ubicacion_edificio
 * @property string|null $ubicacion_detalle
 * @property string|null $fecha_creacion
 * @property string|null $fecha_ultima_edicion
 * @property string|null $ultimo_editor
 */
class Nobreak extends \yii\db\ActiveRecord
{
    // Constantes para estados
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo';
    const ESTADO_BAJA = 'Baja';
    const ESTADO_REPARACION = 'Reparación';
    const ESTADO_BAJA_DEFINITIVA = 'BAJA';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nobreak';
    }

    /**
     * {@inheritdoc}
     */
    public static function primaryKey()
    {
        return ['idNOBREAK'];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // Escenario para catálogo: solo requiere MARCA y MODELO
        $scenarios['catalogo'] = ['MARCA', 'MODELO', 'Estado', 'ubicacion_detalle', 'CAPACIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'DESCRIPCION', 'EMISION_INVENTARIO', 'ubicacion_edificio'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // En modo catálogo solo MARCA y MODELO son requeridos
            [['MARCA', 'MODELO'], 'required'],
            [['CAPACIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'Estado', 'EMISION_INVENTARIO'], 'required', 'except' => 'catalogo'],
            [['EMISION_INVENTARIO', 'fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['MARCA', 'MODELO', 'CAPACIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['DESCRIPCION'], 'string', 'max' => 100],
            [['Estado'], 'string', 'max' => 30],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['Estado'], 'in', 'range' => array_keys(self::getEstados())],
            // Validaciones de unicidad
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otro equipo.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otro equipo.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idNOBREAK' => 'ID',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'CAPACIDAD' => 'Capacidad',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'DESCRIPCION' => 'Descripción',
            'Estado' => 'Estado',
            'EMISION_INVENTARIO' => 'Fecha de Emisión de Inventario',
            'ubicacion_edificio' => 'Ubicación Edificio',
            'ubicacion_detalle' => 'Ubicación Detalle',
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
     * Opciones para el combo Estado - Estandarizados
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
     * Opciones para el combo Ubicación Edificio
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
     * Opciones para el combo Ubicación Detalle
     */
    public static function getUbicacionesDetalle()
    {
        return [
            'Sala de Servidores' => 'Sala de Servidores',
            'Recepción' => 'Recepción',
            'Gerencia' => 'Gerencia',
            'Contabilidad' => 'Contabilidad',
            'Sistemas' => 'Sistemas',
        ];
    }
}