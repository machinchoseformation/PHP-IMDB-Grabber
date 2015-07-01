<?php
	
	require("imdb.class.php");

	$listGrabber = new IMDBListGrabber();
	$moviesList = $listGrabber->fetch(3);
	echo '<pre>';
	print_r($moviesList);
	echo '</pre>';

	/*
	$grabber = new IMDB('http://www.imdb.com/title/tt0118799/?ref_=nv_sr_1');
	if ($grabber->isReady) {
		echo '<pre>';
        print_r($grabber->getAll());
        echo '</pre>';
    }
    else {
        echo 'Movie was not found';
    }
	*/