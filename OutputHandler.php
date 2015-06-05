<?php

include 'OutputConfig.php';

function handle_output($out, $kind, $fname, $buffer) {
	$trusted_out = $out;

	switch ($kind) {
    case "zend":
    	$trusted_out = location_checking($trusted_out, $O_ZEND, $kind, $fname);
        break;	
    case "hhvm":
    	$trusted_out = location_checking($trusted_out, $O_HHVM, $kind, $fname);
        break;
    case "hippyvm":
    	$trusted_out = hippyvm_traceback($trusted_out);
    	$trusted_out = location_checking($trusted_out, $O_HIPPYVM, $kind, $fname);
        break;
    case "hack":
    	$trusted_out = location_checking($trusted_out, $O_HACK, $kind, $fname);
    	break;
    default:
    	return $out;
	}

	foreach ($O_UTIL as $util_tags) {
		$trusted_out = str_replace($util_tags . $fname, "", $trusted_out);
	}
	return fixLineNumbers($trusted_out, $buffer);
}

function location_checking($out, $O_TAGS, $kind, $fname) {
	foreach ($O_TAGS as $tags) {
		$out = str_replace($tags . $fname, "", $out);
	}
	return $out;
}

function fixLineNumbers($string, $buffer_exists) {
	$start = strpos($string, "on line") + 8;
	$end = strlen($string);

	if (!$start) {
		return $string;
	}

	for ($i = $start; $i < strlen($string) - 1; ++$i) {
		if ($string{$i + 1} == '<' || $string{i + 1} == ' ') {
			$end = $i;
			break;
		}
	}
	$actual_value = substr($string, $start, $end - $start + 1) - 4;
	if ($buffer_exists === "true") {
		$actual_value++;
	}

	$actual_string = substr($string, 0, $start) . $actual_value . substr($string, $end + 1, strlen($string));
	return $actual_string;
}

function hippyvm_traceback($error_out) {
	$start = strpos($error_out, "RPython traceback:");
	$end = strpos($error_out, "...");
	if (!$start || !$end) {
		return $error_out;
	}
	return substr($error_out, 0, $start) . " ". substr($error_out, $end + 3, strlen($error_out));
}

?>