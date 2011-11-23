<?php $this->pageTitle=Yii::app()->name; ?>

<h1>Bienvenido, este es Top Five de <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<?php
    $sql = "SELECT DISTINCT titulo, id_juego, veces_jugado FROM juego WHERE veces_jugado=
        (SELECT MAX(veces_jugado) FROM juego) ORDER BY veces_jugado DESC LIMIT 0, 5";
    //$params=array(':accountId'=>$account->id,':paymentId'=>$payment->id);
    
    //$criteria = new CDbCriteria;
    //$criteria->select = "titulo, id_juego";
    //$criteria->distinct = true;
    //$criteria->order = 'foreign_table3.col5 DESC';
    //$model=Post::model()->findAllBySql($sql,$params);
    //$model=Juego::model()->findAll($criteria);
    $model=Juego::model()->findAllBySql($sql);
    $arr=array();
    for($i=0; $i<count($model); $i++) {
        $arr[$i] = array('title' => $model[$i]->titulo, 'image' => Yii::app()->baseUrl .
            '/images/wii/cover3D/EN/' . $model[$i]->id_juego . '.png' ,);
    };
    
    $this->widget('application.extensions.jCoverFlip.jCoverFlip', 
        array(
            'elements' => $arr
        )
    );?>