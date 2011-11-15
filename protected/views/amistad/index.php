<?php
$this->breadcrumbs=array(
	'Amistads',
);

$this->menu=array(
	array('label'=>'Create Amistad', 'url'=>array('create')),
	array('label'=>'Manage Amistad', 'url'=>array('admin')),
);
?>

<h1>Amistads</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
