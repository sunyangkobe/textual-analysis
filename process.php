<?php
/**
 * 2011 Aug 05
 * CSC309 - Textual Analysis
 *
 * Server side textual analysis api
 *
 * @author Kobe Sun
 *
 */

include_once("strProcessor.php");


/**
 * 
 * Determine the file error code and return corresponding string
 * @param int $error_code
 * @return String informative error message
 */
function file_upload_error_message($error_code) {
	switch ($error_code) {
		case UPLOAD_ERR_INI_SIZE:
			return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		case UPLOAD_ERR_FORM_SIZE:
			return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
		case UPLOAD_ERR_PARTIAL:
			return 'The uploaded file was only partially uploaded';
		case UPLOAD_ERR_NO_FILE:
			return 'No file was uploaded';
		case UPLOAD_ERR_NO_TMP_DIR:
			return 'Missing a temporary folder';
		case UPLOAD_ERR_CANT_WRITE:
			return 'Failed to write file to disk';
		case UPLOAD_ERR_EXTENSION:
			return 'File upload stopped by extension';
		default:
			return 'Unknown upload error';
	}
}


// Process uploaded file, exit if error happens
if (empty($_FILES["file"])) {
	exit(json_encode("Unknown upload error, possibly too big or wrong type"));
} elseif ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
	$fp = file_get_contents($_FILES["file"]["tmp_name"]);
	if ($fp) {
		$srFile = strtolower(strip_tags($fp));
	} else {
		exit(json_encode("Failed to open the file"));
	}
} elseif ($_FILES["file"]["error"] == UPLOAD_ERR_NO_FILE) {
	$srFile = "";
} else {
	exit(json_encode(file_upload_error_message($_FILES["file"]["error"])));
}

// Process url text, exit if error happens. 
$url_pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*'
			 . '(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
if (empty($_REQUEST["url"])) {
	$srURL = "";
} elseif (preg_match($url_pattern, $_REQUEST["url"])) {
	$urlp = file_get_contents($_REQUEST["url"]);
	if ($urlp) {
		$srURL = strtolower(strip_tags($urlp));
	} else {
		exit(json_encode("Failed to open " . $_REQUEST["url"]));
	}
} else {
	exit(json_encode("The URL you specified was invalid"));
}

// cast topk as an int
$topk = intval($_REQUEST["topk"]);
// strip out all html tags and convert all chars to lower case
$srText = strtolower(strip_tags($_REQUEST["text"]));

// build instances
$url_obj = new strProcessor($srURL, $topk);
$file_obj = new strProcessor($srFile, $topk);
$text_obj = new strProcessor($srText, $topk);
$total = new strProcessor("", $topk);

// combine all data into one instance
$total->combineData($url_obj);
$total->combineData($file_obj);
$total->combineData($text_obj);

// finally, return the result as json format
exit(json_encode(array(
	"success" => true,
	"total" => $total->gatherStats(),
	"url" => $url_obj->gatherStats(),
	"file" => $file_obj->gatherStats(),
	"text" => $text_obj->gatherStats()
)));

?>
