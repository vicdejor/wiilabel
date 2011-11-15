<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				//$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				//mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				$this->enviarEmail($model->email,$model->subject,$model->body);
				Yii::app()->user->setFlash('contact','¡Gracias por contactar con nosotros!. En cuanto nos sea posible, te reponderemos.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect("index.php?r=usuario/view&id=".Yii::app()->user->id);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/*
	* Función para el envio de emails
	*/
	public function enviarEmail($emailDestinatario,$asunto,$mensaje)
	{    
		$message = new YiiMailMessage;
		$message->subject = $asunto;
		$message->setBody($mensaje,'text/html');//codificar el html de la vista
		$message->from =('wiilabelmail@gmail.com'); // alias del q envia
		$message->setTo($emailDestinatario); // a quien se le envia
		
		Yii::app()->mail->send($message);
	}
	
	public function actionActivacion()
	{
		// renders the view file 'protected/views/site/activacion.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('activacion');
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
