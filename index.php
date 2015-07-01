<?php

	$numberOfBestOfPagesByMoviemeter = 5;
	$numberOfBestOfPagesByNumVotes = 25;
	$minimumRating = 6.4;
	
	require("imdb.class.php");
	require("db.php");
	require("install_tables.php");

	installTables();

	$insertSql = "INSERT INTO movies 
				(id, imdb_id, title, year, cast, directors, writers, genres, plot, rating, votes, runtime, trailer_url, date_created, date_modified) 
				VALUES (NULL, :imdb_id, :title, :year, :cast, :directors, :writers, :genres, :plot, :rating, :votes, :runtime, :trailer_url, NOW(), NOW())";
	$insertSth = $dbh->prepare($insertSql);

	$existsSql = "SELECT id FROM movies 
					WHERE imdb_id = :imdb_id LIMIT 1";
	$existsSth = $dbh->prepare($existsSql);


	function addMovie(array $movieRawData)
	{
		global $insertSth, $existsSth;

		$existsSth->execute(array(":imdb_id" => $movieRawData['Id']));
		$exists = $existsSth->fetchColumn();

		if ($exists){
			echo "exists<br />";
			return false;
		}

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

		if ($insertSth->execute($params)){
			echo "inserted<br />";
			return true;
		}

		return false;
	}

	$listGrabber = new IMDBListGrabber();
	$moviesList1 = $listGrabber->fetch($numberOfBestOfPagesByNumVotes, "num_votes");
	$moviesList2 = $listGrabber->fetch($numberOfBestOfPagesByMoviemeter, "moviemeter");

	$moviesList = array_merge($moviesList2, $moviesList1);

	foreach($moviesList as $movieInfo){

		ini_set("max_execution_time", 30);

		$grabber = new IMDB('http://akas.imdb.com/title/'.$movieInfo['id'].'/');
		if ($grabber->isReady) {
			$movieRawData = $grabber->getAll();
			$movieRawData['Id'] = $movieInfo['id'];
			
			//nothing found ??
			if ($movieRawData['Title']['value'] == "n/A"){
				print_r($movieRawData);
				print_r($movieInfo);
				die("oops");
			}

			echo str_pad(' ', 2000) . '<br /><b>' . $movieRawData['Title']['value'] . "</b><br />";

	        if ($movieRawData['Rating']['value'] >= $minimumRating){
	        	addMovie($movieRawData);
	        }
	        else {
	        	echo "rating too low (".$movieRawData['Rating']['value'].")<br />";
	        }
	    }
	    else {
	        echo 'Movie was not found';
	    }
	}

	