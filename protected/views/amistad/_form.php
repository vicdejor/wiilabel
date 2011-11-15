<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'amistad-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'id_jugador'); ?>
		<?php echo $form->textField($model,'id_jugador',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'id_jugador'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'id_amigo'); ?>
		<?php echo $form->textField($model,'id_amigo',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'id_amigo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'estado'); ?>
		<?php echo $form->textField($model,'estado',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'estado'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'fecha'); ?>
		<?php echo $form->textField($model,'fecha'); ?>
		<?php echo $form->error($model,'fecha'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->