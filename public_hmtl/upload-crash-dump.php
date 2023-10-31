<?php
$uploads_dir = './interiorcraft-crash-dumps'; //Directory to save the file that comes from client application.

if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
    $tmp_name = $_FILES["file"]["tmp_name"];
    $name = $_FILES["file"]["name"];	
	$size = $_FILES["file"]["size"];	

	$file_parts = pathinfo($name);

	if ($size > 1024*1024 || $file_parts['extension'] != "zip") {
    	http_response_code(400);
	    exit('File uploading failed.');
	}

    move_uploaded_file($tmp_name, "$uploads_dir/$name");
}
?>