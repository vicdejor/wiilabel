<div class="view">

	<!-- <b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('activo')); ?>:</b>
	<?php echo CHtml::encode($data->activo); ?>
	<br /> -->

    <div id=centrado>
	<div id="contenido_en_linea">
		<b><?php echo CHtml::encode($data->getAttributeLabel('nombre')); ?>:</b>
		<?php echo CHtml::encode($data->nombre); ?>
		<br />

		<!-- <b><?php echo CHtml::encode($data->getAttributeLabel('primer_apellido')); ?>:</b>
		<?php echo CHtml::encode($data->primer_apellido); ?>
		<br />

		<b><?php echo CHtml::encode($data->getAttributeLabel('segundo_apellido')); ?>:</b>
		<?php echo CHtml::encode($data->segundo_apellido); ?>
		<br /> -->

		<b><?php echo CHtml::encode($data->getAttributeLabel('apodo')); ?>:</b>
		<?php echo CHtml::encode($data->apodo); ?>
		<!--<br />

		<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
		<?php echo CHtml::encode($data->email); ?>-->
		<br />
		<br />
		
		<?php if(Usuario::model()->solicitudAmistadEnTramite(Yii::app()->user->id,$data->id)): ?>
			<div>Solicitud de amistad en tramite</div>
		<?php endif; ?>
		
		<?php if(Usuario::model()->sonAmigos(Yii::app()->user->id,$data->id)): ?>
		    <div id="mensaje" class="">
		        <textarea id="texto_mensaje" rows="10" cols="60"></textarea>
		    <br />
		    <?php echo CHtml::ajaxButton(
				  "enviar",
				  Yii::app()->createUrl( 'usuario/ajaxEnviarMensaje' ),
					  array( // ajaxOptions
					    'type' => 'POST',
					    'beforeSend' => "function( request )
							     {
							       jQuery('#mensaje').html('espera');						   
							       $('#mensaje').addClass('loading');
							       // Set up any pre-sending stuff like initializing progress indicator					  
							     }",
					    'complete' => 'function()
					    		  	{
					    		    $("#mensaje").removeClass("loading");
	      							$("#mensaje").addClass("ok");
	      							}',
					    'success' => "function( data )
							  {
							    // handle return data
							    jQuery('#mensaje').html(data);
							  }",
					    'data' => array( 'id_amigo' => $data->id )
					  ),
					  array( //htmlOptions
					    'href' => Yii::app()->createUrl( 'usuario/ajaxEnviarMensaje' ),
					    'class' => 'enviar_mensaje',
					    'id' => '_mensaje'
					  )
				); ?>
		    </div>
		<?php endif; ?>

		<?php if(Usuario::model()->puedeSolicitarAmistad(Yii::app()->user->id,$data->id)): ?>
			<div id="req_res" class=""><?php echo CHtml::ajaxButton(
				  "Solicitar amistad",
				  Yii::app()->createUrl( 'usuario/ajaxRequest' ),
					  array( // ajaxOptions
					    'type' => 'POST',
					    'beforeSend' => "function( request )
							     {
							       // Set up any pre-sending stuff like initializing progress indicator
							       jQuery('#req_res').html('espera');						   
							       $('#req_res').addClass('loading');						  
							       //jQuery('#boton_amistad').html('Procesando');
							     }",
					    'complete' => 'function()
					    		  	{
	      							$("#req_res").removeClass("loading");
	      							$("#req_res").addClass("ok");
	      							}',
					    'success' => "function( data )
							  {
							    // handle return data
							    //alert( data );
							    jQuery('#req_res').html(data);
							  }",
					    'data' => array( 'id_amigo' => $data->id )
					  ),
					  array( //htmlOptions
					    'href' => Yii::app()->createUrl( 'usuario/ajaxRequest' ),
					    'class' => 'aceptar_amistad',
					    'id' => 'amistad'
					  )
				); ?>
			</div>
		<?php endif; ?>
		
	</div>
	<div id="contenido_en_linea">
		<b><img src="images/usuarios/<?php echo $data->apodo; ?>/<?php echo $data->avatar; ?>" alt="Avatar">
	</div>
	</div>
	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('fecha_nacimiento')); ?>:</b>
	<?php echo CHtml::encode($data->fecha_nacimiento); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('perfil_publico')); ?>:</b>
	<?php echo CHtml::encode($data->perfil_publico); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('codigo_wii')); ?>:</b>
	<?php echo CHtml::encode($data->codigo_wii); ?>
	<br />

	*/ ?>

</div>
