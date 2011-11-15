<?php
$this->breadcrumbs=array(
	'Juegos'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Juego', 'url'=>array('index')),
	array('label'=>'Manage Juego', 'url'=>array('admin')),
);
?>

<h1>Create Juego</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>