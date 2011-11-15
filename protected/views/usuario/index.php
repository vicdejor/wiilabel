<?php
/*$this->breadcrumbs=array(
	'Usuarios',
);*/

$datosJuego=Juego::model()->infoJuego($model->id_juego);
$this->menu=array(
	array('template'=>'<img src="'.Juego::model()->coverByIdJuego($model->id_juego).'" WIDTH=150 HEIGHT=220 alt="Cover">'),
	array('label'=>'Volver', 'url'=>array('juego/view', 'id'=>$model->id)),
	);
?>


<h1>Usuarios</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
