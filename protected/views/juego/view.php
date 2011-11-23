<?php
/*$this->breadcrumbs=array(
	'Juegos'=>array('index'),
	$model->id,
);*/

$datosJuego=Juego::model()->infoJuego($model->id_juego);
$this->menu=array(
	array('template'=>'<img src="'.Juego::model()->coverByIdJuego($model->id_juego).'" WIDTH=150 HEIGHT=220 alt="Cover">'),
	array('label'=>'¿Quién ha jugado?', 'url'=>array('usuario/jugadoresByJuego', 'id_juego'=>$model->id_juego)),
	array('label'=>'Volver', 'url'=>array('usuario/view', 'id'=>Yii::app()->user->id)),
	);
?>

<h1>Detalle del juego <?php echo $datosJuego["title"]; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id_juego',
		'titulo',
		array(               
            	'label'=>'Desarrollador',
            	'type'=>'text',
            	'value'=>$datosJuego["developer"],
        	),
		array(               
            	'label'=>'Nº jugadores',
            	'type'=>'text',
            	'value'=>$datosJuego["input"],
        	),
        array(               
           		'label'=>'Jugadores on-line',
            	'type'=>'text',
            	'value'=>$datosJuego["wifi"],
        	),
        array(               
            	'label'=>'Descripción',
            	'type'=>'text',
            	'value'=>$datosJuego["synopsis"],
        	),
        array(               
            	'label'=>'Gameranking',
            	'type'=>'raw',
            	'value'=>'<a href="http://www.gamerankings.com/browse.html?search='.
            	    $datosJuego["title"].'&numrev=3&site=wii" target="_blank">'.$datosJuego["title"].'</a>',
        	),
	),
)); ?>
