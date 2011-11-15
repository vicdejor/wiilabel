<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'juego-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'id_juego'); ?>
		<?php echo $form->textField($model,'id_juego',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'id_juego'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'id_jugador'); ?>
		<?php echo $form->textField($model,'id_jugador',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'id_jugador'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'titulo'); ?>
		<?php echo $form->textField($model,'titulo',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'titulo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'veces_jugado'); ?>
		<?php echo $form->textField($model,'veces_jugado'); ?>
		<?php echo $form->error($model,'veces_jugado'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'jugado_ultima_vez'); ?>
		<?php echo $form->textField($model,'jugado_ultima_vez'); ?>
		<?php echo $form->error($model,'jugado_ultima_vez'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->