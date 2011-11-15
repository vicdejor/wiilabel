<?php

class AjaxController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('activarCuenta','create', 'recuperarPassword', 'cambiarClave'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('update','jugadoresByJuego','index','view', 'actionReqTest03'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model=$this->loadModel($id);
		$fecha_formateda=$this->fechaBbddAcampo($model->fecha_nacimiento);
		$model->fecha_nacimiento=$fecha_formateda;
		if($model->perfil_publico==1)
		{
			$model->perfil_publico='Sí';
		}
		else
		{
			$model->perfil_publico='No';
		}
		$this->layout='//layouts/column2';
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Usuario;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Usuario']))
		{
			$model->attributes=$_POST['Usuario'];
			//Formateamos la fecha de nacimiento para MySQL
			//throw new CHttpException(400,'1');
			$fecha_valida=$this->fechaCampoAbbdd($model->fecha_nacimiento);
			$model->fecha_nacimiento=$fecha_valida;
			$model->codigo_usuario='false';
			//Ciframos las contraseñas.
			$password_plano=$model->password;
			$model->password=md5($password_plano);
			$model->re_password=md5($password_plano);
			//Tenemos que crear un directorio para almacenar el avatar del usuario.
			//Si el directorio existe lo reiniciamos.
			//Ojo!, antes de crear o inicializar el directorio, tenemos que comprobar
			//que el usuario ha ingresado un apodo.
			if($model->apodo!='')
			{
				if (!is_dir('images/usuarios/'.$model->apodo))
				{	
					mkdir('images/usuarios/'.$model->apodo);
				}
				else
				{
					$this->unlinkRecursive('images/usuarios/'.$model->apodo, false);
				}
			}
			$model->avatar=CUploadedFile::getInstance($model,'avatar');
			if($model->save())
			{
				if(!is_null($model->avatar))
				{
					$model->avatar->saveAs('images/usuarios/'.$model->apodo.'/'.$model->avatar);
				}
				$clave=$this->crearActivacion($model->id);
				$this->enviarEmail($model->email,'Activación de cuenta.','Hola, '.$model->apodo.'. Estás ha un paso de tener cuenta en '.Yii::app()->name.'. Para esto solo tienes que pinchar el siguiente enlace: <a href=\'http://localhost/wiilabel/index.php?r=usuario/activarCuenta&id_usuario='.$model->id.'&clave='.$clave.'\'>http://localhost/wiilabel/index.php?r=usuario/activarCuenta&id_usuario='.$model->id.'&clave='.$clave.'</a>');
				Yii::app()->user->setFlash('crear_usuario','La cuenta se ha creado correctamente. Recibirá un email con un enlace para activar la cuenta.');
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$this->layout='//layouts/column2';
		$model=$this->loadModel($id);
		$fecha_formateda=$this->fechaBbddAcampo($model->fecha_nacimiento);
		$model->fecha_nacimiento=$fecha_formateda;
		//Guardamos el avatar antiguo.
		$avatar_antiguo=$model->avatar;
		//Guardamos la contraseña
		$password=$model->password;

		// Uncomment the following line if AJAX validation is needed
		//$this->performAjaxValidation($model);

		if(isset($_POST['Usuario']))
		{
			$model->attributes=$_POST['Usuario'];
			//Si estamos aquí, por que algún campo no era correcto, no tocamos la fecha.
			$model->password=$password;
			$model->re_password=$password;
			if($model->validate())
			{
				$fecha_valida=$this->fechaCampoAbbdd($model->fecha_nacimiento);
				$model->fecha_nacimiento=$fecha_valida;
			}
			//El directorio debería de existir, aun así, si no existe lo creamos.
			if (!is_dir('images/usuarios/'.$model->apodo))
			{
				mkdir('images/usuarios/'.$model->apodo);
			}
			$model->avatar=CUploadedFile::getInstance($model,'avatar');
			//Si el usuario ha seleccionado un avatar borramos el antiguo (si existe).
			if(is_null($model->avatar))
			{
				$model->avatar=$avatar_antiguo;
			}
			else if($avatar_antiguo!='')
			{	
				//Comprobamos que el antiguo avatar es accesible.
				if(file_exists('images/usuarios/'.$model->apodo.'/'.$avatar_antiguo))
				{ 
					unlink('images/usuarios/'.$model->apodo.'/'.$avatar_antiguo);
				}
				else
				{
					throw new CHttpException(400,'¡Ups!, esto es embarazoso, no he podido actualizar tu avatar. Prueba de nuevo, si vuelvo ha fallar, ponte en contacto con el desarrollador.');
				}
			}
			if($model->save())
			{
				//Solo guardamos el avatar si el usuario a selecionado uno nuevo.
				if($model->avatar!=$avatar_antiguo)
				{	
					$model->avatar->saveAs('images/usuarios/'.$model->apodo.'/'.$model->avatar);
					//Guardamos la extensión del avatar.
					$ext = substr($model->avatar, strrpos($model->avatar, '.') + 1);
					//Creamos una nueva imagen.
					$newfilename = 'images/usuarios/'.$model->apodo.'/avatar.'.$ext;
					//Reescalamos el avatar seleccionado por el usuario, en la imagen creada.
					$avatar=$this->resize('images/usuarios/'.$model->apodo.'/'.$model->avatar, 80, 80, $newfilename);
					//Eliminamos el avatar sin reescalar.
					unlink('images/usuarios/'.$model->apodo.'/'.$model->avatar);
					//Actualizaos el avatar del usuario con la nueva imagen reescalada.
					$model->avatar='avatar.'.$ext;
					$model->save();
				}
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Usuario');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}
	
	public function actionJugadoresByJuego($id_juego)
	{
		$model=Juego::model()->findByAttributes(array('id_juego'=>$id_juego));
		$sql="SELECT * FROM usuario u WHERE EXISTS (SELECT * FROM juego j WHERE j.id_juego='".$id_juego."' AND u.id=j.id_jugador AND u.perfil_publico=1)";
		$usuarios=Usuario::model()->findAllBySql($sql);
		//$dataProvider = new SDataProvider("grid",$usuarios);
		$dataProvider=new CArrayDataProvider($usuarios, array(
   						 	'id'=>'usuario',
    							'sort'=>array(
        							'attributes'=>array(
             								'nombre', 'primer_apellido', 'segundo_apellido' ,'apodo', 'email',
        							),
    							),
    							'pagination'=>array(
        							'pageSize'=>10,
    							),
		));
		$this->layout='//layouts/column2';
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'model'=>$model,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Usuario('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Usuario']))
			$model->attributes=$_GET['Usuario'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Usuario::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='usuario-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	protected function fechaCampoAbbdd($fecha)
	{
		$fecha_formateada=substr($fecha,-4).'-'.substr($fecha,3,-5).'-'.substr($fecha,0,-8);
		return $fecha_formateada;
	}
	
	protected function fechaBbddAcampo($fecha)
	{
		$fecha_formateada=substr($fecha,-2).'/'.substr($fecha,5,-3).'/'.substr($fecha,0,-6);
		return $fecha_formateada;
	}
	
	/**
	 * Recursively delete a directory
	 *
	 * @param string $dir Directory name
	 * @param boolean $deleteRootToo Delete specified top-level directory as well
	 */
	protected function unlinkRecursive($dir, $deleteRootToo)
	{
	    if(!$dh = @opendir($dir))
	    {
		return;
	    }
	    while (false !== ($obj = readdir($dh)))
	    {
		if($obj == '.' || $obj == '..')
		{
		    continue;
		}

		if (!@unlink($dir . '/' . $obj))
		{
		    unlinkRecursive($dir.'/'.$obj, true);
		}
	    }

	    closedir($dh);
	   
	    if ($deleteRootToo)
	    {
		@rmdir($dir);
	    }
	   
	    return;
	}
	
	protected function crearActivacion($id)
	{
		$clave=md5(date("H:i:s"));
		
		$connection=Yii::app()->db;
		$sql="INSERT INTO activacion (id, id_usuario, fecha, clave) VALUES(NULL, :id, CURRENT_TIMESTAMP, :clave)";
		$command=$connection->createCommand($sql);
		$command->bindParam(":id",$id,PDO::PARAM_STR);
		$command->bindParam(":clave",$clave,PDO::PARAM_STR);
		$command->execute();
		
		return $clave;
	}
	
	public function actionActivarCuenta($id_usuario, $clave)
	{
		$activacion_ok=false;
		$id_usuario_row='';
		$clave_row='';
		$id_row='';
		
		$connection=Yii::app()->db;
		$sql="SELECT id, id_usuario, clave FROM activacion";
		$dataReader=$connection->createCommand($sql)->query();
		// bind the 2nd column (id_usuario) with the $id_usuario_row variable
		$dataReader->bindColumn(2,$id_usuario_row);
		// bind the 4nd column (clave) with the $clave_row variable
		$dataReader->bindColumn(3,$clave_row);
		$dataReader->bindColumn(1,$id_row);
		while($dataReader->read()!==false)
		{	
    			if($id_usuario==$id_usuario_row && $clave==$clave_row)
    			{
    				$command = Yii::app()->db->createCommand();
    				// UPDATE `usuario` SET `activo`=:activo WHERE id=:id
    				$command->update('usuario', array('activo'=>'1',), 'id=:id', array(':id'=>$id_usuario));
    				$activacion_ok=true;
    				//TODO tengo que eliminar el registro una vez la activacion se realiza.
    				$sql="DELETE FROM activacion WHERE id=".$id_row;
				$dataReader=$connection->createCommand($sql)->query();
				break;
    			}
		}
		if($activacion_ok)
		{
			$this->redirect(array('site/activacion'));
		}
		else
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}
	}
	
	/*
	* Función para el envio de emails
	*/
	protected function enviarEmail($emailDestinatario,$asunto,$mensaje)
	{    
		$message = new YiiMailMessage;
		$message->subject = $asunto;
		$message->setBody($mensaje,'text/html');//codificar el html de la vista
		$message->from =('wiilabelmail@gmail.com'); // alias del q envia
		$message->setTo($emailDestinatario); // a quien se le envia
		
		Yii::app()->mail->send($message);
	}
	
	/*
	*Función para reescalar el avatar.
	*Sacado de http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
	*/
	protected function resize($img, $w, $h, $newfilename)
	{
		//Check if GD extension is loaded
		if (!extension_loaded('gd') && !extension_loaded('gd2'))
		{
			trigger_error("GD is not loaded", E_USER_WARNING);
		  	return false;
		}
		 
		//Get Image size info
		$imgInfo = getimagesize($img);
		switch ($imgInfo[2])
		{
			case 1: $im = imagecreatefromgif($img); break;
		  	case 2: $im = imagecreatefromjpeg($img);  break;
		  	case 3: $im = imagecreatefrompng($img); break;
		  	default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
		}
		 
		//If image dimension is smaller, do not resize
		if ($imgInfo[0] <= $w && $imgInfo[1] <= $h)
		{
			$nHeight = $imgInfo[1];
		  	$nWidth = $imgInfo[0];
		}
		else
		{
			//yeah, resize it, but keep it proportional
		  	if ($w/$imgInfo[0] > $h/$imgInfo[1])
		  	{
		   		$nWidth = $w;
		   		$nHeight = $imgInfo[1]*($w/$imgInfo[0]);
		  	}
		  	else
		  	{
		   		$nWidth = $imgInfo[0]*($h/$imgInfo[1]);
		   		$nHeight = $h;
		  	}
		}
		$nWidth = round($nWidth);
		$nHeight = round($nHeight);
		 
		$newImg = imagecreatetruecolor($nWidth, $nHeight);
		 
		/* Check if this image is PNG or GIF, then set if Transparent*/  
		if(($imgInfo[2] == 1) OR ($imgInfo[2] == 3))
		{
			imagealphablending($newImg, false);
		  	imagesavealpha($newImg,true);
		  	$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
		  	imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
		}
		imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);
		 
		//Generate the file, and rename it to $newfilename
		switch ($imgInfo[2])
		{
			case 1: imagegif($newImg,$newfilename); break;
		  	case 2: imagejpeg($newImg,$newfilename);  break;
		  	case 3: imagepng($newImg,$newfilename); break;
		  	default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
		}
		return $newfilename;
	}
	
	public function actionRecuperarPassword()
	{
		$model=new Usuario;
		if(isset($_POST['Usuario']))
		{
			$model->attributes=$_POST['Usuario'];
			if($model->email=='')
			{
				Yii::app()->user->setFlash('crear_usuario','Tienes que introducir un email. <a href=""> Volver</a>');
			}
			else
			{
				$usuario=Usuario::model()->findByAttributes(array('email'=>$model->email));
				if(is_null($usuario))
				{
					Yii::app()->user->setFlash('crear_usuario','¡Ups!, no existe ningún usuario con el email "'.$model->email.'". <a href=""> Volver</a>');
				}
				else
				{
					$clave=$this->crearCambioClave($usuario->id);
					$this->enviarEmail($usuario->email,'Cambio de contraseña.','Hola, '.$usuario->apodo.' estás ha un paso de cambiar tu contraseña de '.Yii::app()->name.'. Para esto solo tienes que pinchar el siguiente enlace: <a href=\'http://localhost/wiilabel/index.php?r=usuario/cambiarClave&id_usuario='.$usuario->id.'&clave='.$clave.'\'>http://localhost/wiilabel/index.php?r=usuario/cambiarClave&id_usuario='.$usuario->id.'&clave='.$clave.'</a>');
					Yii::app()->user->setFlash('crear_usuario','¡Perfecto!. Comprueba tu correo, te habrá llegado un email con instrucciones para cambiar tu contraseña.');
				}
			}
		}
		//Borramos las contraseñas añadidas en caso de que algo no fuera bien.
		$model->password='';
		$model->re_password='';
		$this->render('pedirEmail',array(
			'model'=>$model,
		));
	}
	
	protected function crearCambioClave($id)
	{
		$clave=md5(date("H:i:s"));
		
		$connection=Yii::app()->db;
		$sql="INSERT INTO cambio_clave (id, id_usuario, fecha, clave) VALUES(NULL, :id, CURRENT_TIMESTAMP, :clave)";
		$command=$connection->createCommand($sql);
		$command->bindParam(":id",$id,PDO::PARAM_STR);
		$command->bindParam(":clave",$clave,PDO::PARAM_STR);
		$command->execute();
		
		return $clave;
	}
	
	public function actionCambiarClave($id_usuario, $clave)
	{
		//Comprobamos posibles hacks
		if($this->isHackCambioClave($id_usuario, $clave))
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}
		$id_usuario_row='';
		$clave_row='';
		$id_row='';
		$connection=Yii::app()->db;
		$sql='SELECT id, id_usuario, clave FROM cambio_clave WHERE id_usuario='.$id_usuario.' AND clave="'.$clave.'"';
		$dataReader=$connection->createCommand($sql)->query();
		//El valor de la primera columna lo pondremos en la variable $id_row.
		$dataReader->bindColumn(1,$id_row);
		//El valor de la segunda columna lo pondremos en la variable $id_usuario_row.
		$dataReader->bindColumn(2,$id_usuario_row);
		//El valor de la segunda columna lo pondremos en la variable $clave_row.
		$dataReader->bindColumn(3,$clave_row);
		//Lo primero es comprobar si ya hemos entrado antes
		if($dataReader->read()==false)
		{
			throw new CHttpException(404,'The requested page does not exist.');
		}
		else
		{
			//Si estamos aqui, es que es la primera vez que entramos y los datos son correctos (id y clave).
			$model=new Usuario;
			$model=$this->loadModel($id_usuario);
			//Reseteamos las contraseñas.
			$model->password='';
			$model->re_password='';
			if(isset($_POST['Usuario']))
			{
				$model->attributes=$_POST['Usuario'];
				if($model->password!=$model->re_password)
				{
					Yii::app()->user->setFlash('crear_usuario','¡Ups!. No Has introducido bien la contraseña. <a href=""> Volver</a>');
				}
				else if($model->password=='' OR $model->re_password=='')
				{
					Yii::app()->user->setFlash('crear_usuario','¡Ups!. La contraseña no puede ser nula. <a href=""> Volver</a>');
				}
				else if($id_usuario==$id_usuario_row && $clave==$clave_row)
	    			{
	    				$command = Yii::app()->db->createCommand();
	    				$nuevoPassword=md5($model->password);
	    				// UPDATE `usuario` SET `password`=:password WHERE id=:id
	    				$command->update('usuario', array('password'=>$nuevoPassword,), 'id=:id', array(':id'=>$id_usuario));
	    				//TODO tengo que eliminar el registro una vez se ha hecho el cambio de password.
	    				$sql="DELETE FROM cambio_clave WHERE id=".$id_row;
					$dataReader=$connection->createCommand($sql)->query();
					Yii::app()->user->setFlash('crear_usuario','¡Perfecto!. Tu clave se ha combiado correctamente. <a href="index.php?r=site/login"> Acceder</a>');
	    			}
				else
				{
					throw new CHttpException(404,'The requested page does not exist.');
				}
			}
		}
		$this->render('cambioPass',array(
			'model'=>$model,
		));
	}
	
	public function isHackCambioClave($id_usuario, $clave)
	{
		if(is_null($id_usuario) || is_null($clave) || $clave=='' || $id_usuario=='' || !is_numeric($id_usuario) || is_numeric($clave))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function actionReqTest03()
	{
		echo CHtml::encode(print_r($_POST, true));
	}
}
