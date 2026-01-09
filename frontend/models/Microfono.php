<?php
// filepath: c:\wamp64\www\ALTAS_BAJAS\common\models\Microfono.php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "microfono".
 */
class Microfono extends \yii\db\ActiveRecord
{
    // Constantes para estados
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'inactivo';
    const ESTADO_DAÑADO = 'dañado';
    const ESTADO_MANTENIMIENTO = 'mantenimiento';

    // Tipos de micrófono
    const TIPO_DINAMICO = 'Dinámico';
    const TIPO_CONDENSADOR = 'Condensador';
    const TIPO_RIBBON = 'Ribbon';
    const TIPO_LAVALIER = 'Lavalier';
    const TIPO_SHOTGUN = 'Shotgun';
    const TIPO_USB = 'USB';
    const TIPO_INALAMBRICO = 'Inalámbrico';
    const TIPO_OTRO = 'Otro';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'microfono';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['MARCA', 'MODELO', 'TIPO', 'CONECTIVIDAD', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'ESTADO', 'FECHA'], 'required'],
            [['PATRON_POLAR', 'FRECUENCIA_RESPUESTA', 'DESCRIPCION', 'ubicacion_edificio', 'ubicacion_detalle'], 'safe'],
            [['FECHA'], 'date', 'format' => 'php:Y-m-d'],
            [['MARCA', 'MODELO', 'TIPO', 'PATRON_POLAR', 'CONECTIVIDAD', 'FRECUENCIA_RESPUESTA', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['DESCRIPCION'], 'string', 'max' => 100],
            [['ESTADO'], 'string', 'max' => 15],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otro micrófono.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otro micrófono.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idMicrofono' => 'ID',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TIPO' => 'Tipo',
            'PATRON_POLAR' => 'Patrón Polar',
            'CONECTIVIDAD' => 'Conectividad',
            'FRECUENCIA_RESPUESTA' => 'Frecuencia de Respuesta',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'DESCRIPCION' => 'Descripción',
            'ESTADO' => 'Estado',
            'FECHA' => 'Fecha',
            'ubicacion_edificio' => 'Ubicación Edificio',
            'ubicacion_detalle' => 'Detalle de Ubicación',
        ];
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
            
            // Si ESTADO está vacío, establecer activo por defecto
            if (empty($this->ESTADO)) {
                $this->ESTADO = self::ESTADO_ACTIVO;
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
            'Shure' => 'Shure',
            'Audio-Technica' => 'Audio-Technica',
            'Sennheiser' => 'Sennheiser',
            'AKG' => 'AKG',
            'Rode' => 'Rode',
            'Blue' => 'Blue',
            'Electro-Voice' => 'Electro-Voice',
            'Neumann' => 'Neumann',
            'Behringer' => 'Behringer',
            'Sony' => 'Sony',
            'Samson' => 'Samson',
            'CAD' => 'CAD',
            'MXL' => 'MXL',
            'Otra' => 'Otra'
        ];
    }

    /**
     * Obtiene los tipos de micrófono disponibles
     */
    public static function getTipos()
    {
        return [
            self::TIPO_DINAMICO => 'Dinámico',
            self::TIPO_CONDENSADOR => 'Condensador',
            self::TIPO_RIBBON => 'Ribbon',
            self::TIPO_LAVALIER => 'Lavalier',
            self::TIPO_SHOTGUN => 'Shotgun',
            self::TIPO_USB => 'USB',
            self::TIPO_INALAMBRICO => 'Inalámbrico',
            self::TIPO_OTRO => 'Otro',
        ];
    }

    /**
     * Obtiene los patrones polares disponibles
     */
    public static function getPatronesPolar()
    {
        return [
            'Cardioide' => 'Cardioide',
            'Supercardioide' => 'Supercardioide',
            'Hipercardioide' => 'Hipercardioide',
            'Omnidireccional' => 'Omnidireccional',
            'Bidireccional' => 'Bidireccional (Figura 8)',
            'Shotgun' => 'Shotgun',
            'Variable' => 'Variable',
        ];
    }

    /**
     * Obtiene los tipos de conectividad disponibles
     */
    public static function getConectividad()
    {
        return [
            'XLR' => 'XLR',
            'USB' => 'USB',
            '3.5mm TRS' => '3.5mm TRS',
            '1/4" TRS' => '1/4" TRS',
            'Inalámbrico' => 'Inalámbrico',
            'Lightning' => 'Lightning (iOS)',
            'USB-C' => 'USB-C',
            'Bluetooth' => 'Bluetooth',
            '2.4GHz' => '2.4GHz',
            'Mixto' => 'Conexiones Mixtas',
        ];
    }

    /**
     * Obtiene las frecuencias de respuesta comunes
     */
    public static function getFrecuenciasRespuesta()
    {
        return [
            '20Hz-20kHz' => '20Hz - 20kHz (Rango completo)',
            '50Hz-15kHz' => '50Hz - 15kHz (Voz)',
            '40Hz-18kHz' => '40Hz - 18kHz (Instrumental)',
            '20Hz-16kHz' => '20Hz - 16kHz (Grabación)',
            '80Hz-12kHz' => '80Hz - 12kHz (Comunicación)',
            'Personalizada' => 'Otra frecuencia',
        ];
    }

    /**
     * Obtiene los estados disponibles
     */
    public static function getEstados()
    {
        return [
            self::ESTADO_ACTIVO => 'Activo',
            self::ESTADO_INACTIVO => 'Inactivo',
            self::ESTADO_DAÑADO => 'Dañado',
            self::ESTADO_MANTENIMIENTO => 'En Mantenimiento',
        ];
    }

    /**
     * Obtiene los edificios disponibles
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