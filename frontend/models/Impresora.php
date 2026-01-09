<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "impresora".
 *
 * @property int $idIMPRESORA
 * @property string $MARCA
 * @property string $MODELO
 * @property string $TIPO
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string $EMISION_INVENTARIO
 * @property string $DESCRIPCION
 * @property string $Estado
 * @property string $propia_rentada
 * @property string $ubicacion_edificio
 * @property string $ubicacion_detalle
 * @property string $fecha_creacion
 * @property string $fecha_ultima_edicion
 * @property string $ultimo_editor
 */
class Impresora extends \yii\db\ActiveRecord
{
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_BAJA = 'BAJA';

    const TIPO_LASER = 'Láser';
    const TIPO_INKJET = 'Inyección de Tinta';
    const TIPO_MULTIFUNCIONAL = 'Multifuncional';
    const TIPO_PLOTTER = 'Plotter';
    const TIPO_MATRICIAL = 'Matricial';
    const TIPO_TERMICA = 'Térmica';

    const PROPIEDAD_PROPIA = 'propia';
    const PROPIEDAD_RENTADA = 'rentada';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'impresora';
    }

    /**
     * {@inheritdoc}
     */
    public static function primaryKey()
    {
        return ['idIMPRESORA'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['MARCA', 'MODELO', 'TIPO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'EMISION_INVENTARIO', 'Estado', 'propia_rentada'], 'required'],
            [['EMISION_INVENTARIO'], 'date', 'format' => 'yyyy-MM-dd'],
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['MARCA', 'MODELO', 'TIPO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'EMISION_INVENTARIO', 'DESCRIPCION'], 'string', 'max' => 45],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['Estado'], 'string', 'max' => 100],
            [['propia_rentada'], 'string', 'max' => 10],
            [['ubicacion_edificio'], 'string', 'max' => 15],
            [['ubicacion_detalle'], 'string', 'max' => 255],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otra impresora.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otra impresora.'],
            [['Estado'], 'in', 'range' => [
                self::ESTADO_ACTIVO, 
                self::ESTADO_INACTIVO, 
                self::ESTADO_DANADO, 
                self::ESTADO_MANTENIMIENTO,
                self::ESTADO_BAJA
            ]],
            [['TIPO'], 'in', 'range' => array_keys(self::getTipos())],
            [['propia_rentada'], 'in', 'range' => [self::PROPIEDAD_PROPIA, self::PROPIEDAD_RENTADA]],
            [['Estado'], 'default', 'value' => self::ESTADO_ACTIVO],
            [['TIPO'], 'default', 'value' => self::TIPO_INKJET],
            [['propia_rentada'], 'default', 'value' => self::PROPIEDAD_PROPIA],
            [['EMISION_INVENTARIO'], 'default', 'value' => date('Y-m-d')],
            [['ultimo_editor'], 'default', 'value' => 'Sistema'],
            [['ubicacion_edificio'], 'in', 'range' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idIMPRESORA' => 'ID Impresora',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'TIPO' => 'Tipo de Impresora',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'EMISION_INVENTARIO' => 'Emisión de Inventario',
            'DESCRIPCION' => 'Descripción',
            'Estado' => 'Estado',
            'propia_rentada' => 'Propiedad',
            'ubicacion_edificio' => 'Edificio',
            'ubicacion_detalle' => 'Ubicación Detallada',
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
                'skipUpdateOnClean' => false,
            ],
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
     * Obtiene los tipos de impresora disponibles
     */
    public static function getTipos()
    {
        return [
            self::TIPO_LASER => 'Láser',
            self::TIPO_INKJET => 'Inyección de Tinta',
            self::TIPO_MULTIFUNCIONAL => 'Multifuncional',
            self::TIPO_PLOTTER => 'Plotter',
            self::TIPO_MATRICIAL => 'Matricial',
            self::TIPO_TERMICA => 'Térmica',
        ];
    }

    /**
     * Obtiene las opciones de propiedad disponibles
     */
    public static function getPropiedades()
    {
        return [
            self::PROPIEDAD_PROPIA => 'Propia',
            self::PROPIEDAD_RENTADA => 'Rentada',
        ];
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
     * Obtiene todas las impresoras disponibles
     * @return array
     */
    public static function getImpresorasDisponibles()
    {
        return self::find()->all();
    }

    /**
     * Obtiene solo impresoras activas
     * @return array
     */
    public static function getImpresorasActivas()
    {
        return self::find()->where(['Estado' => self::ESTADO_ACTIVO])->all();
    }

    /**
     * Obtiene solo impresoras inactivas
     * @return array
     */
    public static function getImpresorasInactivas()
    {
        return self::find()->where(['Estado' => self::ESTADO_INACTIVO])->all();
    }

    /**
     * Busca una impresora por número de serie
     * @param string $numSerie
     * @return Impresora|null
     */
    public static function findByNumSerie($numSerie)
    {
        return self::findOne(['NUMERO_SERIE' => $numSerie]);
    }

    /**
     * Busca una impresora por número de inventario
     * @param string $numInventario
     * @return Impresora|null
     */
    public static function findByNumInventario($numInventario)
    {
        return self::findOne(['NUMERO_INVENTARIO' => $numInventario]);
    }

    /**
     * Obtiene el nombre completo de la impresora
     * @return string
     */
    public function getNombreCompleto()
    {
        return $this->MARCA . ' ' . $this->MODELO;
    }

    /**
     * Verifica si la impresora tiene todos los campos requeridos
     * @return bool
     */
    public function estaCompleta()
    {
        return !empty($this->MARCA) && !empty($this->MODELO) && 
               !empty($this->NUMERO_SERIE) && !empty($this->NUMERO_INVENTARIO);
    }

    /**
     * Verifica si la impresora está activa
     * @return bool
     */
    public function estaActiva()
    {
        return $this->Estado === self::ESTADO_ACTIVO;
    }

    /**
     * Verifica si la impresora está inactiva
     * @return bool
     */
    public function estaInactiva()
    {
        return $this->Estado === self::ESTADO_INACTIVO;
    }

    /**
     * Cambia el estado de la impresora a activa
     * @return bool
     */
    public function activar()
    {
        $this->Estado = self::ESTADO_ACTIVO;
        return $this->save();
    }

    /**
     * Cambia el estado de la impresora a inactiva
     * @return bool
     */
    public function desactivar()
    {
        $this->Estado = self::ESTADO_INACTIVO;
        return $this->save();
    }

    /**
     * Obtiene el estado formateado para mostrar
     * @return string
     */
    public function getEstadoTexto()
    {
        $estados = self::getEstados();
        return isset($estados[$this->Estado]) ? $estados[$this->Estado] : $this->Estado;
    }

    /**
     * Obtiene la fecha formateada
     * @return string
     */
    public function getFechaFormateada()
    {
        return $this->fecha ? Yii::$app->formatter->asDate($this->fecha, 'dd/MM/yyyy') : '';
    }

    /**
     * Obtiene impresoras por marca
     * @param string $marca
     * @return array
     */
    public static function findByMarca($marca)
    {
        return self::find()->where(['MARCA' => $marca])->all();
    }

    /**
     * Obtiene todas las marcas únicas
     * @return array
     */
    public static function getMarcasUnicas()
    {
        return self::find()->select('MARCA')->distinct()->column();
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
            if (empty($this->TIPO)) {
                $this->TIPO = self::TIPO_INKJET;
            }
            if (empty($this->EMISION_INVENTARIO)) {
                $this->EMISION_INVENTARIO = date('Y-m-d');
            }
        }
    }

    /**
     * Antes de guardar, asegurar valores por defecto y configurar auditoría
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->Estado)) {
                $this->Estado = self::ESTADO_ACTIVO;
            }
            
            // Solo establecer valor por defecto para registros nuevos o si realmente está vacío/null
            if ($insert && ($this->propia_rentada === null || $this->propia_rentada === '')) {
                $this->propia_rentada = self::PROPIEDAD_PROPIA;
            }
            
            // Configurar el editor actual
            $this->ultimo_editor = $this->getCurrentUser();
            
            return true;
        }
        return false;
    }

    /**
     * Obtiene el usuario actual que está logueado
     */
    private function getCurrentUser()
    {
        // Verificar si Yii está inicializado y hay un usuario logueado
        if (isset(\Yii::$app) && !\Yii::$app->user->isGuest) {
            // Obtener el usuario logueado actual
            $user = \Yii::$app->user->identity;
            if ($user && isset($user->username)) {
                return $user->username;
            }
        }
        
        // Si no hay usuario logueado, usar 'Sistema' como fallback
        return 'Sistema';
    }

    /**
     * Obtiene información completa del usuario que editó
     */
    public function getInfoUsuarioEditor()
    {
        if (empty($this->ultimo_editor) || $this->ultimo_editor === 'Sistema') {
            return [
                'username' => $this->ultimo_editor ?? 'Sistema',
                'email' => null,
                'id' => null,
                'display_name' => $this->ultimo_editor ?? 'Sistema'
            ];
        }

        // Buscar el usuario usando la relación
        $usuario = $this->usuarioEditor;
        if ($usuario) {
            return [
                'username' => $usuario->username,
                'email' => $usuario->email,
                'id' => $usuario->id,
                'display_name' => $usuario->username . ' (' . $usuario->email . ')'
            ];
        }

        return [
            'username' => $this->ultimo_editor,
            'email' => null,
            'id' => null,
            'display_name' => $this->ultimo_editor
        ];
    }

    /**
     * Obtiene los días que lleva activa la impresora desde la emisión
     * @return int
     */
    public function getDiasActivo()
    {
        if (empty($this->EMISION_INVENTARIO)) {
            return 0;
        }
        
        try {
            // Limpiar y validar la fecha
            $fechaStr = trim($this->EMISION_INVENTARIO);
            if (empty($fechaStr)) {
                return 0;
            }
            
            $fechaEmision = new \DateTime($fechaStr);
            $fechaActual = new \DateTime();
            
            // Calcular diferencia en días
            $diferenciaMilisegundos = $fechaActual->getTimestamp() - $fechaEmision->getTimestamp();
            $dias = floor($diferenciaMilisegundos / (60 * 60 * 24));
            
            // Verificar que la fecha no esté en el futuro
            if ($dias < 0) {
                return 0;
            }
            
            return $dias;
            
        } catch (\Exception $e) {
            // Log del error para debugging
            error_log("Error calculando días activo para impresora {$this->idIMPRESORA}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene los años que lleva activa la impresora desde la emisión
     * @return float
     */
    public function getAnosActivo()
    {
        $dias = $this->getDiasActivo();
        if ($dias == 0) {
            return 0;
        }
        // Usar exactamente la misma división que JavaScript: 365.25
        return round($dias / 365.25, 2);
    }

    /**
     * Obtiene solo el texto de años activo
     * @return string
     */
    public function getAnosActivoTexto()
    {
        $dias = $this->getDiasActivo();
        
        if ($dias == 0) {
            return 'Sin fecha';
        }
        
        $anos = $this->getAnosActivo();
        
        if ($anos < 1) {
            return 'Menos de 1 año';
        } else if ($anos == 1) {
            return '1 año';
        } else {
            return sprintf('%.1f años', $anos);
        }
    }

    /**
     * Obtiene información detallada del último editor
     */
    public function getInfoUltimaEdicion()
    {
        if (empty($this->fecha_ultima_edicion)) {
            return 'Sin ediciones registradas';
        }
        
        $userInfo = $this->getInfoUsuarioEditor();
        
        $fecha = new \DateTime($this->fecha_ultima_edicion);
        $ahora = new \DateTime();
        $diferencia = $ahora->diff($fecha);
        
        $tiempo = '';
        if ($diferencia->days > 0) {
            $tiempo = $diferencia->days . ' día' . ($diferencia->days > 1 ? 's' : '');
        } elseif ($diferencia->h > 0) {
            $tiempo = $diferencia->h . ' hora' . ($diferencia->h > 1 ? 's' : '');
        } else {
            $tiempo = $diferencia->i . ' minuto' . ($diferencia->i > 1 ? 's' : '');
        }
        
        return "Editado por {$userInfo['display_name']} hace {$tiempo}";
    }

    /**
     * Relación con la tabla user para obtener datos del editor
     */
    public function getUsuarioEditor()
    {
        return $this->hasOne(\common\models\User::class, ['username' => 'ultimo_editor']);
    }
}