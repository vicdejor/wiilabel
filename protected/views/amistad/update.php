<?php
$this->breadcrumbs=array(
	'Amistads'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Amistad', 'url'=>array('index')),
	array('label'=>'Create Amistad', 'url'=>array('create')),
	array('label'=>'View Amistad', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Amistad', 'url'=>array('admin')),
);
?>

<h1>Update Amistad <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>