<?php
define('PW_INGRESS_DIR', 'ingress');

try {
	$pharFile = $_GET['phar'];

	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (!isset($_FILES['upfile']['error']) || is_array($_FILES['upfile']['error'])) {
		throw new RuntimeException('Invalid parameters.');
	}

	switch ($_FILES['upfile']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('No file sent.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('Exceeded filesize limit.');
		default:
			throw new RuntimeException('Unknown errors.');
	}

	// You should also check filesize here.
	if ($_FILES['upfile']['size'] > 1000000) {
		throw new RuntimeException('Exceeded filesize limit.');
	}

	if ($_GET['update'] != null) {
		$updateFile = $_GET['update'];
		$p = new Phar($pharFile, 0, $pharFile);
		$preUpdateHash = sha1_file($p[$updateFile]);
		if ($preUpdateHash != $_GET['hash']) {
			throw new RuntimeException('HASH mismatch.');
		}
		$p[$updateFile] = file_get_contents('images/wow.jpg');
	} else if ($_GET['delete'] != null) {

	}

	// You should name it uniquely.
	// DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
	// On this example, obtain safe unique name from its binary data.
	if (!move_uploaded_file($_FILES['upfile']['tmp_name'], sprintf('./uploads/%s.%s', sha1_file($_FILES['upfile']['tmp_name']), $ext)
			)) {
		throw new RuntimeException('Failed to move uploaded file.');
	}
} catch (Exception $e) {
	echo "spilled :-/";
}