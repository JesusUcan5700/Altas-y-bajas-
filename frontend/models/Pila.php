<?php
// filepath: c:\wamp64\www\ALTAS_BAJAS\common\models\Pila.php

namespace backend\models;


use Yii;

/**
 * This is the model class for table "baterias".
 */
class Pila extends \yii\db\ActiveRecord
{
    // Constantes para estados estandarizados
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DAÑADO = 'dañado(Proceso de baja)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';

    // Tipos de baterías
    const TIPO_ALCALINA = 'Alcalina';
    const TIPO_LITIO = 'Litio';
    const TIPO_RECARGABLE = 'Recargable';
    const TIPO_CARBON_ZINC = 'Carbón-Zinc';
    const TIPO_OTRO = 'Otro';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'baterias';  // Mantén el nombre de la tabla como está
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['MARCA', 'TIPO'], 'required'],
            [['RECARGABLE'], 'boolean'],
            [['FECHA', 'FECHA_VENCIMIENTO', 'FECHA_REEMPLAZO'], 'date', 'format' => 'php:Y-m-d'],
            [['MARCA', 'MODELO', 'USO', 'NUMERO_INVENTARIO', 'NUMERO_SERIE'], 'string', 'max' => 45],
            [['TIPO', 'CAPACIDAD'], 'string', 'max' => 20],
            [['FORMATO_PILA', 'VOLTAJE'], 'string', 'max' => 10],
            [['DESCRIPCION', 'USO_PERSONALIZADO'], 'string'],
            [['ESTADO'], 'string', 'max' => 15],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255],
            [['FORMATO_PILA', 'VOLTAJE', 'CAPACIDAD', 'USO', 'DESCRIPCION', 'NUMERO_INVENTARIO', 'USO_PERSONALIZADO', 'NUMERO_SERIE'], 'safe'],
            [['FECHA', 'ESTADO', 'FECHA_VENCIMIENTO', 'FECHA_REEMPLAZO', 'ubicacion_edificio', 'ubicacion_detalle'], 'safe'],
            [['NUMERO_SERIE'], 'validarNumSerie', 'skipOnEmpty' => true],
            [['NUMERO_INVENTARIO'], 'validarNumInventario', 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TIPO' => 'Tipo de Batería',
            'FORMATO_PILA' => 'Formato/Tamaño',
            'VOLTAJE' => 'Voltaje',
            'CAPACIDAD' => 'Capacidad',
            'USO' => 'Uso/Aplicación',
            'USO_PERSONALIZADO' => 'Uso Personalizado',
            'RECARGABLE' => 'Recargable',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'NUMERO_SERIE' => 'Número de Serie',
            'DESCRIPCION' => 'Descripción',
            'ESTADO' => 'Estado',
            'FECHA' => 'Fecha de Registro',
            'FECHA_VENCIMIENTO' => 'Fecha de Vencimiento',
            'FECHA_REEMPLAZO' => 'Fecha de Reemplazo',
            'ubicacion_edificio' => 'Ubicación (Edificio/Piso/Oficina)',
            'ubicacion_detalle' => 'Detalle de Ubicación',
        ];
    }

    /**
     * Establece valores por defecto antes de validar
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (empty($this->FECHA)) {
                $this->FECHA = date('Y-m-d');
            }
            
            if (empty($this->ESTADO)) {
                $this->ESTADO = self::ESTADO_ACTIVO;
            }
            
            return true;
        }
        return false;
    }

    /**
     * Antes de guardar, si USO es "Otro", usar el valor personalizado
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->USO === 'Otro' && !empty($this->USO_PERSONALIZADO)) {
                $this->USO = $this->USO_PERSONALIZADO;
            }
            return true;
        }
        return false;
    }

    /**
     * Obtiene las marcas disponibles
     */
    public static function getMarcas()
    {
        return [
            'Duracell' => 'Duracell',
            'Energizer' => 'Energizer',
            'Rayovac' => 'Rayovac',
            'Panasonic' => 'Panasonic',
            'Sony' => 'Sony',
            'Varta' => 'Varta',
            'GP' => 'GP Batteries',
            'Kirkland' => 'Kirkland',
            'AmazonBasics' => 'Amazon Basics',
            'Eveready' => 'Eveready',
            'Maxell' => 'Maxell',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene los tipos de batería disponibles
     */
    public static function getTipos()
    {
        return [
            self::TIPO_ALCALINA => 'Alcalina',
            self::TIPO_LITIO => 'Litio',
            self::TIPO_RECARGABLE => 'Recargable (NiMH)',
            self::TIPO_CARBON_ZINC => 'Carbón-Zinc',
            self::TIPO_OTRO => 'Otro',
        ];
    }

    /**
     * Obtiene los formatos/tamaños disponibles
     */
    public static function getFormatos()
    {
        return [
            'AA' => 'AA (LR6)',
            'AAA' => 'AAA (LR03)',
            'C' => 'C (LR14)',
            'D' => 'D (LR20)',
            '9V' => '9V (6LR61)',
            'CR2032' => 'CR2032 (Botón)',
            'CR2025' => 'CR2025 (Botón)',
        ];
    }

    /**
     * Obtiene los voltajes disponibles
     */
    public static function getVoltajes()
    {
        return [
            '1.5V' => '1.5 Voltios',
            '3V' => '3 Voltios',
            '3.7V' => '3.7 Voltios',
            '4.5V' => '4.5 Voltios',
            '6V' => '6 Voltios',
            '9V' => '9 Voltios',
        ];
    }

    /**
     * Obtiene las capacidades disponibles
     */
    public static function getCapacidades()
    {
        return [
            '2000mAh' => '2000 mAh',
            '2500mAh' => '2500 mAh',
            '3000mAh' => '3000 mAh',
            '3500mAh' => '3500 mAh',
            '4000mAh' => '4000 mAh',
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
        ];
    }

    /**
     * Obtiene los usos/aplicaciones disponibles
     */
    public static function getUsos()
    {
        return [
            'Multímetro' => 'Multímetro',
            'Control Aire' => 'Control Aire',
            'Micrófonos' => 'Micrófonos',
            'Otro' => 'Otro'
        ];
    }

    /**
     * Validador personalizado: Verifica que NUMERO_SERIE sea único en TODOS los registros
     * sin importar el estado
     */
    public function validarNumSerie($attribute, $params)
    {
        if (empty($this->$attribute)) {
            return;
        }
        $query = self::find()->where(['NUMERO_SERIE' => $this->$attribute]);

        // Si es una actualización, excluir el registro actual
        if (!$this->isNewRecord) {
            // Para Pila, el ID podría ser diferente. Vamos a asumir que es 'id' o buscar el primaryKey
            $pk = $this->primaryKey();
            if (!empty($pk) && is_array($pk)) {
                $pkField = $pk[0];
            } else {
                $pkField = 'id';
            }
            $query->andWhere(['!=', $pkField, $this->{$pkField}]);
        }

        if ($query->exists()) {
            $this->addError($attribute, 'Este número de serie ya está registrado (incluso si está dado de baja).');
        }
    }

    /**
     * Validador personalizado: Verifica que NUMERO_INVENTARIO sea único en TODOS los registros
     * sin importar el estado
     */
    public function validarNumInventario($attribute, $params)
    {
        if (empty($this->$attribute)) {
            return;
        }
        $query = self::find()->where(['NUMERO_INVENTARIO' => $this->$attribute]);

        // Si es una actualización, excluir el registro actual
        if (!$this->isNewRecord) {
            // Para Pila, el ID podría ser diferente. Vamos a asumir que es 'id' o buscar el primaryKey
            $pk = $this->primaryKey();
            if (!empty($pk) && is_array($pk)) {
                $pkField = $pk[0];
            } else {
                $pkField = 'id';
            }
            $query->andWhere(['!=', $pkField, $this->{$pkField}]);
        }

        if ($query->exists()) {
            $this->addError($attribute, 'Este número de inventario ya está registrado (incluso si está dado de baja).');
        }
    }

    /**
     * Obtiene las opciones para recargable
     */
    public static function getRecargableOptions()
    {
        return [
            0 => 'No',
            1 => 'Sí'
        ];
    }

    /**
     * Obtiene las ubicaciones de edificio disponibles
     */
    public static function getUbicacionesEdificio()
    {
        return [
            'Edificio A - Piso 1' => 'Edificio A - Piso 1',
            'Edificio A - Piso 2' => 'Edificio A - Piso 2',
            'Edificio A - Piso 3' => 'Edificio A - Piso 3',
            'Edificio B - Piso 1' => 'Edificio B - Piso 1',
            'Edificio B - Piso 2' => 'Edificio B - Piso 2',
            'Laboratorio' => 'Laboratorio',
            'Almacén' => 'Almacén',
            'Oficina Principal' => 'Oficina Principal',
            'Sala de Servidores' => 'Sala de Servidores',
            'Otro' => 'Otro',
        ];
    }
}