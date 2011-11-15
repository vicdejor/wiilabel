<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_juego')); ?>:</b>
	<?php echo CHtml::encode($data->id_juego); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('id_jugador')); ?>:</b>
	<?php echo CHtml::encode($data->id_jugador); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('titulo')); ?>:</b>
	<?php echo CHtml::encode($data->titulo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('veces_jugado')); ?>:</b>
	<?php echo CHtml::encode($data->veces_jugado); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('jugado_ultima_vez')); ?>:</b>
	<?php echo CHtml::encode($data->jugado_ultima_vez); ?>
	<br />


</div>