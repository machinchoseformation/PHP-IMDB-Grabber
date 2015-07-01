<?php

	$numberOfBestOfPages = 1;
	$minimumRating = 7;
	
	require("imdb.class.php");
	require("db.php");

	function installTables()
	{

		global $dbh;

		$sql = "
			CREATE TABLE IF NOT EXISTS movies (
			  id int(11) NOT NULL,
			  imdb_id varchar(20) NOT NULL,
			  title varchar(255) NOT NULL,
			  year int(4) NOT NULL,
			  cast text NOT NULL,
			  directors text NOT NULL,
			  writers text NOT NULL,
			  genres varchar(255) NOT NULL,
			  plot text NOT NULL,
			  rating float(2,1) NOT NULL,
			  votes int(11) NOT NULL,
			  runtime varchar(25) NOT NULL,
			  trailer_url text NOT NULL,
			  date_created datetime NOT NULL,
			  date_modified datetime NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			ALTER TABLE movies
		  		ADD PRIMARY KEY (id);
			ALTER TABLE movies
			  	MODIFY id int(11) NOT NULL AUTO_INCREMENT;
		";

		$sth = $dbh->prepare($sql);
		$sth->execute();	

	}


	$insertSql = "INSERT INTO movies 
				(id, imdb_id, title, year, cast, directors, writers, genres, plot, rating, votes, runtime, trailer_url, date_created, date_modified) 
				VALUES (NULL, :imdb_id, :title, :year, :cast, :directors, :writers, :genres, :plot, :rating, :votes, :runtime, :trailer_url, NOW(), NOW())";
	$insertSth = $dbh->prepare($insertSql);


	function addMovie(array $movieRawData)
	{
		global $insertSth;

		$params = array(
			':imdb_id' 		=> $movieRawData['Id'], 
			':title' 		=> $movieRawData['Title']['value'], 
			':year' 		=> $movieRawData['Year']['value'], 
			':cast' 		=> $movieRawData['Cast']['value'], 
			':directors' 	=> $movieRawData['Director']['value'], 
			':writers' 		=> $movieRawData['Writer']['value'], 
			':genres' 		=> $movieRawData['Genre']['value'], 
			':plot' 		=> $movieRawData['Plot']['value'], 
			':rating' 		=> $movieRawData['Rating']['value'], 
			':votes' 		=> $movieRawData['Votes']['value'], 
			':runtime' 		=> $movieRawData['Runtime']['value'], 
			':trailer_url' 	=> $movieRawData['TrailerLinked']['value']
		);

		$insertSth->execute($params);

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
			$movieRawData = $grabber->getAll();
			$movieRawData['Id'] = $movieInfo['id'];
			echo '<pre>';
	        print_r($movieRawData);
	        echo '</pre>';
	        if ($movieRawData['Rating']['value'] > $minimumRating){
	        	addMovie($movieRawData);
	        }
	    }
	    else {
	        echo 'Movie was not found';
	    }
	}

	