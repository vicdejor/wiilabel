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
	
	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>30)); ?>
		<p class="hint">
			Introduce el email con el que te registraste en WiiLabel
		</p>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Pedir cambio de contraseña' : 'Actualizar'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php endif; ?>
