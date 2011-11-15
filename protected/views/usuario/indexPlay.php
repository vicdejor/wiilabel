<?php
/*$this->breadcrumbs=array(
	'Usuarios',
);*/

$juego=Juego::model()->findByPk($model->id);
$datosJuego=Juego::model()->infoJuego($juego->id_juego);
$this->menu=array(
	array('label'=>'Create Usuario', 'url'=>array('create')),
	array('label'=>'Manage Usuario', 'url'=>array('admin')),
);
?>

<h1>Usuarios que han jugado al <?php echo $datosJuego["title"]; ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
