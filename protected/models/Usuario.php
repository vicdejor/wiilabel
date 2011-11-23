<?php

/**
 * This is the model class for table "usuario".
 *
 * The followings are the available columns in table 'usuario':
 * @property string $id
 * @property string $codigo_usuario
 * @property integer $activo
 * @property string $nombre
 * @property string $primer_apellido
 * @property string $segundo_apellido
 * @property string $apodo
 * @property string $email
 * @property string $fecha_nacimiento
 * @property string $password
 * @property integer $perfil_publico
 * @property integer $avatar
 * @property string $codigo_wii
 */
class Usuario extends CActiveRecord
{

	public $re_password;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Usuario the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'usuario';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('codigo_usuario, password, re_password, nombre, apodo, email, fecha_nacimiento', 'required'),
			array('apodo', 'unique', 'className'=> 'Usuario'),
			array('email', 'unique', 'className'=> 'Usuario'),
			array('re_password', 'compare', 'compareAttribute'=>'password'),
			array('email', 'email'),
			array('activo, perfil_publico', 'numerical', 'integerOnly'=>true),
			array('nombre, codigo_usuario, apodo, codigo_wii', 'length', 'max'=>20),
			array('password, re_password', 'length', 'max'=>32),
			array('primer_apellido, segundo_apellido, email', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, codigo_usuario, activo, nombre, primer_apellido, segundo_apellido, apodo, email, fecha_nacimiento, perfil_publico, codigo_wii', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
	            'juego' => array(self::BELONGS_TO, 'Juego', 'id'),
	            'usuarios' => array(self::HAS_MANY, 'Usuario', 'id'),
	        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'codigo_usuario' => 'Codigo Usuario',
			'activo' => 'Activo',
			'nombre' => 'Nombre',
			'primer_apellido' => 'Primer Apellido',
			'segundo_apellido' => 'Segundo Apellido',
			'apodo' => 'Apodo',
			'email' => 'Email',
			'fecha_nacimiento' => 'Fecha Nacimiento',
			'password' => 'Contraseña',
			're_password' => 'Introduzca de nuevo la contraseña',
			'perfil_publico' => 'Perfil Publico',
			'avatar' => 'Avatar',
			'codigo_wii' => 'Codigo Wii',
			'verifyCode'=>'Código de verificación',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('codigo_usuario',$this->codigo_usuario,true);
		$criteria->compare('activo',$this->activo);
		$criteria->compare('nombre',$this->nombre,true);
		$criteria->compare('primer_apellido',$this->primer_apellido,true);
		$criteria->compare('segundo_apellido',$this->segundo_apellido,true);
		$criteria->compare('apodo',$this->apodo,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('fecha_nacimiento',$this->fecha_nacimiento,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('perfil_publico',$this->perfil_publico);
		$criteria->compare('codigo_wii',$this->codigo_wii,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function numVecesJugado($id_juego, $id_jugador)
	{
		$juego=Juego::model()->findByAttributes(array('id_juego'=>$id_juego, 'id_jugador'=>$id_jugador));
		return $juego->veces_jugado;
	}
	
	public function sonAmigos($id_jugador, $id_amigo)
	{
		//Comprobamos si A es amigo de B.
		$connection=Yii::app()->db;
		$sql='SELECT id FROM amistad WHERE id_jugador='.$id_jugador.' AND id_amigo='.$id_amigo.' AND estado="aceptada"';
		$dataReader=$connection->createCommand($sql)->query();
		if($dataReader->read()!==false)
		{
			return true;
		}
		else
		{
			//Comprobamos si B es amigo de A.
			$sql='SELECT id FROM amistad WHERE id_jugador='.$id_amigo.' AND id_amigo='.$id_jugador.' AND estado="aceptada"';
			$dataReader=$connection->createCommand($sql)->query();
			if($dataReader->read()!==false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	public function puedeSolicitarAmistad($id_jugador, $id_amigo)
	{
		$solicitud_pendiente=true;
		$connection=Yii::app()->db;
		$sql='SELECT id FROM amistad WHERE id_jugador='.$id_jugador.' AND id_amigo='.$id_amigo;
		$dataReader=$connection->createCommand($sql)->query();
		if($dataReader->read()==false)
		{
			//A no ha pedido amistad a B.
			$sql='SELECT id FROM amistad WHERE id_jugador='.$id_amigo.' AND id_amigo='.$id_jugador;
			$dataReader=$connection->createCommand($sql)->query();
			if($dataReader->read()==false)
			{
				//B no ha pedido amistad a A.
				$solicitud_pendiente=false;
			}
		}
		if($id_jugador!=$id_amigo && !$solicitud_pendiente)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function solicitudAmistadEnTramite($id_jugador, $id_amigo)
	{
		$connection=Yii::app()->db;
		$sql='SELECT id FROM amistad WHERE id_jugador='.$id_jugador.' AND id_amigo='.$id_amigo.' AND estado="pendiente"';
		$dataReader=$connection->createCommand($sql)->query();
		if($dataReader->read()==false)
		{
			//La solicitud de amista de A a B no está pendiente.
			$sql='SELECT id FROM amistad WHERE id_jugador='.$id_amigo.' AND id_amigo='.$id_jugador.' AND estado="pendiente"';
			$dataReader=$connection->createCommand($sql)->query();
			if($dataReader->read()==false)
			{
				//La solicitud de amista de B a A no está pendiente
				return false;
			}
		}
		return true;
	}
	
	public function solicitudesDeAmistad($id_usuario)
	{
		$solicitudes_pedidas=Amistad::model()->findAllByAttributes(array('id_amigo'=>$id_usuario, 'estado'=>'pendiente'));
		return $solicitudes_pedidas;
		//$solicitudes_por_aceptar=Amistad::model()->findByAttributes(array('id_amigo'=>$id_usuario, 'estado'=>'pendiente'));
	}
}
