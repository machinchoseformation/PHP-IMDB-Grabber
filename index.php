<?php

	$numberOfBestOfPages = 1;
	
	require("imdb.class.php");

	$listGrabber = new IMDBListGrabber();
	$moviesList = $listGrabber->fetch($numberOfBestOfPages);
	/*echo '<pre>';
	print_r($moviesList);
	echo '</pre>';*/

	foreach($moviesList as $movieInfo){

		ini_set("max_execution_time", 30);

		$grabber = new IMDB('http://akas.imdb.com/title/'.$movieInfo['id'].'/');
		if ($grabber->isReady) {
			echo '<pre>';
	        print_r($grabber->getAll());
	        echo '</pre>';
	    }
	    else {
	        echo 'Movie was not found';
	    }
	}