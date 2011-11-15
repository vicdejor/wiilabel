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
		<div class="doscolumnas">
			<?php echo $form->labelEx($model,'nombre'); ?>
			<?php echo $form->error($model,'nombre'); ?>
			<?php echo $form->textField($model,'nombre',array('size'=>20,'maxlength'=>20)); ?>
		</div>
	
		<div class="doscolumnas">
			<?php echo $form->labelEx($model,'apodo'); ?>
			<?php echo $form->error($model,'apodo'); ?>
			<?php echo $form->textField($model,'apodo',array('size'=>20,'maxlength'=>20)); ?>
		</div>

	</div>
	
	<div class="row">
		<div class="doscolumnas">
			<?php echo $form->labelEx($model,'primer_apellido'); ?>
			<?php echo $form->error($model,'primer_apellido'); ?>
			<?php echo $form->textField($model,'primer_apellido',array('size'=>20,'maxlength'=>30)); ?>
		</div>

		<div class="doscolumnas">
			<?php echo $form->labelEx($model,'segundo_apellido'); ?>
			<?php echo $form->error($model,'segundo_apellido'); ?>
			<?php echo $form->textField($model,'segundo_apellido',array('size'=>20,'maxlength'=>30)); ?>
		</div>
	</div>
	
	<div class="row">
		<div class="doscolumnas">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->error($model,'email'); ?>
			<?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>30)); ?>
		</div>

		<div class="doscolumnas">
			<?php echo $form->labelEx($model,'fecha_nacimiento'); ?>
			<?php
				$this->widget('CMaskedTextField', array(
					'model' => $model,
					'attribute' => 'fecha_nacimiento',
					'mask' => '99/99/9999',
					'htmlOptions' => array('size' => 10)
				));
			?>
		</div>
	</div>
	
	<div class="row">
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
	</div>
	
	<div class="row">
		<div class="doscolumnas">
			<?php echo $form->labelEx($model,'codigo_wii'); ?>
			<?php
				$this->widget('CMaskedTextField', array(
					'model' => $model,
					'attribute' => 'codigo_wii',
					'mask' => '9999-9999-9999-9999',
					'htmlOptions' => array('size' => 19)
				));
			?>
		</div>
		<div class="doscolumnascheck">
			<?php echo $form->labelEx($model,'perfil_publico'); ?>
			<?php echo CHtml::activeCheckBox($model,'perfil_publico'); ?>
		</div>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'avatar'); ?>
		<?php echo CHtml::activeFileField($model, 'avatar'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Nuevo usuario' : 'Actualizar'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php endif; ?>
