<?php

	$numberOfBestOfPages = 1;
	
	require("imdb.class.php");
	require("db.php");

	function installTables()
	{

		global $dbh;

		$sql = "
			CREATE TABLE IF NOT EXISTS `movies` (
			  `id` int(11) NOT NULL,
			  `imdb_id` varchar(20) NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `year` int(4) NOT NULL,
			  `cast` text NOT NULL,
			  `directors` text NOT NULL,
			  `writers` text NOT NULL,
			  `genres` varchar(255) NOT NULL,
			  `plot` text NOT NULL,
			  `rating` float(2,1) NOT NULL,
			  `votes` int(11) NOT NULL,
			  `runtime` varchar(25) NOT NULL,
			  `trailer_url` text NOT NULL,
			  `date_created` datetime NOT NULL,
			  `date_modified` datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			ALTER TABLE `movies`
		  		ADD PRIMARY KEY (`id`);
			ALTER TABLE `movies`
			  	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";

		$sth = $dbh->prepare($sql);
		$sth->execute();	

	}


	installTables();

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

