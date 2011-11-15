<?php
$this->breadcrumbs=array(
	'Juegos'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Juego', 'url'=>array('index')),
	array('label'=>'Create Juego', 'url'=>array('create')),
	array('label'=>'View Juego', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Juego', 'url'=>array('admin')),
);
?>

<h1>Update Juego <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>