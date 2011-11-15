<?php if(Yii::app()->user->hasFlash('crear_usuario')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('crear_usuario'); ?>
</div>

<?php else: ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'usuario-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),

)); ?>
	<p class="note">Los campos con <span class="required">*</span> son necesarios.</p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->hiddenField($model,'activo'); ?>
	</div>
	
	<div class="doscolumnas">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>20,'maxlength'=>30)); ?>
	</div>

	<div class="doscolumnas">
		<?php echo $form->labelEx($model,'re_password'); ?>
		<?php echo $form->error($model,'re_password'); ?>
		<?php echo $form->passwordField($model,'re_password',array('size'=>20,'maxlength'=>30)); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Cambiar contraseña' : 'Cambiar contraseña'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php endif; ?>
