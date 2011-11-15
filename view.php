<?php
/*$this->breadcrumbs=array(
	'Juegos'=>array('index'),
	$model->id,
);*/

$this->menu=array(
	array('template'=>'<img src="'.Juego::model()->coverByIdJuego($model->id_juego).'" WIDTH=150 HEIGHT=220 alt="Cover">'),
	array('label'=>'List Juego', 'url'=>array('index')),
	array('label'=>'Create Juego', 'url'=>array('create')),
	array('label'=>'Update Juego', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Juego', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Juego', 'url'=>array('admin')),
);
?>

<h1>View Juego #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id_juego',
		'id_jugador',
		'titulo',
		'veces_jugado',
		'jugado_ultima_vez',
	),
)); ?>
