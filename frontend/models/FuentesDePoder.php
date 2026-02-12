<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * Modelo para la tabla fuentes_de_poder
 */
class FuentesDePoder extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fuentes_de_poder';
    }

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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!Yii::$app->user->isGuest) {
                $this->ultimo_editor = Yii::$app->user->identity->username;
            } else {
                $this->ultimo_editor = 'Sistema';
            }
            
            // Si estamos en escenario simplificado, completar campos faltantes
            if ($this->scenario === 'simplificado' && $insert) {
                $this->TIPO = $this->TIPO ?: 'PSU';
                $this->VOLTAJE = $this->VOLTAJE ?: 'No especificado';
                $this->AMPERAJE = $this->AMPERAJE ?: 'No especificado';
                $this->POTENCIA_WATTS = $this->POTENCIA_WATTS ?: 'No especificado';
                $this->ESTADO = $this->ESTADO ?: 'Activo';
                $this->ubicacion_edificio = $this->ubicacion_edificio ?: 'Catálogo';
                $this->ubicacion_detalle = $this->ubicacion_detalle ?: 'Catálogo';
                $this->DESCRIPCION = $this->DESCRIPCION ?: 'Entrada de catálogo';
                
                // Generar números únicos solo si no existen
                if (empty($this->NUMERO_SERIE)) {
                    $timestamp = time() . rand(100, 999);
                    $this->NUMERO_SERIE = 'CAT-FP-' . $timestamp;
                }
                if (empty($this->NUMERO_INVENTARIO)) {
                    $timestamp = time() . rand(100, 999);
                    $this->NUMERO_INVENTARIO = 'CAT-FP-' . $timestamp;
                }
            } else {
                // Modo normal - Completar campos obligatorios automáticamente solo si están vacíos
                if (empty($this->TIPO)) {
                    $this->TIPO = 'PSU';
                }
                if (empty($this->NUMERO_INVENTARIO)) {
                    $this->NUMERO_INVENTARIO = 'FP-' . date('YmdHis');
                }
                if (empty($this->ESTADO)) {
                    $this->ESTADO = 'Inactivo(Sin Asignar)';
                }
                if (empty($this->ubicacion_detalle)) {
                    $this->ubicacion_detalle = 'Catálogo';
                }
            }
            
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [
            // Campos requeridos - modo normal
            [['MARCA', 'MODELO'], 'required', 'except' => 'simplificado'],
            
            // Campos requeridos - modo simplificado
            [['MARCA', 'MODELO'], 'required', 'on' => 'simplificado'],
            
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['MARCA', 'MODELO'], 'string', 'max' => 45],
            [['TIPO'], 'string', 'max' => 30, 'except' => 'simplificado'],
            [['VOLTAJE', 'AMPERAJE', 'POTENCIA_WATTS'], 'string', 'max' => 20, 'except' => 'simplificado'],
            [['NUMERO_SERIE'], 'string', 'max' => 50, 'except' => 'simplificado'],
            [['NUMERO_INVENTARIO'], 'string', 'max' => 45, 'except' => 'simplificado'],
            [['DESCRIPCION', 'ultimo_editor'], 'string', 'max' => 100, 'except' => 'simplificado'],
            [['ESTADO'], 'string', 'max' => 100, 'except' => 'simplificado'],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255, 'except' => 'simplificado'],
            
            // Otros campos opcionales
            [['TIPO', 'VOLTAJE', 'AMPERAJE', 'POTENCIA_WATTS', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'DESCRIPCION', 'ESTADO', 'ubicacion_edificio', 'ubicacion_detalle'], 'safe'],
            
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otra fuente de poder.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otra fuente de poder.'],
        ];
    }
    
    /**
     * Define scenarios para diferentes contextos
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        // Scenario para modo simplificado (catálogo)
        $scenarios['simplificado'] = ['MARCA', 'MODELO', 'TIPO', 'VOLTAJE', 'AMPERAJE', 'POTENCIA_WATTS', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'DESCRIPCION', 'ESTADO', 'ubicacion_edificio', 'ubicacion_detalle'];
        
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'idFuentePoder' => 'ID',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TIPO' => 'Tipo',
            'VOLTAJE' => 'Voltaje',
            'AMPERAJE' => 'Amperaje',
            'POTENCIA_WATTS' => 'Potencia (Watts)',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'DESCRIPCION' => 'Descripción',
            'ESTADO' => 'Estado',
            'ubicacion_edificio' => 'Edificio',
            'ubicacion_detalle' => 'Detalle de Ubicación',
            'fecha_creacion' => 'Fecha Creación',
            'fecha_ultima_edicion' => 'Fecha Última Edición',
            'ultimo_editor' => 'Último Editor',
        ];
    }

    public function getUltimoEditor()
    {
        return $this->hasOne(User::class, ['username' => 'ultimo_editor']);
    }

    public static function getEdificios()
    {
        $edificios = [];
        foreach (range('A', 'U') as $letra) {
            $edificios["Edificio $letra"] = "Edificio $letra";
        }
        return $edificios;
    }

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
}
