<?php
/*$this->breadcrumbs=array(
	'Usuarios'=>array('index'),
	$model->id,
);*/

$usuario=Usuario::model()->findByPk(Yii::app()->user->id);
$solicitudes_pedidas=Usuario::model()->solicitudesDeAmistad(Yii::app()->user->id);

$this->menu=array(
	array('template'=>'<img src="images/usuarios/'.Yii::app()->user->apodo.'/'.$usuario->avatar.'" alt="Avatar" id="img_centrada" class="img_centrada">', 'url'=>array('create')),
	array('label'=>'List Usuario', 'url'=>array('index'), 'visible'=>Yii::app()->user->name=='admin'),
	array('label'=>'Create Usuario', 'url'=>array('create'), 'visible'=>Yii::app()->user->name=='admin'),
	array('label'=>'Actualizar datos', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Usuario', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'), 'visible'=>Yii::app()->user->name=='admin'),
	array('label'=>'Manage Usuario', 'url'=>array('admin'), 'visible'=>Yii::app()->user->name=='admin'),
);
?>

<h1>Datos del usuario <?php echo $model->apodo; ?></h1>
<?php if(count($solicitudes_pedidas)>0): ?>
	<!-- <a style="color:red;" href="#" onclick='$("#nombre_div").toggle(200);'>(Tienes solicitudes de amistad por confirmar, en concreto <?php echo count($solicitudes_pedidas); ?>)</a> -->
	<!-- <div id="nombre_div" style="background-color:white;border:1px dotted; border-color: blue; padding:10px;"> -->
		<?php
			for($i=0; $i<count($solicitudes_pedidas); $i++)
			{ ?>
			<?php echo"<div id='req_res_".$i."' class=''>"; ?>
				<?php echo 'El usuario ' .Usuario::model()->findByPk($solicitudes_pedidas[$i]->id_jugador)->apodo.' quiere ser tu amig@...  ';?>
					<?php echo CHtml::ajaxLink(
					  'aceptar',
					  Yii::app()->createUrl( 'usuario/ajaxAceptarAmistad' ),
						  array( // ajaxOptions
						    'type' => 'POST',
						    'beforeSend' => "function( request )
								     {
								       // Set up any pre-sending stuff like initializing progress indicator
								       jQuery('#req_res_".$i."').html('espera');						   
								       $('#req_res_".$i."').addClass('loading');						  
								     }",
						    'complete' => "function()
						    		  	{
		      							$('#req_res_".$i."').removeClass('loading');
		      							$('#req_res_".$i."').addClass('ok');
		      							}",
						    'success' => "function( data )
								  {
								    // handle return data
								    //jQuery( '#req_res_".$i."' ).html( data );
								    $('#req_res_".$i."').slideUp(400);
								    //$('#req_res_".$i."').slideUp(300).delay(800).fadeIn(400);
								    //$('#req_res_".$i."').hide(200);
								    //location.reload();
								    //$('#nombre_div').show(200);
								  }",
						    'data' => array( 'id' => $solicitudes_pedidas[$i]->id )
						  ),
						  array( //htmlOptions
						    'href' => Yii::app()->createUrl( 'usuario/ajaxAceptarAmistad' ),
						    'class' => 'aceptar_amistad',
						    'id' => 'aceptar_amistad_'.$i
						  )
					); ?>
					<?php echo CHtml::ajaxLink(
					  'rechazar',
					  Yii::app()->createUrl( 'usuario/ajaxRechazarAmistad' ),
						  array( // ajaxOptions
						    'type' => 'POST',
						    'beforeSend' => "function( request )
								     {
								       // Set up any pre-sending stuff like initializing progress indicator
								       jQuery('#req_res_".$i."').html('espera');						   
								       $('#req_res_".$i."').addClass('loading');						  
								     }",
						    'complete' => "function()
						    		  	{
		      							$('#req_res_".$i."').removeClass('loading');
		      							$('#req_res_".$i."').addClass('ok');
		      							}",
						    'success' => "function( data )
								  {
								    // handle return data
								    jQuery('#req_res_".$i."').html( data );
								    $('#req_res_".$i."').slideUp(400);
								    //$('#req_res_".$i."').hide(200);
								  }",
						    'data' => array( 'id' => $solicitudes_pedidas[$i]->id )
						  ),
						  array( //htmlOptions
						    'href' => Yii::app()->createUrl( 'usuario/ajaxRechazarAmistad' ),
						    'class' => 'rechazar_amistad',
						    'id' => 'rechazar_amistad_'.$i
						  )
					); ?>
			</div>
			<?php } ?>
	<!-- </div> -->
<?php endif; ?>
<br />
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'nombre',
		'primer_apellido',
		'segundo_apellido',
		'apodo',
		'email',
		'fecha_nacimiento',
		'perfil_publico',
		'codigo_wii',
	),
)); ?>

<br />
<h1>Juegos jugados</h1>
<?php 
	$id_jugador=Yii::app()->user->id;
	$model->unsetAttributes();
	$model=new Juego;
	$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$model->searchByIdJugador($id_jugador),
	'columns'=>array(
		'titulo',
		'veces_jugado',
		'jugado_ultima_vez',
		array(
			'class'=>'CButtonColumn',
			'template'=>'{detalle}{muro}', // <-- TEMPLATE WITH THE TWO STATES
			'htmlOptions'=>array(
				'width'=>40,
			),
			'buttons' => array(
				'detalle'=>array(
				        'label'=>'Detalle',
				        'url'=>'Yii::app()->createUrl("juego/view", array("id"=>$data->id))',
				        'imageUrl'=>'images/icons/+info.png',
				),
				'muro'=>array(
				        'label'=>'Muro',
				        'url'=>'Yii::app()->createUrl("juego/view", array("id"=>$data->id))',
				        'imageUrl'=>'images/icons/muro.png',
				),
			),
		),
	),
)); ?>
