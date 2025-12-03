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
            
            // Completar campos obligatorios automáticamente para formulario simplificado
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
            
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [
            [['MARCA', 'MODELO'], 'required'],
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['MARCA', 'MODELO'], 'string', 'max' => 45],
            [['TIPO'], 'string', 'max' => 30],
            [['VOLTAJE', 'AMPERAJE', 'POTENCIA_WATTS'], 'string', 'max' => 20],
            [['NUMERO_SERIE'], 'string', 'max' => 50],
            [['NUMERO_INVENTARIO'], 'string', 'max' => 45],
            [['DESCRIPCION', 'ultimo_editor'], 'string', 'max' => 100],
            [['ESTADO'], 'string', 'max' => 100],
            [['ubicacion_edificio', 'ubicacion_detalle'], 'string', 'max' => 255],
        ];
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
}
