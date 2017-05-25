<?php
//Author: James Alford-Golojuch

//The main static json file which program depends on to search other files
$mainJSON = "UKgames.json";

if (isset($_GET['fileToSearch']) && isset($_GET['statToSearch']) && isset($_GET['funcWanted'])) {
	processform();
} else {
	displayform();
}

function processform() {
	//Global value for main json file
	global $mainJSON;
	$json = readJSONfile($mainJSON);
	//readJSONfile returns 'Error' if error converting to json object
        if ($json == -1) {
                return;
        }
	//Check if form fileToSearch value exists
	if ($json['files'][$_GET['fileToSearch']] == NULL) {
		echo "Error invalid file<br>";
	} else {
		//Reads from json file
		$path = $json['files'][$_GET['fileToSearch']];
		$jsonSearch = readJSONfile($path);
		//Error reading from file and converting to json object
		if ($jsonSearch == -1) {
			return;
		}
		$game = doSearch($jsonSearch, $_GET['statToSearch'], $_GET['funcWanted']);
		//If game is -1 then there was a printed error from search
		if ($game == -1) {
			return;
		}
		//Prints the game array stats
		echo "<h3><strong>Game stats for the game with the ", $_GET['funcWanted'], "est ", $_GET['statToSearch'], ": </strong></h3>";
		foreach ($game as $stat=>$value) {
			if ($_GET['statToSearch'] == $stat) {
				echo "<strong>", $stat, ": ", $value, "</strong><br>";
			} else {
				echo $stat, ": ", $value, "<br>";
			}
		}
	}
}

function doSearch($json, $statToSearch, $funcWanted) {
	//Searches for highest value for stat
	if ($funcWanted == 'high') {
		//Holds value for highest stat value
		$high = NULL;
		//Holds the game array for highest stat game
		$highJSON = NULL;
		//Look at each game array of stats
		foreach ($json["games"] as $num=>$value) {
			//Holds value for games array statToSearch
			$stat = $value[$statToSearch];
			//If high value hasnt been set yet
			if ($high == NULL) {
				$high = $stat;
				$highJSON = $value;
			} else {
				//If the stat value is higher than the highest value make it the new highest
				if ($stat > $high) {
					$high = $stat;
					$highJSON = $value;
				}
			}
		}
		//If high is still null then statToSearch doesnt exist in the games array
		if ($high == NULL) {
			echo "Error selected stat doesn't exist<br>";
			return -1;
		} else {
			return $highJSON;
		}
	}
	//Searches for lowest value for stat
	else if ($funcWanted == 'low') {
		//Holds value for lowest stat value
                $low = NULL;
		//Holds the game array for lowest stat game
		$lowJSON = NULL;
                //Look at each game array of stats
                foreach ($json["games"] as $num=>$value) {
                        //Holds value for games array stat
                        $stat = $value[$statToSearch];
			//If value hasnt been set yet
                        if ($low == NULL) {
				$low = $stat;
				$lowJSON = $value;
			} else {
	                        //If the stat value is lower than the lowest value make it the new lowest
				if ($stat < $low) {
                                	$low = $stat;
					$lowJSON = $value;
                 	       }
			}
                }
                //If low is still null then statToSearch doesnt exist in the games array
                if ($low == NULL) {
                        echo "Error selected stat doesn't exist<br>";
			return -1;
                } else {
                        return $lowJSON;
                }
	}
	//Error invalid funcWanted
	else {
		echo "Error function wanted is invalid<br>";
		return -1;
	}
}

function readJSONfile($file) {
	//Makes sure filename is valid
	if (strlen($file) <= 0) {
		echo "Error invalid filename<br>";
		return -1;
	}
	//Reads file contents
	$fileString = file_get_contents($file);
	$json = json_decode($fileString, true);
	//checks if there was an error converting to json object
	if (json_last_error() != 0) {
                echo "Error converting to JSON object<br>";
                return -1;
        }
	return $json;
}

function displayForm() {
	//Global value for main json file
        global $mainJSON;

	//Beginning html
	startHTML();
	//Get json file data from UKgames.json to populate select bars
	$json = readJSONfile($mainJSON);
	//readJSONfile returns 'Error' if error converting to json object
	if ($json == -1) {
		return;
	}

	echo "<form action='project3.php' method='get'>
        Select parameters to search:<br>";
	//Prints file descriptors for each file in json
        echo "<p>Select File: <select name='fileToSearch'> ";
        foreach ($json['files'] as $desc => $value) {
                echo "<option value=$desc>$desc</option>";
        }
        echo "</select>";
	//Prints stat name for each stat in json
        echo "<p>Select Stat: <select name='statToSearch'> ";
        foreach ($json['stats'] as $desc) {
                echo "<option value=$desc>$desc</option>";
        }
        echo "</select>";

        echo"<p>Select High or Low: <select name='funcWanted'> ";
                echo "<option value='high'>High</option>";
                echo "<option value='low'>Low</option>";
        echo "</select>";

        echo "<p><input type='submit' value='Search'>";
        echo "</form>";
	//Ending html
	endHTML();
}

function startHTML() {
	echo " <html><head><title>Search records!</title></head>
	<body><h1>Search Records!</h1>";
}

function endHTML(){
	echo "</body></html>";
}
?>
