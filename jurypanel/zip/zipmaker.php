<?php
include("../op/connect.php");

if ($_FILES["file"]["error"] > 0)
{
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
}
else
{
    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br>";
    //echo "Stored in: " . $_FILES["file"]["tmp_name"];
	$a=$_FILES["file"]["tmp_name"];
	//echo $a;
	
// Name of your CSV file
$csv_file = $a;
    
	# create new zip opbject
     $zip = new ZipArchive();
     # create a temp file & open it
     $tmp_file = tempnam('.','');
     $zip->open($tmp_file, ZipArchive::CREATE);
	 
	 
if (($getfile = fopen($csv_file, "r")) !== FALSE) {
         $data = fgetcsv($getfile, 1000, ",");
   while (($data = fgetcsv($getfile, 1000, ",")) !== FALSE) {
     //$num = count($data);
	   //echo $num;
        //for ($c=0; $c < $num; $c++) {
            $result = $data;
        	$str = implode(",", $result);
        	$slice = explode(",", $str);
			
			 $col3 = $slice[3];
			 
	$pics1 = mysql_query("select * from pics where id = '$col3'")or die(mysql_error());
    $tr_pics1 = mysql_fetch_array($pics1);

	$file="salon.ypsbengaluru.com/img/upload/".$tr_pics1['pic'];
        # download file
    $download_file = file_get_contents($file);
	$ext = pathinfo($file, PATHINFO_EXTENSION);
    $renamefile=$col3.$ext;
        #add it to the zip
        $zip->addFromString(basename($file),$download_file);
		$zip->renameName(basename($file),$renamefile);
}
}
    # close zip
    $zip->close();

    # send the file to the browser as a download
    header('Content-disposition: attachment; filename=download.zip');
    header('Content-type: application/zip');
    readfile($tmp_file);	
}
?>