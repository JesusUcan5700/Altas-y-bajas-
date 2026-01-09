<?php
namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\models\User;

/**
 * This is the model class for table "video_vigilancia".
 *
 * @property int $idVIDEO_VIGILANCIA
 * @property string $MARCA
 * @property string $MODELO
 * @property string $NUMERO_SERIE
 * @property string $NUMERO_INVENTARIO
 * @property string $DESCRIPCION
 * @property string $tipo_camara
 * @property string $EDIFICIO
 * @property string $ESTADO
 * @property string $ubicacion_edificio
 * @property string $ubicacion_detalle
 * @property string $EMISION_INVENTARIO
 * @property string $TIEMPO_TRANSCURRIDO
 * @property string $VIDEO_VIGILANCIA_COL
 * @property string $fecha
 * @property string $fecha_creacion
 * @property string $fecha_ultima_edicion
 * @property string $ultimo_editor
 *
 * @property User $ultimoEditorUser
 */
class VideoVigilancia extends \yii\db\ActiveRecord
{
    const ESTADO_ACTIVO = 'Activo';
    const ESTADO_INACTIVO = 'Inactivo(Sin Asignar)';
    const ESTADO_DANADO = 'dañado(Proceso de baja)';
    const ESTADO_MANTENIMIENTO = 'En Mantenimiento';
    const ESTADO_BAJA = 'BAJA';

    // Tipos de cámara
    const TIPO_FIJA = 'fija';
    const TIPO_PTZ = 'ptz';
    const TIPO_DOMO = 'domo';
    const TIPO_BULLET = 'bullet';
    const TIPO_TERMICA = 'termica';
    const TIPO_IP = 'ip';
    const TIPO_ANALOGICA = 'analogica';

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
    public static function tableName()
    {
        return 'video_vigilancia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO'], 'required'],
            [['fecha'], 'date', 'format' => 'yyyy-MM-dd'],
            [['fecha_creacion', 'fecha_ultima_edicion'], 'safe'],
            [['ultimo_editor'], 'string', 'max' => 100],
            [['tipo_camara'], 'string', 'max' => 50],
            [['MARCA', 'MODELO', 'NUMERO_SERIE', 'NUMERO_INVENTARIO', 'DESCRIPCION', 'EDIFICIO', 'EMISION_INVENTARIO', 'TIEMPO_TRANSCURRIDO', 'VIDEO_VIGILANCIA_COL'], 'string', 'max' => 45],
            [['ESTADO'], 'string', 'max' => 100],
            [['ubicacion_edificio'], 'string', 'max' => 15],
            [['ubicacion_detalle'], 'string', 'max' => 255],
            [['NUMERO_SERIE'], 'unique', 'message' => 'Este número de serie ya está registrado en otra cámara.'],
            [['NUMERO_INVENTARIO'], 'unique', 'message' => 'Este número de inventario ya está registrado en otra cámara.'],
            [['NUMERO_INVENTARIO'], 'unique'],
            [['ESTADO'], 'in', 'range' => [
                self::ESTADO_ACTIVO, 
                self::ESTADO_INACTIVO, 
                self::ESTADO_DANADO, 
                self::ESTADO_MANTENIMIENTO,
                self::ESTADO_BAJA
            ]],
            [['tipo_camara'], 'in', 'range' => [
                self::TIPO_FIJA,
                self::TIPO_PTZ,
                self::TIPO_DOMO,
                self::TIPO_BULLET,
                self::TIPO_TERMICA,
                self::TIPO_IP,
                self::TIPO_ANALOGICA
            ]],
            [['ESTADO'], 'default', 'value' => self::ESTADO_ACTIVO],
            [['tipo_camara'], 'default', 'value' => self::TIPO_FIJA],
            [['fecha'], 'default', 'value' => date('Y-m-d')],
            [['ubicacion_edificio'], 'in', 'range' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idVIDEO_VIGILANCIA' => 'ID Video Vigilancia',
            'MARCA' => 'Marca',
            'MODELO' => 'Modelo',
            'NUMERO_SERIE' => 'Número de Serie',
            'NUMERO_INVENTARIO' => 'Número de Inventario',
            'DESCRIPCION' => 'Descripción',
            'tipo_camara' => 'Tipo de Cámara',
            'EDIFICIO' => 'Edificio (Campo Anterior)',
            'ESTADO' => 'Estado',
            'ubicacion_edificio' => 'Edificio',
            'ubicacion_detalle' => 'Ubicación Detallada',
            'EMISION_INVENTARIO' => 'Emisión de Inventario',
            'TIEMPO_TRANSCURRIDO' => 'Tiempo Transcurrido',
            'VIDEO_VIGILANCIA_COL' => 'Columna Especial',
            'fecha' => 'Fecha de Instalación',
            'fecha_creacion' => 'Fecha de Creación',
            'fecha_ultima_edicion' => 'Última Edición',
            'ultimo_editor' => 'Último Editor',
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
        return self::find()->where(['ESTADO' => self::ESTADO_DANADO])->all();
    }

    /**
     * Contar equipos con estado dañado (proceso de baja)
     */
    public static function countEquiposDanados()
    {
        return self::find()->where(['ESTADO' => self::ESTADO_DANADO])->count();
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
     * Obtiene todas las cámaras disponibles
     * @return array
     */
    public static function getCamarasDisponibles()
    {
        return self::find()->all();
    }

    /**
     * Obtiene solo cámaras activas
     * @return array
     */
    public static function getCamarasActivas()
    {
        return self::find()->where(['ESTADO' => self::ESTADO_ACTIVO])->all();
    }

    /**
     * Obtiene cámaras por edificio
     * @param string $edificio
     * @return array
     */
    public static function getCamarasPorEdificio($edificio)
    {
        return self::find()->where(['EDIFICIO' => $edificio])->all();
    }

    /**
     * Busca una cámara por número de serie
     * @param string $numSerie
     * @return VideoVigilancia|null
     */
    public static function findByNumSerie($numSerie)
    {
        return self::findOne(['NUMERO_SERIE' => $numSerie]);
    }

    /**
     * Busca una cámara por número de inventario
     * @param string $numInventario
     * @return VideoVigilancia|null
     */
    public static function findByNumInventario($numInventario)
    {
        return self::findOne(['NUMERO_INVENTARIO' => $numInventario]);
    }

    /**
     * Obtiene el nombre completo de la cámara
     * @return string
     */
    public function getNombreCompleto()
    {
        return $this->MARCA . ' ' . $this->MODELO . ' - ' . $this->EDIFICIO;
    }

    /**
     * Verifica si la cámara está activa
     * @return bool
     */
    public function estaActiva()
    {
        return $this->ESTADO === self::ESTADO_ACTIVO;
    }

    /**
     * Verifica si la cámara está en mantenimiento
     * @return bool
     */
    public function estaEnMantenimiento()
    {
        return $this->ESTADO === self::ESTADO_MANTENIMIENTO;
    }

    /**
     * Obtiene el estado formateado para mostrar
     * @return string
     */
    public function getEstadoTexto()
    {
        $estados = self::getEstados();
        return isset($estados[$this->ESTADO]) ? $estados[$this->ESTADO] : $this->ESTADO;
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
     * Obtiene todos los edificios únicos
     * @return array
     */
    public static function getEdificiosUnicos()
    {
        return self::find()->select('EDIFICIO')->distinct()->column();
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
     * Cambia el estado a mantenimiento
     * @return bool
     */
    public function marcarMantenimiento()
    {
        $this->ESTADO = self::ESTADO_MANTENIMIENTO;
        return $this->save();
    }

    /**
     * Cambia el estado a activo
     * @return bool
     */
    public function activar()
    {
        $this->ESTADO = self::ESTADO_ACTIVO;
        return $this->save();
    }

    /**
     * Inicializa el modelo con valores por defecto
     */
    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            if (empty($this->ESTADO)) {
                $this->ESTADO = self::ESTADO_ACTIVO;
            }
            if (empty($this->fecha)) {
                $this->fecha = date('Y-m-d');
            }
        }
    }

    /**
     * Antes de guardar, asegurar valores por defecto
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->ESTADO)) {
                $this->ESTADO = self::ESTADO_ACTIVO;
            }
            if (empty($this->tipo_camara)) {
                $this->tipo_camara = self::TIPO_FIJA;
            }
            if (empty($this->fecha)) {
                $this->fecha = date('Y-m-d');
            }
            return true;
        }
        return false;
    }

    /**
     * Obtiene los tipos de cámara disponibles
     */
    public static function getTiposCamara()
    {
        return [
            self::TIPO_FIJA => 'Cámara Fija',
            self::TIPO_PTZ => 'Cámara PTZ (Pan-Tilt-Zoom)',
            self::TIPO_DOMO => 'Cámara Domo',
            self::TIPO_BULLET => 'Cámara Bullet',
            self::TIPO_TERMICA => 'Cámara Térmica',
            self::TIPO_IP => 'Cámara IP',
            self::TIPO_ANALOGICA => 'Cámara Analógica',
        ];
    }

    /**
     * Relación con el usuario que fue el último editor
     */
    public function getUltimoEditorUser()
    {
        return $this->hasOne(User::class, ['username' => 'ultimo_editor']);
    }

    /**
     * Obtiene el tiempo que lleva activo este equipo de videovigilancia
     * Calcula basándose en el campo 'fecha' (fecha definida por el usuario)
     */
    public function getTiempoActivo()
    {
        if (empty($this->fecha)) {
            return 'Fecha no definida';
        }

        try {
            $fechaInicio = new \DateTime($this->fecha);
            $fechaActual = new \DateTime();
            $intervalo = $fechaInicio->diff($fechaActual);

            $years = $intervalo->y;
            $months = $intervalo->m;
            $days = $intervalo->d;

            $partes = [];
            if ($years > 0) {
                $partes[] = $years . ($years == 1 ? ' año' : ' años');
            }
            if ($months > 0) {
                $partes[] = $months . ($months == 1 ? ' mes' : ' meses');
            }
            if ($days > 0 && $years == 0) { // Solo mostrar días si no hay años
                $partes[] = $days . ($days == 1 ? ' día' : ' días');
            }

            if (empty($partes)) {
                return 'Menos de 1 día';
            }

            return implode(', ', $partes);
        } catch (\Exception $e) {
            return 'Error en fecha';
        }
    }

    /**
     * Obtiene el tiempo transcurrido desde la última edición
     */
    public function getTiempoUltimaEdicion()
    {
        if (empty($this->fecha_ultima_edicion)) {
            return 'No disponible';
        }

        try {
            $fechaEdicion = new \DateTime($this->fecha_ultima_edicion);
            $fechaActual = new \DateTime();
            $intervalo = $fechaEdicion->diff($fechaActual);

            $days = $intervalo->days;
            $hours = $intervalo->h;
            $minutes = $intervalo->i;

            if ($days > 0) {
                return $days . ($days == 1 ? ' día' : ' días');
            } elseif ($hours > 0) {
                return $hours . ($hours == 1 ? ' hora' : ' horas') . 
                       ($minutes > 0 ? ', ' . $minutes . ($minutes == 1 ? ' minuto' : ' minutos') : '');
            } elseif ($minutes > 0) {
                return $minutes . ($minutes == 1 ? ' minuto' : ' minutos');
            } else {
                return 'Menos de 1 minuto';
            }
        } catch (\Exception $e) {
            return 'Error en fecha';
        }
    }

    /**
     * Obtiene información del último editor
     */
    public function getInfoUltimoEditor()
    {
        if (empty($this->ultimo_editor)) {
            return 'No definido';
        }

        // Intentar obtener información del usuario
        $usuario = $this->ultimoEditorUser;
        if ($usuario) {
            return $this->ultimo_editor;
        }

        return $this->ultimo_editor;
    }
}