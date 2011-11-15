<?php
/*$this->breadcrumbs=array(
	'Usuarios'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);*/
$usuario=Usuario::model()->findByPk(Yii::app()->user->id);
$this->menu=array(
	array('template'=>'<img src="images/usuarios/'.Yii::app()->user->apodo.'/'.$usuario->avatar.'" alt="Avatar" id="img_centrada" class="img_centrada">', 'url'=>array('create')),
	array('label'=>'List Usuario', 'url'=>array('index'), 'visible'=>Yii::app()->user->name=='admin'),
	array('label'=>'Create Usuario', 'url'=>array('create'), 'visible'=>Yii::app()->user->name=='admin'),
	array('label'=>'Descartar cambios', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Delete Usuario', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'), 'visible'=>Yii::app()->user->name=='admin'),
	array('label'=>'Manage Usuario', 'url'=>array('admin'), 'visible'=>Yii::app()->user->name=='admin'),
);
?>

<h1>Actualizar el usuario <?php echo Yii::app()->user->apodo ?></h1>

<?php echo $this->renderPartial('_formup', array('model'=>$model)); ?>
