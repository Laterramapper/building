<?php
 
// REST API for converting PostGIS data to GeoJSON
require "./dbinfo.php";

//Connect to PostgreSQL database using PDO 
// Need to enable pgsql PDO extension in php.ini file(remember the restarting)
 
$conn = new PDO("pgsql:host=".$host.";dbname=".$dbname."","".$user."","".$password."");

//SQL Geo-DBMS in PostGIS
$sql = "SELECT *, ST_AsGeoJSON(geometry) AS geojson FROM building";
$result = $conn->query($sql);
if(!$result) {
	echo "SQL error!";
	exit();
	
	}


// BuildGeoJSOn feature Collection array
$geojson = array(
   "type"=> "FeatureCollection",
   "features" => array()
);

// Loop Through rows to build feature arrays
while($row = $result->fetch(PDO::FETCH_ASSOC)){
     $properties = $row;
     
     //Remove geojson nd geometry fields from properties
     unset($properties["geojson"]);
     unset($properties["geometry"]);
     
     $feature = array(
         "type" => "Feature",
         "geometry" => json_decode($row["geojson"], true),
         "properties" => $properties
     );	
	
	// Add feature arrays to feature collection array
	array_push($geojson["features"],$feature);
	
}
// CREAT JSON header
header("content-type: application/json");

//Display GeoJSON output add rewrite var
echo json_encode($geojson, JSON_NUMERIC_CHECK);

//Close DB connection
$conn = NULL;

?>
