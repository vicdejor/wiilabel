<?php
$this->breadcrumbs=array(
	'Amistads'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Amistad', 'url'=>array('index')),
	array('label'=>'Manage Amistad', 'url'=>array('admin')),
);
?>

<h1>Create Amistad</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>