<?php

/**
 * This is the model class for table "juego".
 *
 * The followings are the available columns in table 'juego':
 * @property string $id
 * @property string $id_juego
 * @property string $id_jugador
 * @property string $titulo
 * @property double $veces_jugado
 * @property string $jugado_ultima_vez
 */
class Juego extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Juego the static model class
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
		return 'juego';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_juego, id_jugador, titulo, veces_jugado, jugado_ultima_vez', 'required'),
			array('veces_jugado', 'numerical'),
			array('id_juego, id_jugador', 'length', 'max'=>20),
			array('titulo', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, id_juego, id_jugador, titulo, veces_jugado, jugado_ultima_vez', 'safe', 'on'=>'search'),
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
			'id_juego' => 'Id Juego',
			'id_jugador' => 'Id Jugador',
			'titulo' => 'Titulo',
			'veces_jugado' => 'Veces Jugado',
			'jugado_ultima_vez' => 'Jugado Ultima Vez',
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
		$criteria->compare('id_juego',$this->id_juego,true);
		$criteria->compare('id_jugador',$this->id_jugador,true);
		$criteria->compare('titulo',$this->titulo,true);
		$criteria->compare('veces_jugado',$this->veces_jugado);
		$criteria->compare('jugado_ultima_vez',$this->jugado_ultima_vez,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
		/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function searchByIdJugador($id_jugador)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('id_juego',$this->id_juego,true);
		$criteria->compare('id_jugador',$this->id_jugador,true);
		$criteria->compare('titulo',$this->titulo,true);
		$criteria->compare('veces_jugado',$this->veces_jugado);
		$criteria->compare('jugado_ultima_vez',$this->jugado_ultima_vez,true);
		
		$criteria->condition = "id_jugador=:id_jugador";
		$criteria->params = array(':id_jugador' => $id_jugador);
		$criteria->order = 'jugado_ultima_vez DESC';
		//$criteria->limit = 10;

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/*
	*Devuelve el cover del juego a partir del id del juego
	*/
	public function coverByIdJuego($id_juego)
	{
		$cover='images/wii/cover3D/noimage.png';
		
		if(file_exists('images/wii/cover3D/DE/'.$id_juego.'.png'))
		{
			$cover='images/wii/cover3D/DE/'.$id_juego.'.png';
			return $cover;
		}
		elseif(file_exists('images/wii/cover3D/EN/'.$id_juego.'.png'))
		{
			$cover='images/wii/cover3D/EN/'.$id_juego.'.png';
			return $cover;
		}
		elseif(file_exists('images/wii/cover3D/ES/'.$id_juego.'.png'))
		{	
			$cover='images/wii/cover3D/ES/'.$id_juego.'.png';
			return $cover;
		}
		elseif(file_exists('images/wii/cover3D/FR/'.$id_juego.'.png'))
		{
			$cover='images/wii/cover3D/FR/'.$id_juego.'.png';
			return $cover;
		}
		elseif(file_exists('images/wii/cover3D/IT/'.$id_juego.'.png'))
		{
			$cover='images/wii/cover3D/IT/'.$id_juego.'.png';
			return $cover;
		}
		else
		{
			return $cover;
		}
	}
	
	public function infoJuego($id_juego)
	{
		$juego=null;
		$DateinameXML = Yii::app()->basePath . '/data/wiitdb.xml';
		$Tabellenname = "wiitdb";

		$wiitdbdata = simplexml_load_file($DateinameXML);
		$contador=0;
		
		foreach($wiitdbdata->game as $wiitdbentry)
		{
			$contador++;
			if($wiitdbentry->id==$id_juego)
			{
				$id=$wiitdbentry->id;
				$developer=$wiitdbdata->game->developer;
				$wifi=$wiitdbentry->{'wi-fi'}['players'];
				$input=$wiitdbentry->input['players'];
				
				foreach ($wiitdbentry->locale as $locale_ES)
				{
	    				switch((string) $locale_ES['lang'])
	    				{
	    					case 'ES':
	    						//<id>
	    						//$id=$wiitdbdata->game->id;
	    						//if($id_juego==$id)
	    						//{
	    							//throw new CHttpException(404,'Dentro');
	    							//<developer>
	    							//$developer=$wiitdbdata->game->developer;
	    							//<locale lang="ES"> <title>
								$title=$wiitdbentry->locale->title;
								//<locale lang="ES"> <synopsis>
								$synopsis=$locale_ES->synopsis;
								//<wi-fi players="0"/>
								//$wifi=$wiitdbentry->{'wi-fi'}['players'];
								//<input players="1">
								//$input=$wiitdbentry->input['players'];
								//$juego = array("id" => $id, "developer" => $developer,
								//	       "title" => $title, "synopsis"=>$synopsis,
								//	       "wifi"=>$wifi, "input"=>$input);
							break;
	    						//}
	    				}
				}
				$juego = array("id" => $id, "developer" => $developer,
						"title" => $title, "synopsis"=>$synopsis,
						 "wifi"=>$wifi, "input"=>$input);
			}
		}
	return $juego;
	}
	
	public function actionTest($id)
	{
		$juego=$this->infoJuego($id);
		throw new CHttpException(404,$juego["id"].' - '.$juego["developer"].' - '.$juego["title"].' - '.$juego["synopsis"].' - '.$juego["wifi"].' - '.$juego["input"]);
	}

}
