<?php
// function array_reconstruct($arr, $level = 0) {
// $tab = "";
// for($i = 0; $i < $level; $i ++)
// $tab .= "\t";
// foreach ( $arr as $k1 => $e1 ) {
// if (! is_array ( $e1 ))
// $arr [$k1] = '{' . "$tab$k1 , \"$e1\"" . '}';
// else
// $arr [$k1] = '{' . "$tab$k1 , " . array_reconstruct ( $e1, $level + 1 ) . '}';
// }

// return "{" . PHP_EOL . implode ( "," . PHP_EOL, $arr ) . PHP_EOL . $tab . "}";
// }

// $arr = [ "private","public","static"
// ];

// echo array_reconstruct ( $arr );
// =========================================================================================
// $g = "g";
// function &a(&$input) {
// $input = "a";
// echo "Inside a: " . $input . PHP_EOL;
// return $input;
// }
// function &b(&$input) {
// $ref = &a ( $input );
// $ref = "b";
// echo "Inside b: " . $input . PHP_EOL . $ref . PHP_EOL;
// return $ref;
// }
// b ( $g );
// echo $g;
// =========================================================================================
$g = 3;
function &ref() {
	return $GLOBALS ["g"];
}
function a() {
	$a = &ref ();
	$a = 4;
}
a ();
echo $g;