<?php
$test = array(
    
    'default_route' => array('arrangementen','index')
	, 'body' => "bladie 

	moreff

	stufff

	here
	"
	, 'database' => array(
		'driver' => 'sqlite hoi" en vic\'s dingetje'
		, 'sqlite' => array(
			'sqlite_file' => 'data/aroundtown.sqlite'
		)
	)
);

function linebreaks_encode_by_reference(&$value) { 
	if (is_string($value)) {
	     $value = str_replace(array("\n", "\r"), array('**BR**', ''), $value); 
	}
}
function linebreaks_decode_by_reference(&$value) { 
	if (is_string($value)) {
	     $value = str_replace('**BR**', "\n", $value); 
	}
}

// $test2 = json_encode($test);
// echo $test2;
// $test3 = stripslashes($test2);
// echo $test3;
$decoded = json_decode(str_replace(array('\"', '\''), array('\\u0022', '\\u0027'), json_encode($test)), true);
echo nl2br($decoded['body']);
?>
<form id="" action="?" method="post">
	<textarea name="test">bladie </textarea>
	<input type="submit" value="post me">
</form>
<?php
echo '<pre>';
print_r($_POST);
array_walk_recursive($_POST, linebreaks_encode_by_reference);
print_r($_POST);
$_POST = json_decode(stripslashes(str_replace(array('\"', '\''), array('\\u0022', '\\u0027'), json_encode($_POST))), true);
echo($_POST);
array_walk_recursive($_POST, linebreaks_decode_by_reference);
print_r($_POST);
echo '</pre>';
echo nl2br($_POST['test']);
// if (get_magic_quotes_gpc() === 1){
//     $_GET = json_decode(stripslashes(json_encode($_GET, JSON_HEX_APOS)), true);
//     $_POST = json_decode(stripslashes(json_encode($_POST, JSON_HEX_APOS)), true);
//     $_COOKIE = json_decode(stripslashes(json_encode($_COOKIE, JSON_HEX_APOS)), true);
//     $_REQUEST = json_decode(stripslashes(json_encode($_REQUEST, JSON_HEX_APOS)), true);
// }

// no meer zooi
