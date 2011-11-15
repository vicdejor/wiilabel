<?php
$this->breadcrumbs=array(
	'Amistads'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Amistad', 'url'=>array('index')),
	array('label'=>'Create Amistad', 'url'=>array('create')),
	array('label'=>'Update Amistad', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Amistad', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Amistad', 'url'=>array('admin')),
);
?>

<h1>Solicitudes de amistad de  #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'id_jugador',
		'id_amigo',
		'estado',
		'fecha',
	),
)); ?>
