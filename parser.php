<?php
include ("GTException.php");
$operators = [ ":","::","=","==",">","<","==","!==",".",",",":","+",">=","<=","!=","!","-","*","/","%","+:","++","-:","--",".:","[]","*:","/:","%:","<>","?","=>","->",">>","<<","..","&&","||","|&","(",")","{","}","[","]"
];
$operator_1st_chars = [ ];
$operators_by_chars_count = [ 3 => [ ],2 => [ ],1 => [ ]
];
$real_operators = [ "=" => [ 2,3
],"==" => [ 2,5
],">" => [ 2,4
],"<" => [ 2,4
],"==" => [ 2,5
],"!==" => [ 2,5
],'.' => [ 2,3
],"," => [ 2,3
],"+" => [ 2,3
],">=" => [ 2,4
],"<=" => [ 2,4
],"!=" => [ 2,5
],"!" => [ 1,1
],"-" => [ 2,3
],"*" => [ 2,2
],"/" => [ 2,2
],"%" => [ 2,2
],":" => [ 2,6
],"+:" => [ 2,6
],"++" => [ 0,3
],"-:" => [ 2,3
],"--" => [ 0,6
],".:" => [ 2,6
],"[]" => [ 0,0
],"*:" => [ 2,6
],"/:" => [ 2,6
],"%:" => [ 2,6
],"<>" => [ 2,5
],"&&" => [ 2,7
],"||" => [ 2,9
],"|&" => [ 2,8
],"get_ele" => [ 2,0
]
];
$keywords = [ "if","else","class","do","while","break","use","switch","new","continue","return","include","throw","try","catch","finally","defcat","deffi","elseif","new","do","case","continue","for","times","from","to","foreach","as","at"
];
$modifiers = [ "private","public","static"
];
define ( "CPP_EOL", PHP_EOL );
$funcs = $classes = [ ];
$main = [ ];
foreach ( $operators as $operator ) {
    if (array_search ( $operator [0], $operator_1st_chars ) === false)
        $operator_1st_chars [] = $operator [0];
    $len = strlen ( $operator );
    if (array_search ( $operator, $operators_by_chars_count [$len] ) === false)
        $operators_by_chars_count [$len] [] = $operator;
}
function array_reconstruct($arr, $level = 0) {
    $tab = "";
    for($i = 0; $i < $level; $i ++)
        $tab .= "\t";
    foreach ( $arr as $k1 => $e1 ) {
        if (! is_array ( $e1 ))
            $arr [$k1] = "$tab\"$k1\" => \"$e1\"";
        else
            $arr [$k1] = "$tab\"$k1\" => " . array_reconstruct ( $e1, $level + 1 );
    }

    return "[" . PHP_EOL . implode ( "," . PHP_EOL, $arr ) . PHP_EOL . $tab . "]";
}
function is_az(string $inp) {
    for($i1 = 0; $i1 < strlen ( $inp ); $i1 ++) {
        $matches = [ ];
        preg_match ( '/[a-zA-Z]/', $inp [$i1], $matches );
        if (! $matches)
            return false;
    }
    return true;
}
function get_1st_ele(array $arr) {
    if (! $arr)
        return false;
    foreach ( $arr as $ele ) {
        return $ele;
    }
}
function get_last_ele(array $arr) {
    if (! $arr)
        return false;
    return end ( $arr );
    // return $arr;
}
function remove_from_string($str, $start, $stop) {
    if ($start >= $stop || $stop > strlen ( $str ))
        return $str;
    $str = substr ( $str, 0, $start ) . " " . substr ( $str, $stop + 1 );
    return $str;
}
function get_first_pos_or_end_of_str($content, $str, $start_pos) {
    $pos_end = strpos ( $content, $str, $start_pos );
    if ($pos_end === FALSE)
        $pos_end = strlen ( $content );
    else
        $pos_end += strlen ( $str );
    return $pos_end;
}
function remove_comment_blocks($content) {
    $len = strlen ( $content );
    $i = 0;
    while ( $i < $len - 1 ) {
        if ($content [$i] . $content [$i + 1] == "/*") {
            $last_pos = get_first_pos_or_end_of_str ( $content, "*/", $i );
            $content = remove_from_string ( $content, $i, $last_pos );
            $len = strlen ( $content );
        }
        $i ++;
    }
    return $content;
}
function remove_inline_comments($content) {
    $arr = explode ( "\n", $content );
    foreach ( $arr as $k => $v ) {
        if (($pos = strpos ( $v, "//" )) !== false)
            $arr [$k] = substr ( $v, 0, $pos );
    }
    return implode ( "\n", $arr );
}
function remove_comments($content) {
    $content = remove_inline_comments ( $content );
    $content = remove_comment_blocks ( $content );
    return $content;
}
function rtrim_all_lines(string $content) {
    $arr = explode ( "\n", $content );
    foreach ( $arr as $k => $v ) {
        $arr [$k] = rtrim ( $v );
    }
    return implode ( "\n", $arr );
}
function rtrim_all_lines_arr(array $content) {
    foreach ( $content as $k0 => $line ) {
        foreach ( $line [0] as $k1 => $token )
            if ($token [1] != "spaces") {
                $kx = $k1;
                break;
            }
        $content [$k0] [0] = array_slice ( $line [0], $kx );
    }
    return $content;
}
function tokens_arr_to_str1(array $tokens_arr) {
    $return = "";
    foreach ( $tokens_arr as $token )
        $return .= $token [0];
    return $return;
}
function explode_tokens($del, $tokens_arr) {
    $return = [ 0 => [ ]
    ];
    $k = 0;
    if (is_array ( $del )) {
        foreach ( $tokens_arr as $token ) {
            if ($token == $del) {
                $k ++;
                $return [$k] = [ ];
            } else {
                array_push ( $return [$k], $token );
            }
        }
        return $return;
    } else {
        foreach ( $tokens_arr as $token ) {
            if ($token [0] === $del) {
                $k ++;
                $return [$k] = [ ];
            } else {
                array_push ( $return [$k], $token );
            }
        }
        return $return;
    }
}
function array_search_multi($search, array $arr) {
    $return = [ ];
    foreach ( $arr as $k => $v ) {
        if ($search == $v)
            array_push ( $return, $k );
    }
    return $return;
}
function remove_spaces_from_tokens_arr(array $tokens_arr) {
    $return = [ ];
    foreach ( $tokens_arr as $token ) {
        if (isset ( $token [1] ) && $token [1] != "spaces")
            $return [] = $token;
    }
    return $return;
}
// ========================================================================================;
// ========================================================================================;
// ========================================================================================;
function remove_blank_lines(string $content) {
    $arr = explode ( "\n", $content );
    foreach ( $arr as $k => $v )
        if (is_blank_str ( $v ))
            unset ( $arr [$k] );
    return implode ( "\n", $arr );
}
function process_first_line(string $content) {
    $arr = explode ( "\n", $content );
    $first_line = trim ( $arr [0] );
    if (strpos ( $first_line, "#" ) === 0)
        array_shift ( $arr );
    return implode ( "\n", $arr );
}
function new_line(&$arr, &$line) {
    $line ++;
    if (! isset ( $arr [$line] ))
        $arr [$line] = [ [ ],0
        ];
}
function tokenize(string $content) {
    $content = process_first_line ( $content );
    $output_arr = [ ];
    $content = remove_comments ( $content );
    $content = remove_blank_lines ( $content );
    $content = "\n" . rtrim_all_lines ( $content );
    $len = strlen ( $content );
    $line = 0;
    $block_lv = 0;
    for($i = 0; $i < $len; $i ++) {
        // new_line ( $output_arr, $line );
        // if (! isset ( $output_arr [$line] ))
        // $output_arr [$line] = [ 0 => [ ],1 => 0
        // ];
        if ($content [$i] === "\n" && isset ( $content [$i + 1] ) && $content [$i + 1] === "\n") {
            $i += 1;
            continue;
        } elseif (substr ( $content, $i, 3 ) === "...") {
            $i += 2;
        } elseif ($content [$i] === "\n" && substr ( $content, $i - 3, 3 ) === "...") {
            $i += 3;
        } elseif ($content [$i] === ";") {
            new_line ( $output_arr, $line );
        } elseif ($content [$i] === "\n") {
            new_line ( $output_arr, $line );
            // ignore first tabs and spaces;
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                if ($content [$i + $i1] !== " " && $content [$i + $i1] !== "\t")
                    break;
            }
            $extracted_str = substr ( $content, $i + 1, $i1 - 1 );
            $extracted_str = str_ireplace ( "\t", "    ", $extracted_str );
            $block_lv = round ( strlen ( $extracted_str ) / 4, 0 );
            $output_arr [$line] [1] = $block_lv;
            $i += $i1 - 1;
        } elseif ($content [$i] == " " || $content [$i] == "\t") {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                if ($content [$i + $i1] !== " ")
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"spaces"
            ] );
        } elseif ($content [$i] == " " || $content [$i] == "\t") {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                if ($content [$i + $i1] !== " ")
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"spaces"
            ] );
        } elseif ($content [$i] == '\\' && isset ( $content [$i + 1] )) {
            for($i1 = 2; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            // if (strlen ( $extracted_str ) == 1)
            // throw new Exception ( "wrong string format" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"string"
            ] );
            $i += $i1 - 1;
        } elseif ($content [$i] == '$' && $content [$i + 1] == '?') {
            array_push ( $output_arr [$line] [0], [ '$answer_var',"var"
            ] );
            $i += 1;
            continue;
        } elseif ($content [$i] == '$' && $content [$i + 1] == '@') {
            array_push ( $output_arr [$line] [0], [ '$list_params',"var"
            ] );
            $i += 1;
            continue;
        } elseif ($content [$i] == '$' && $content [$i + 1] == '$') {
            for($i1 = 2; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\-\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (strlen ( $extracted_str ) == 1)
                throw new Exception ( "wrong var format" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"var_global"
            ] );
            $i += $i1 - 1;
        } elseif ($content [$i] == '$') {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\-\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (strlen ( $extracted_str ) == 1)
                throw new Exception ( "wrong var format" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"var"
            ] );
            $i += $i1 - 1;
        } elseif ($content [$i] == '@' && $content [$i + 1] == '@') {
            for($i1 = 2; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\-\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (strlen ( $extracted_str ) == 1 || is_numeric ( $extracted_str [1] ))
                throw new Exception ( "wrong function format @$" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"function_self"
            ] );
            $i += $i1 - 1;
        } elseif ($content [$i] == '@' && $content [$i + 1] == '$') {
            for($i1 = 2; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\-\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (strlen ( $extracted_str ) == 1 || is_numeric ( $extracted_str [1] ))
                throw new Exception ( "wrong function format @$" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"function_var"
            ] );
            $i += $i1 - 1;
        } elseif ($content [$i] == '@' && $content [$i + 1] != '@') {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\-\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (strlen ( $extracted_str ) == 1 || is_numeric ( $extracted_str [1] ))
                throw new Exception ( "wrong function format" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"function"
            ] );
            $i += $i1 - 1;
        } elseif ($content [$i] == '^') {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\-\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (strlen ( $extracted_str ) == 1)
                throw new Exception ( "wrong class format" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"class"
            ] );
            $i += $i1 - 1;
        } elseif (is_numeric ( $content [$i] )) {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[0-9\.]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (substr_count ( $extracted_str, "." ) > 1)
                throw new Exception ( "wrong number format, more than 1 point" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"number"
            ] );
            $i += $i1 - 1;
        } elseif ($content [$i] == '"') {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                if ($content [$i + $i1] == "\"" && $content [$i + $i1 - 1] != "\\")
                    break;
            }
            $extracted_str = substr ( $content, $i + 1, $i1 - 1 );
            // if (strlen ( $extracted_str ) == 1 || is_numeric ( $extracted_str [1] ))
            // throw new Exception ( "wrong string format" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"string"
            ] );
            $i += $i1;
        } elseif ($content [$i] == '\'') {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                if ($content [$i + $i1] == "'" && $content [$i + $i1 - 1] != "\\")
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 - 1 );
            // if (strlen ( $extracted_str ) == 1 || is_numeric ( $extracted_str [1] ))
            // throw new Exception ( "wrong string format" );
            array_push ( $output_arr [$line] [0], [ $extracted_str,"string"
            ] );
            $i += $i1;
        } elseif (is_az ( $content [$i] )) {
            for($i1 = 1; $i1 < $len - $i; $i1 ++) {
                $matches = [ ];
                preg_match ( '/[a-zA-Z0-9\_]/', $content [$i + $i1], $matches );
                if (! $matches)
                    break;
            }
            $extracted_str = substr ( $content, $i, $i1 );
            if (in_array ( $extracted_str, $GLOBALS ["keywords"] ))
                array_push ( $output_arr [$line] [0], [ $extracted_str,"keyword"
                ] );
            elseif (in_array ( $extracted_str, $GLOBALS ["modifiers"] ))
                array_push ( $output_arr [$line] [0], [ $extracted_str,"modifier"
                ] );
            else
                array_push ( $output_arr [$line] [0], [ $extracted_str,"string"
                ] );
            $i += $i1 - 1;
        } elseif (in_array ( $content [$i], $GLOBALS ["operator_1st_chars"] )) {
            foreach ( $GLOBALS ["operators_by_chars_count"] as $char_count => $operators ) {
                foreach ( $operators as $operator ) {
                    if (substr ( $content, $i, $char_count ) === $operator) {
                        array_push ( $output_arr [$line] [0], [ substr ( $content, $i, $char_count ),"operator"
                        ] );
                        $i += $char_count - 1;
                        continue 3;
                    }
                }
            }
            array_push ( $output_arr [$line] [0], [ $content [$i],"other0"
            ] );
        } else {
            array_push ( $output_arr [$line] [0], [ $content [$i],"other"
            ] );
        }
    }
    foreach ( $output_arr as $i => $output_line ) {
        if (! $output_line [0])
            unset ( $output_arr [$i] );
    }
    $output_arr = array_values ( $output_arr );
    return $output_arr;
}
function is_blank_str(string $str) {
    for($i = 0; $i < strlen ( $str ); $i ++)
        if ($str [$i] !== " " && $str [$i] !== "\t")
            return false;
    return true;
}
function ltrim_arr(array $arr) {
    foreach ( $arr as $k => $v ) {
        if (! is_blank_str ( $v [0] ))
            break;
        unset ( $arr [$k] );
    }
    return array_values ( $arr );
}
function in_array_lv2_0($ele, $arr) {
    /*
     * check if $ele is one of $array[i][0];
     * return true or false;
     */
    foreach ( $arr as $ele_tmp ) {
        if ($ele == $ele_tmp [0])
            return true;
    }
    return false;
}
function in_array_lv2_1($ele, $arr) {
    /*
     * check if $ele is one of $array[i][0];
     * return true or false;
     */
    foreach ( $arr as $ele_tmp ) {
        if ($ele == $ele_tmp [1])
            return true;
    }
    return false;
}
function get_arr_between_brackets($open_bracket, $tokens_arr, $pos_of_open_bracket) {
    $close_bracket = ")";
    if ($open_bracket == "[")
        $close_bracket = "]";
    $lv = 1;
    foreach ( $tokens_arr as $k => $v ) {
        if ($k <= $pos_of_open_bracket)
            continue;
        elseif ($v [0] == $open_bracket && $v [1] == "operator")
            $lv ++;
        elseif ($v [0] == $close_bracket && $v [1] == "operator") {
            $lv --;
            if ($lv == 0)
                break;
        }
    }
    return array_slice ( $tokens_arr, $pos_of_open_bracket + 1, $k - $pos_of_open_bracket - 1 );
}
function get_function_and_params_lv0($tokens_arr) {
    $return = [ "name" => "","params" => [ ]
    ];
    // $lv0 = $lv1 = 0;
    // $return1_i = 0;
    // $return ["params"] [$return1_i] = [ ];
    // if (! $tokens_arr)
    // return false;
    // if (sizeof ( $tokens_arr ) == 1 && get_1st_ele ( $tokens_arr ) [1] != "function")
    // return FALSE;
    // foreach ( $tokens_arr as $v ) {
    // if ($v [0] == "(")
    // $lv0 ++;
    // elseif ($v [0] == ")")
    // $lv0 --;
    // elseif ($v [0] == "[")
    // $lv1 ++;
    // elseif ($v [0] == "]")
    // $lv0 --;
    // // if ($lv0 < 0 || $lv1 < 0)
    // // throw new Exception("invalid bracket");
    // if ($v [1] == "function" && $lv0 == 0 && $lv1 == 0) {
    // array_push ( $return ["names"], $v [0] );
    // $return1_i ++;
    // $return ["params"] [$return1_i] = [ ];
    // } elseif ($lv0 == 0 && $lv1 == 0 && $v [0] == ",") {
    // $return1_i ++;
    // $return ["params"] [$return1_i] = [ ];
    // } elseif ($lv0 == 0 && $lv1 == 0 && $v [1] == "spaces")
    // continue;
    // elseif ($lv0 == 0 && $lv1 == 0 && ($v [1] == "(" || $v [1] == ")"))
    // continue;
    // else
    // $return ["params"] [$return1_i] = parse_expression ( [ $v
    // ] );
    // }
    // foreach ( $return ["params"] as $i => $param ) {
    // if (! $param)
    // unset ( $return ["params"] [$i] );
    // }
    // $return ["params"] = array_values ( $return ["params"] );
    // if (sizeof ( $return ["names"] ) > 1)
    // throw new Exception ( "2 func" );
    $all_func_names_pos = find_all_token_at_level0_by_type ( "function", $tokens_arr );
    if (sizeof ( $all_func_names_pos ) != 1)
        // throw new Exception ( "invalid func name count" );
        return false;
    $return ["name"] = $tokens_arr [$all_func_names_pos [0]] [0];
    $two_sides = array_cut_by_pos ( $tokens_arr, $all_func_names_pos );
    $tmp_array = [ ];
    $left_comma_pos = find_all_token_at_level0 ( [ ",","operator"
    ], $two_sides [0] );
    $tmp_array = array_cut_by_pos ( $two_sides [0], $left_comma_pos );
    $right_comma_pos = find_all_token_at_level0 ( [ ",","operator"
    ], $two_sides [1] );
    $tmp_array = array_merge ( $tmp_array, array_cut_by_pos ( $two_sides [1], $right_comma_pos ) );
    foreach ( $tmp_array as $v ) {
        if ($v)
            $return ["params"] [] = parse_expression ( $v );
    }
    return $return;
}
// function parse_one_element_arr(array $tokens_arr) {
// $lv1 = $lv2 = $element_no = 0;
// $tmp_arr = [ ];
// foreach ( $tokens_arr as $token ) {
// if ($token [0] == "(" && $token [1] == "operator") {
// $lv1 ++;
// } elseif ($token [0] == "[" && $token [1] == "operator") {
// $lv2 ++;
// } elseif ($token [0] == ")" && $token [1] == "operator") {
// $lv1 --;
// } elseif ($token [0] == "]" && $token [1] == "operator") {
// $lv2 --;
// } elseif ($token [0] == "=>" && $token [1] == "operator" && $lv1 == 0 && $lv2 == 0) {
// $element_no ++;
// } else {
// $tmp_arr [$element_no];
// $tmp_arr [$element_no] [] = $token;
// }
// foreach ( $tmp_arr as $k => $v ) {
// $tmp_arr [$k] = parse_one_element_arr ( $v );
// }
// }
// }
function getline_subblock($tokens_arr, $header_line_i) {
    $header_lv = $tokens_arr [$header_line_i] [1];
    $tmp_arr = array_slice ( $tokens_arr, $header_line_i );
    $tmp_arr = array_values ( $tmp_arr );
    $i = 0;
    for($i = 0; $i < sizeof ( $tmp_arr ) - 1; $i ++) {
        $v = $tmp_arr [$i + 1];
        if ($v [1] <= $header_lv)
            return [ $i,array_slice ( $tokens_arr, $header_line_i + 1, $i )
            ];
    }
    return [ $i,array_slice ( $tokens_arr, $header_line_i + 1 )
    ];
}
function parse_array(array $tokens_arr) {
    $all_commas = find_all_token_at_level0 ( [ ",","operator"
    ], $tokens_arr );
    $elements = array_cut_by_pos ( $tokens_arr, $all_commas );
    $return = [ ];
    foreach ( $elements as $element ) {
        $return [] = parse_array_element ( $element );
    }
    return [ "type" => "array","body" => $return
    ];
}
function parse_array_element(array $tokens_arr) {
    // $lv1 = $lv2 = $element_no = 0;
    $tmp_arr = [ ];
    // foreach ( $tokens_arr as $token ) {
    // if ($token [0] == "(" && $token [1] == "operator") {
    // $lv1 ++;
    // } elseif ($token [0] == "[" && $token [1] == "operator") {
    // $lv2 ++;
    // } elseif ($token [0] == ")" && $token [1] == "operator") {
    // $lv1 --;
    // } elseif ($token [0] == "]" && $token [1] == "operator") {
    // $lv2 --;
    // } elseif ($token [0] == "=>" && $token [1] == "operator" && $lv1 == 0 && $lv2 == 0) {
    // $element_no ++;
    // } else {
    // if (! isset ( $tmp_arr [$element_no] ))
    // $tmp_arr [$element_no] = [ ];
    // array_push ( $tmp_arr [$element_no], $token );
    // }
    // }
    //
    $all_marks_pos = find_all_token_at_level0 ( [ "=>","operator"
    ], $tokens_arr );
    $tmp_arr = array_cut_by_pos ( $tokens_arr, $all_marks_pos );
    if (sizeof ( $tmp_arr ) > 2 || sizeof ( $tmp_arr ) < 1)
        throw new Exception ( "Invalid array declaration." );
    elseif (sizeof ( $tmp_arr ) == 1)
        return [ "key" => NULL,"value" => parse_expression ( $tmp_arr [0] )
        ];
    elseif (sizeof ( $tmp_arr ) == 2)
        return [ "key" => parse_expression ( $tmp_arr [0] ),"value" => parse_expression ( $tmp_arr [1] )
        ];
}
function is_token($input) {
    if (isset ( $input [0] ) && isset ( $input [1] ) && sizeof ( $input ) == 2 && ! is_array ( $input [0] ) && ! is_array ( $input [1] ))
        return true;
    return false;
}
function find_all_token_at_level0($token_to_compare, array $tokens_arr) {
    $lv1 = $lv2 = 0;
    $return = [ ];
    foreach ( $tokens_arr as $k => $token ) {
        if (! is_token ( $token )) {
            continue;
        }
        if ($token [0] == "(" && $token [1] == "operator") {
            $lv1 ++;
        } elseif ($token [0] == "[" && $token [1] == "operator") {
            $lv2 ++;
        } elseif ($token [0] == ")" && $token [1] == "operator") {
            $lv1 --;
        } elseif ($token [0] == "]" && $token [1] == "operator") {
            $lv2 --;
        } elseif ($token == $token_to_compare && $lv1 == 0 && $lv2 == 0) {
            array_push ( $return, $k );
        }
    }
    return $return;
}
function find_all_token_at_level0_by_type($token_type_to_compare, array $tokens_arr) {
    $lv1 = $lv2 = 0;
    $return = [ ];
    foreach ( $tokens_arr as $k => $token ) {
        if (! is_token ( $token )) {
            continue;
        }
        if ($token [0] == "(" && $token [1] == "operator") {
            $lv1 ++;
        } elseif ($token [0] == "[" && $token [1] == "operator") {
            $lv2 ++;
        } elseif ($token [0] == ")" && $token [1] == "operator") {
            $lv1 --;
        } elseif ($token [0] == "]" && $token [1] == "operator") {
            $lv2 --;
        } elseif ($token [1] == $token_type_to_compare && $lv1 == 0 && $lv2 == 0) {
            array_push ( $return, $k );
        }
    }
    return $return;
}
function array_cut_by_pos($arr, $positions) {
    if (! $positions) {
        return [ $arr
        ];
    }
    $return = [ ];
    foreach ( $positions as $i => $pos ) {
        $start = isset ( $positions [$i - 1] ) ? $positions [$i - 1] + 1 : 0;
        $stop = isset ( $positions [$i] ) ? $pos : sizeof ( $arr );
        $len = $stop - $start;
        $return [$i] = array_slice ( $arr, $start, $len );
    }

    $return [$i + 1] = array_slice ( $arr, $pos + 1 );
    return $return;
}
function parse_var($tokens_arr) {
    $tokens_arr = remove_all_spaces ( $tokens_arr );
    if ($tokens_arr [0] [1] != "var" && $tokens_arr [0] [1] != "var_global")
        throw new Exception ( "not a var" );
    $tokens_arr1 = array_slice ( $tokens_arr, 1 );
    $lv = 0;
    // $tmp = [ ];
    foreach ( $tokens_arr1 as $token ) {
        if ($token == [ "[","operator"
        ]) {
            $lv ++;
        } elseif ($token == [ "]","operator"
        ]) {
            $lv --;
        } elseif ($token == [ "get_ele","operator"
        ] && $lv == 0) {
            break;
        } elseif ($lv == 0)
            throw new Exception ( "not valid var" );
    }
    return parse_expression ( $tokens_arr );
}
function parse_complex_var($tokens_arr) {
    $all_dots_pos = find_all_token_at_level0 ( [ ".","operator"
    ], $tokens_arr );
    $cutted = array_cut_by_pos ( $tokens_arr, $all_dots_pos );
    foreach ( $cutted as $var ) {
        try {
            parse_var ( $var );
        } catch ( Exception $e ) {
            return false;
        }
    }
    return parse_expression ( $tokens_arr );
}
function process_assignment(array $tokens_arr) {
    $tokens_arr = remove_all_spaces ( $tokens_arr );
    $pos_of_all_colon = find_all_token_at_level0 ( [ ":","operator"
    ], $tokens_arr );
    if (sizeof ( $pos_of_all_colon ) == 0) {
        return false;
    } elseif (sizeof ( $pos_of_all_colon ) > 1) {
        $return = [ ];
        $tmp_arr = array_cut_by_pos ( $tokens_arr, $pos_of_all_colon );
        $value = array_pop ( $tmp_arr );
        foreach ( $tmp_arr as $tmp_ele ) {
            array_push ( $tmp_ele, [ ":","operator"
            ] );
            $tmp_ele = array_merge ( $tmp_ele, $value );
            $return [] = process_assignment ( $tmp_ele );
        }
        return [ "type" => "inline_block","body" => $return
        ];
    }
    $post_of_1st_colon = $pos_of_all_colon [0];
    $left = array_slice ( $tokens_arr, 0, $post_of_1st_colon );
    $right = array_slice ( $tokens_arr, $post_of_1st_colon + 1 );
    $left_commas_lv0 = find_all_token_at_level0 ( [ ",","operator"
    ], $left );
    $right_commas_lv0 = find_all_token_at_level0 ( [ ",","operator"
    ], $right );
    if (sizeof ( $left_commas_lv0 ) != sizeof ( $right_commas_lv0 ))
        throw new Exception ( "not valid assignment size" );
    if (! $left_commas_lv0) {
        return [ "type" => "assignment","left" => parse_complex_var ( $left ),"right" => ($tmp = parse_expression ( $right )),"return" => $tmp
        ];
    } else {
        $return = [ ];
        $left_slided = array_cut_by_pos ( $left, $left_commas_lv0 );
        $right_slided = array_cut_by_pos ( $right, $right_commas_lv0 );
        foreach ( $left_slided as $i => $one_var ) {
            $one_value = $right_slided [$i];
            array_push ( $return, [ "type" => "assignment","left" => parse_complex_var ( $one_var ),"right" => parse_expression ( $one_value )
            ] );
        }
        return [ "type" => "inline_block","body" => $return
        ];
    }
}
function parse_expression_simple($tokens_arr) {
    # TODO:
    ;
}
function process_multiple_operators($tokens_arr) {
    while ( true ) {
        $arr_operators = [ ];
        foreach ( $tokens_arr as $k => $token ) {
            if (is_token ( $token ) && $token [1] == "operator") {
                array_push ( $arr_operators, [ $k,$token [0]
                ] );
            }
        }
        if (sizeof ( $arr_operators ) == 1)
            return [ "type" => "expr","body" => tokens_arr_to_name_and_type_format ( $tokens_arr )
            ];
        $min = 30;
        $highest_operator_pos = 0;
        $highest_operator = "";
        foreach ( $arr_operators as $operator ) {
            if ($GLOBALS ["real_operators"] [$operator [1]] [1] < $min) {
                $highest_operator_pos = $operator [0];
                $highest_operator = $operator [1];
                $min = $GLOBALS ["real_operators"] [$operator [1]] [1];
            }
        }
        if ($GLOBALS ["real_operators"] [$highest_operator] [0] == 0) {
            $tmp = array_slice ( $tokens_arr, 0, $highest_operator_pos );
            $tmp = array_push ( $tmp, [ "type" => "expr","body" => [ $tokens_arr [$highest_operator_pos],$tokens_arr [$highest_operator_pos + 1]
            ]
            ] );
            $tmp = array_merge ( $tmp, array_slice ( $tokens_arr, $highest_operator_pos + 2 ), sizeof ( $tokens_arr ) );
            // return $return;
        } elseif ($GLOBALS ["real_operators"] [$highest_operator] [0] == 1) {
            $tmp = array_slice ( $tokens_arr, 0, $highest_operator_pos - 1 );
            $tmp = array_push ( $tmp, [ "type" => "expr","body" => [ $tokens_arr [$highest_operator_pos - 1],$tokens_arr [$highest_operator_pos]
            ]
            ] );
            $tmp = array_merge ( $tmp, array_slice ( $tokens_arr, $highest_operator_pos + 1 ), sizeof ( $tokens_arr ) );
            // return $return;
        } elseif ($GLOBALS ["real_operators"] [$highest_operator] [0] == 2) {
            $tmp = array_slice ( $tokens_arr, 0, $highest_operator_pos - 1 );
            array_push ( $tmp, [ "type" => "expr","body" => [ $tokens_arr [$highest_operator_pos - 1],$tokens_arr [$highest_operator_pos],$tokens_arr [$highest_operator_pos + 1]
            ]
            ] );
            $tmp = array_merge ( $tmp, array_slice ( $tokens_arr, $highest_operator_pos + 2 ) );
            // return $return;
        }
        $tokens_arr = $tmp;
    }
    // return [ "type" => "expr","body" => tokens_arr_to_name_and_type_format ( $tmp )
    // ];
}
function is_new_clause($tokens_arr) {
    $tokens_arr = remove_all_spaces ( $tokens_arr );
    if (sizeof ( $tokens_arr ) < 2)
        return false;
    foreach ( $tokens_arr as $token ) {
        if ($token == [ "new","keyword"
        ])
            return true;
    }
    return false;
}
function tokens_arr_trim(array $tokens_arr) {
    $token_array_trimmed = $tokens_arr;
    $token_array_trimmed = tokens_arr_trim1 ( $tokens_arr );
    while ( sizeof ( $tokens_arr ) > sizeof ( $token_array_trimmed ) ) {
        $tokens_arr = $token_array_trimmed;
        $token_array_trimmed = tokens_arr_trim1 ( $tokens_arr );
    }
    return $token_array_trimmed;
}
function tokens_arr_trim1(array $tokens_arr) {
    reset ( $tokens_arr );
    if (isset ( $tokens_arr [sizeof ( $tokens_arr ) - 1] [1] ) && $tokens_arr [sizeof ( $tokens_arr ) - 1] [1] == "spaces")
        array_pop ( $tokens_arr );
    if (isset ( $tokens_arr [0] [1] ) && $tokens_arr [0] [1] == "spaces") {
        array_shift ( $tokens_arr );
        reset ( $tokens_arr );
    }
    return $tokens_arr;
}
function convert_spaces_to_concat_operators($tokens_arr) {
    $tokens_arr = tokens_arr_trim ( $tokens_arr );
    // $spaces_poses = find_all_token_at_level0_by_type ( "spaces", $tokens_arr );
    $return = [ ];
    foreach ( $tokens_arr as $pos => $token ) {
        $type = isset ( $token ["type"] ) ? $token ["type"] : $token [1];
        if ($type !== "spaces") {
            array_push ( $return, $token );
            continue;
        }
        $name = isset ( $token ["name"] ) ? $token ["name"] : $token [0];
        $spaces_pos = $pos;
        $before = $tokens_arr [$spaces_pos - 1];
        $after = $tokens_arr [$spaces_pos + 1];
        $before_type = isset ( $before ["type"] ) ? $before ["type"] : $before [1];
        $after_type = isset ( $after ["type"] ) ? $after ["type"] : $after [1];
        $concat_types = [ "var","number","string"
        ];
        if (in_array ( $before_type, $concat_types ) && in_array ( $after_type, $concat_types )) {
            $return [] = [ ".","operator"
            ];
            $return [] = [ $name,"string"
            ];
            $return [] = [ ".","operator"
            ];
        }
    }
    return $return;
}
function token_to_name_and_type_format($token) {
    // if (! is_token ( $token ))
    // return $token;
    return [ "name" => $token [0],"type" => $token [1]
    ];
}
function tokens_arr_to_name_and_type_format($tokens_arr) {
    $return = [ ];
    foreach ( $tokens_arr as $k => $token ) {
        if (is_array ( $token )) {
            if (is_token ( $token ))
                $return [$k] = token_to_name_and_type_format ( $token );
            else
                $return [$k] = tokens_arr_to_name_and_type_format ( $token );
        } else
            $return [$k] = $token;
    }
    return $return;
}
function replace_get_ele_with_complex_value($expr) {
    if (! is_array ( $expr ) || ! isset ( $expr ["type"] ) || $expr ["type"] != "expr")
        return $expr;
    if (isset ( $expr ["body"] [1] ) && $expr ["body"] [1] == [ "name" => "get_ele","type" => "operator"
    ] && sizeof ( $expr ["body"] ) == 3) {
        $expr = [ "type" => "complex_value","name" => $expr ["body"] [0],"key" => $expr ["body"] [2]
        ];
    }
    foreach ( $expr as $k => $v ) {
        $expr [$k] = replace_get_ele_with_complex_value ( $v );
    }
    return $expr;
}
// function expr_simplifier($expr) {
// $value_types = [ "string","number","expr","var","array","complex_value"
// ];
// if (sizeof ( $expr ["body"] ) == 1 && in_array ( $expr ["body"] [0] ["type"], $value_types )) {
// return $expr ["body"] [0];
// }
// if (isset ( $expr ["body"] ["type"] ) && in_array ( $expr ["body"] ["type"], $value_types )) {
// return $expr ["body"];
// }
// return $expr;
// }
function parse_expression($tokens_arr) {
    // $return = [ ];
    $value_types = [ "string","number","expr","var","array","complex_value"
    ];
    if (isset ( $tokens_arr ["type"] ) && $tokens_arr ["type"] == "expr")
        $tokens_arr = $tokens_arr ["data"];
    $complex_expr_arr = [ ];
    reset ( $tokens_arr );
    $tokens_arr = tokens_arr_trim1 ( $tokens_arr );
    if (sizeof ( $tokens_arr ) == 1)
        if (is_token ( get_1st_ele ( $tokens_arr ) )) {
            if (in_array ( get_1st_ele ( $tokens_arr ) [1], [ "string","number","var","class","array","expr","complex_value"
            ] )) {
                $first_token = reset ( $tokens_arr );
                return [ "type" => $first_token [1],"name" => $first_token [0]
                ];
            }
        } else {
            return get_1st_ele ( $tokens_arr );
        } // if (! in_array ( [ "(","operator"
          // ], $tokens_arr ) && ! in_array ( [ "[","operator"
          // ], $tokens_arr ) && ! in_array_lv2_1 ( "function", $tokens_arr ) &&
          // ! in_array ( [ ":","operator"
          // ], $tokens_arr ) && ! in_array ( [ "new","keyword"
          // ], $tokens_arr ))
          // return [ "type" => "expr","body" => $tokens_arr
          // ];
    $is_complex_expr = false;
    $tmp = [ ];
    // brackets expr
    while ( true ) {
        for($k = 1; $k < sizeof ( $tokens_arr ); $k ++) {
            $token = $tokens_arr [$k];
            $previous_token_type = isset ( $tokens_arr [$k - 1] [1] ) ? $tokens_arr [$k - 1] [1] : $tokens_arr [$k - 1] ["type"];
            if (in_array ( $previous_token_type, $value_types ) && $token == [ "[","operator"
            ]) {
                $result_arr = get_arr_between_brackets ( "[", $tokens_arr, $k );
                $tmp = array_slice ( $tokens_arr, 0, $k );
                $tmp [] = [ "get_ele","operator"
                ];
                $tmp [] = parse_expression ( $result_arr );
                $tmp = array_merge ( $tmp, array_slice ( $tokens_arr, $k + sizeof ( $result_arr ) + 2 ) );
                $tokens_arr = $tmp;
                continue;
            }
        }
        break;
    }
    for($k = 0; $k < sizeof ( $tokens_arr ); $k ++) {
        $token = $tokens_arr [$k];
        if (isset ( $token ["type"] )) {
            $complex_expr_arr [] = $token;
            continue;
        }
        if ($token [0] == "(" && $token [1] == "operator") {
            $is_complex_expr = true;
            $result_arr = get_arr_between_brackets ( "(", $tokens_arr, $k );
            $complex_expr_arr [] = parse_expression ( $result_arr );
            $k += sizeof ( $result_arr ) + 1;
        } elseif ($token [0] == "[" && $token [1] == "operator") {
            $is_complex_expr = true;
            $result_arr = get_arr_between_brackets ( "[", $tokens_arr, $k );
            $complex_expr_arr [] = parse_array ( $result_arr );
            $k += sizeof ( $result_arr ) + 1;
        } elseif ($token [0] == ")" && $token [1] == "operator") {
            throw new Exception ( "not valid close round bracket" );
        } elseif ($token [0] == "]" && $token [1] == "operator") {
            throw new Exception ( "not valid close square bracket" );
        } else {
            $complex_expr_arr [] = $token;
        }
    }
    if ($is_complex_expr) {
        $parsed = parse_expression ( $complex_expr_arr );
        if (in_array ( $parsed ["type"], $value_types ))
            return ($parsed);
        else
            return ([ "type" => "expr","body" => [ $parsed
            ]
            ]);
    }
    // assignment expr
    foreach ( $tokens_arr as $k => $token ) {
        if ($token == [ ":","operator"
        ]) {
            return (process_assignment ( $tokens_arr ));
        }
    }
    // simple function
    $function_and_params = get_function_and_params_lv0 ( $tokens_arr );
    if ($function_and_params && isset ( $function_and_params ["name"] )) {
        $function_name = $function_and_params ["name"];
        $function_args = $function_and_params ["params"];
        return ([ "type" => "function","name" => $function_name,"args" => $function_args
        ]);
    }
    // multiple operator but no brackets expr
    $tokens_arr = convert_spaces_to_concat_operators ( $tokens_arr );
    $operators_count = 0;
    foreach ( $tokens_arr as $token ) {
        if (is_token ( $token ) && $token [1] == "operator")
            $operators_count ++;
    }
    if ($operators_count > 1) {
        return (replace_get_ele_with_complex_value ( process_multiple_operators ( $tokens_arr ) ));
    }
    // new object of class
    if (is_new_clause ( $tokens_arr )) {
        $tokens_arr1 = $tokens_arr;
        foreach ( $tokens_arr1 as $j => $token ) {
            if ($token == [ "new","keyword"
            ])
                $tokens_arr1 [$j] = [ "@new","function"
                ];
        }
        $return = get_function_and_params_lv0 ( $tokens_arr1 );
        $classes = [ ];
        foreach ( $return ["params"] as $l => $param ) {
            $type = isset ( $param ["type"] ) ? $param ["type"] : $param [1];
            if ($type == "class") {
                $name = isset ( $param ["name"] ) ? $param ["name"] : $param [0];
                $classes [] = $name;
                unset ( $return ["params"] [$l] );
            }
        }
        if (sizeof ( $classes ) != 1)
            throw new Exception ( "new but no/multi class" );
        reset ( $return ["params"] );
        return ([ "type" => "new","class" => $classes [0],"params" => $return ["params"]
        ]);
    } // simple single operator expr
    $tokens_arr = tokens_arr_to_name_and_type_format ( $tokens_arr );
    $tokens_arr = replace_get_ele_with_complex_value ( $tokens_arr );
    if (sizeof ( $tokens_arr ) == 1) {
        return $tokens_arr [0];
    } else
        return [ "type" => "expr","body" => $tokens_arr
        ];
}
function process_if_sl($line_num, $tokens_arr) {
    $if_line = $tokens_arr [$line_num] [0];
    array_shift ( $if_line );
    $if_line = array_values ( $if_line );
    // $poses_of_2_dots = array_search_multi ( [
    // ":",
    // "operator"
    // ], $if_line );
    // if (! $poses_of_2_dots || sizeof ( $poses_of_2_dots ) > 1)
    // throw new Exception ( "invalid sl if" );
    // $pos_of_2_dots = $poses_of_2_dots [0];
    $poses_of_else = array_search_multi ( [ "else","keyword"
    ], $if_line );
    if (sizeof ( $poses_of_else ) > 1)
        throw new Exception ( "invalid sl if-else" );
    if (sizeof ( $poses_of_else ) == 0)
        $pos_of_else = sizeof ( $if_line );
    else
        $pos_of_else = $poses_of_else [0];
    // $line_num++;
    return [ "type" => "if","condition" => parse_expression ( array_slice ( $if_line, 0, $pos_of_2_dots ) ),"exec_if" => structurelize ( array_slice ( $if_line, $pos_of_2_dots, $pos_of_else - $pos_of_2_dots ) ),"exec_else" => structurelize ( array_slice ( $if_line, $pos_of_else ) )
    ];
}
function process_if_ml(&$line_num, $tokens_arr) {
    $return = [ "type" => "ifs","ifs" => [ ],"else" => [ ]
    ];
    $if_blocks = [ ];
    $if_conditions = [ ];

    $if_line = $tokens_arr [$line_num] [0];
    $getline_subblock = getline_subblock ( $tokens_arr, $line_num );
    $line_num += $getline_subblock [0];
    $if_blocks [] = $getline_subblock [1];
    $if_conditions [] = parse_expression ( array_slice ( $if_line, 1 ) );
    while ( true ) {
        if (isset ( $tokens_arr [$line_num + 1] [0] ) && $tokens_arr [$line_num + 1] [0] [0] == [ "elseif","keyword"
        ]) {
            $line_num ++;

            $if_line = $tokens_arr [$line_num] [0];
            $getline_subblock = getline_subblock ( $tokens_arr, $line_num );
            $line_num += $getline_subblock [0];
            $if_blocks [] = $getline_subblock [1];
            $if_conditions [] = parse_expression ( array_slice ( $if_line, 1 ) );
        } else {
            break;
        }
    }
    if (isset ( $tokens_arr [$line_num + 1] [0] ) && $tokens_arr [$line_num + 1] [0] == [ [ "else","keyword"
    ]
    ]) {
        $line_num ++;

        $getline_subblock = getline_subblock ( $tokens_arr, $line_num );
        $line_num += $getline_subblock [0];
        $else_block = $getline_subblock [1];
    } else
        $else_block = [ ];
    foreach ( $if_blocks as $i => $if_block ) {
        $return ["ifs"] [$i] = [ "condition" => $if_conditions [$i],"body" => structurelize ( $if_block )
        ];
    }
    $return ["else"] = structurelize ( $else_block );
    return $return;
}
function process_if(&$line_num, $tokens_arr) {
    // $if_line = $tokens_arr [$line_num];
    // if (get_last_ele ( $if_line [0] ) [0] == ":" && get_last_ele ( $if_line [0] ) [1] == "operator")
    return process_if_ml ( $line_num, $tokens_arr );
    // else
    // process_if_sl ( $line_num, $tokens_arr );
    // throw new Exception ( "invalid if line" );
}
function for_parse($for_tokens) {
    echo "";
    if ($for_tokens [2] [1] == "number" && isset ( $for_tokens [4] [1] ) && $for_tokens [4] [1] == "var" && array_search ( [ ",","operator"
    ], $for_tokens ) === false)
        return [ tokenize ( $for_tokens [4] [0] . ":1" ) [0] [0],tokenize ( $for_tokens [4] [0] . "<=" . $for_tokens [2] [0] ) [0] [0],tokenize ( $for_tokens [4] [0] . "++" ) [0] [0]
        ];
    else if (isset ( $for_tokens [4] [1] ) && $for_tokens [2] [1] == "number" && $for_tokens [4] [0] == "times" && $for_tokens [4] [1] == "keyword")
        return [ tokenize ( '$_time:1' ) [0] [0],tokenize ( '$_time<=' . $for_tokens [2] [0] ) [0] [0],tokenize ( '$_time++' ) [0] [0]
        ];
    array_shift ( $for_tokens );
    $return = explode_tokens ( [ ",","operator"
    ], $for_tokens );
    if (sizeof ( $return ) != 3)
        throw new Exception ( "invalid for line" );
    return $return;
}
function foreach_parse($foreach_tokens) {
    array_shift ( $foreach_tokens );
    $return = explode_tokens ( ",", $foreach_tokens );
    if (sizeof ( $return ) == 3)
        return $return;
    if (sizeof ( $return ) == 2)
        return [ $return [0],[ "\$_k","var'"
        ],$return [1]
        ];
    if (sizeof ( $return ) == 1)
        return [ $return [0],[ "\$_k","var'"
        ],[ "\$_v","var'"
        ]
        ];
}
// function process_for_sl($line_num, $tokens_arr) {
// $for_line = $tokens_arr [$line_num] [0];
// array_shift ( $for_line );
// $for_line = array_values ( $for_line );
// $poses_of_2_dots = array_search_multi ( [
// ":",
// "operator"
// ], $tokens_arr [$line_num] [0] );
// if (! $poses_of_2_dots || sizeof ( $poses_of_2_dots ) > 1)
// throw new Exception ( "invalid sl for" );
// $pos_of_2_dots = $poses_of_2_dots [0];
// // $line_num++;
// $for_parse = for_parse ( array_slice ( $for_line, 1, $pos_of_2_dots ) );
// return [
// "type" => "for",
// "init" => parse_expression ( $for_parse [0] ),
// "increment" => parse_expression ( $for_parse [1] ),
// "terminate" => parse_expression ( $for_parse [2] ),
// "exec" => structurelize ( array_slice ( $for_line, $pos_of_2_dots ) )
// ];
// }
function process_for_ml(&$line_num, $tokens_arr) {
    $for_line = $tokens_arr [$line_num] [0];
    $getline_subblock = getline_subblock ( $tokens_arr, $line_num );
    $line_num += $getline_subblock [0];
    $for_block = $getline_subblock [1];
    $for_parse = for_parse ( $for_line );
    $return = [ "type" => "for","init" => parse_expression ( $for_parse [0] ),"increment" => parse_expression ( $for_parse [2] ),"terminate" => parse_expression ( $for_parse [1] ),"body" => structurelize ( $for_block )
    ];
    return $return;
}
function process_for(&$line_num, $tokens_arr) {
    // $if_line = $tokens_arr [$line_num];
    // if (get_last_ele ( $if_line [0] ) [0] == ":" && get_last_ele ( $if_line [0] ) [1] == "operator")
    return process_for_ml ( $line_num, $tokens_arr );
    // else
    // process_for_sl ( $line_num, $tokens_arr );
}
// function process_while_sl($line_num, $tokens_arr) {
// $while_line = $tokens_arr [$line_num] [0];
// array_shift ( $while_line );
// $while_line = array_values ( $while_line );
// // $poses_of_2_dots = array_search_multi ( [
// // ":",
// // "operator"
// // ], $while_line );
// // if (! $poses_of_2_dots || sizeof ( $poses_of_2_dots ) > 1)
// // throw new Exception ( "invalid sl if" );
// // $pos_of_2_dots = $poses_of_2_dots [0];
// // $line_num++;
// // $for_parse = for_parse ( array_slice ( $for_line, 1, $pos_of_2_dots ) );
// return [
// "type" => "while",
// "condition" => parse_expression ( $while_line, 1 ),
// "exec" => structurelize ( $while_line )
// ];
// }
function process_while_ml(&$line_num, $tokens_arr) {
    $while_line = $tokens_arr [$line_num] [0];
    // $while_lv = $tokens_arr [$line_num] [1];
    $while_block = [ ];
    // $poses_of_2_dots = array_search_multi ( [
    // ":",
    // "operator"
    // ], $while_line );
    // if (! $poses_of_2_dots || sizeof ( $poses_of_2_dots ) > 1)
    // throw new Exception ( "invalid sl while" );
    // $pos_of_2_dots = $poses_of_2_dots [0];
    $getline_subblock = getline_subblock ( $tokens_arr, $line_num );
    $line_num += $getline_subblock [0];
    $while_block = $getline_subblock [1];
    // $for_parse = for_parse ( array_slice ( $while_line, 0, sizeof ( $while_line ) ) );
    return [ "type" => "while","condition" => parse_expression ( array_slice ( $while_line, 1 ) ),"body" => structurelize ( $while_block )
    ];
}
function process_while(&$line_num, $tokens_arr) {
    // $if_line = $tokens_arr [$line_num];
    // if (get_last_ele ( $if_line [0] ) [0] == ":" && get_last_ele ( $if_line [0] ) [1] == "operator")
    return process_while_ml ( $line_num, $tokens_arr );
    // else
    // process_while_sl ( $line_num, $tokens_arr );
}
function is_function_def(array $tokens) {
    if ($tokens [sizeof ( $tokens ) - 1] [0] != ":" || $tokens [sizeof ( $tokens ) - 1] [1] != "operator")
        return false;
    $check = false;
    // /========================
    foreach ( $tokens as $token )
        if ($token [1] == "function") {
            $check = true;
            break;
        }
    if ($check == false)
        return false;
    // =========================
    array_pop ( $tokens );
    $function_name = "";
    $function_vars = [ [ ]
    ];
    $function_vars_i = 0;
    for($i = 0; $i < count ( $tokens ); $i ++) {
        $token = $tokens [$i];
        if ($token [1] == "function")
            if ($function_name == "") {
                $function_name = $token [0];
                $function_vars_i > 0 && $function_vars_i ++;
            } else
                throw new Exception ( "function declare has multiple name" );
        // else if ($token [1] != "var" && $token [1] != "spaces" && $token [0] != ",")
        // throw new Exception ( "non-var not allowed function declare" );
        else if ($token [1] == "var") {
            $function_vars [$function_vars_i] [] = $token;
        } else if ($token [0] == "[" && $token [1] == "operator") {
            $arr_between_brackets = get_arr_between_brackets ( "[", $tokens, $i );
            array_push ( $function_vars [$function_vars_i], parse_expression ( $arr_between_brackets ) );
            $i += sizeof ( $arr_between_brackets );
        } else if ($token [0] == "(" && $token [1] == "operator") {
            $arr_between_brackets = get_arr_between_brackets ( "(", $tokens, $i );
            array_push ( $function_vars [$function_vars_i], parse_expression ( $arr_between_brackets ) );
            $i += sizeof ( $arr_between_brackets );
        } else if ($token [0] == "," && $token [1] == "operator") {
            $function_vars_i ++;
            $function_vars [$function_vars_i] = [ ];
        } else {
            // $function_vars_i ++;
            $function_vars [$function_vars_i] [] = $token;
        }
    }
    if ($function_vars == [ [ ]
    ])
        $function_vars = [ ];
    // validate function_vars
    foreach ( $function_vars as $k => $vars ) {
        $function_vars [$k] = remove_spaces_from_tokens_arr ( $vars );
        $vars = $function_vars [$k];
        if (sizeof ( $vars ) == 2)
            throw new Exception ( "function declare invalid arg" );
        if ($vars [0] [1] != "var")
            throw new Exception ( "function declare no var" );
        if (sizeof ( $vars ) >= 3 && $vars [1] != [ ":","operator"
        ])
            throw new Exception ( "function var declare no: " . $k );
        $value_tokens = array_slice ( $vars, 2 );
        $value_expr = parse_expression ( $value_tokens );
        $function_vars [$k] = [ 0 => $vars [0] [0]
        ];
        if (sizeof ( $vars ) >= 3)
            $function_vars [$k] [1] = $value_expr;
    }
    return [ $function_name,$function_vars
    ];
}
function is_list(array $tokens, $types, $delimiter = [ ",","operator"
]) {
    $return = [ ];
    if (! is_array ( $types ))
        $types = [ $types
        ];
    $is_delimiter = true;
    foreach ( $tokens as $token ) {
        $is_delimiter = ! $is_delimiter;
        if ($is_delimiter) {
            if ($token != $delimiter) {
                return false;
            }
        } else {
            if (! in_array ( $token [1], $types ))
                return false;
            array_push ( $return, $token );
        }
    }
    return $return;
}
function get_each_element_from_array($arr, $num) {
    $return = [ ];
    foreach ( $arr as $k => $sub_array ) {
        if (isset ( $sub_array [$num] ))
            $return [$k] = $sub_array [$num];
    }
    return $return;
}
function is_class_def(array $tokens) {
    $return = [ "class" => "","extends" => [ ]
    ];
    $tokens = remove_all_spaces ( $tokens );
    $last_token_i = sizeof ( $tokens ) - 1;
    if (! isset ( $tokens [0] ) || $tokens [0] [1] != "class")
        return false;
    if ($tokens [$last_token_i] [0] != ":" || $tokens [$last_token_i] [1] != "operator")
        return false;
    // /========================
    if (sizeof ( $tokens ) == 2) {
        $return ["class"] = $tokens [0] [0];
        return $return;
    } elseif (sizeof ( $tokens ) < 2 || sizeof ( $tokens ) == 3) {
        return false;
    } elseif ($tokens [1] != [ "<<","operator"
    ]) {
        return false;
    } elseif (! ($list = is_list ( array_slice ( $tokens, 2, $last_token_i - 2 ), "class" ))) {
        return false;
    } else {
        $return ["class"] = $tokens [0] [0];
        $return ["extends"] = get_each_element_from_array ( $list, 0 );
        return $return;
    }
    return false;
}
function process_function_def(&$line_num, $tokens_arr, $func_declare, $is_method = false) {
    $getline_subblock = getline_subblock ( $tokens_arr, $line_num );
    $line_num += $getline_subblock [0];
    $func_def_block = $getline_subblock [1];
    if (! $is_method) {
        $GLOBALS ["funcs"] [] = [ "type" => "func_def","name" => $func_declare [0],"vars" => $func_declare [1],"body" => structurelize ( $func_def_block )
        ];
        return [ ];
    } else
        return [ "type" => "func_def","name" => $func_declare [0],"vars" => $func_declare [1],"body" => structurelize ( $func_def_block )
        ];
}
function process_class_def(&$line_num, $tokens_arr, $class_name) {
    $getline_subblock = getline_subblock ( $tokens_arr, $line_num );
    $line_num += $getline_subblock [0];
    $class_def_block = $getline_subblock [1];
    $GLOBALS ["classes"] [] = [ "type" => "class_def","name" => $class_name ["class"],"extends" => $class_name ["extends"],"body" => parse_class ( $class_def_block )
    ];
    return [ ];
}
function parse_property($tokens_arr) {
    try {
        $tokens_arr = remove_all_spaces ( $tokens_arr );
        $modifiers = [ ];
        $start_vars_section = - 1;
        $return = [ ];
        foreach ( $tokens_arr as $i => $token ) {
            if ($token [1] == "modifier") {
                array_push ( $modifiers, $token [0] );
            } else {
                $start_vars_section = $i;
                break;
            }
        }
        if (! $modifiers)
            $modifiers = [ "public"
            ];
        if ($start_vars_section == - 1)
            throw new Exception ( "non-var in property" );
        $vars_section = array_slice ( $tokens_arr, $start_vars_section );
        $all_colons = find_all_token_at_level0 ( [ ":","operator"
        ], $vars_section );
        if (sizeof ( $all_colons ) > 1) {
            throw new Exception ( "too many colons in property" );
        } elseif (sizeof ( $all_colons ) == 1) {
            $two_sides = array_cut_by_pos ( $vars_section, $all_colons );
            if (sizeof ( find_all_token_at_level0 ( [ ",","operator"
            ], $two_sides [0] ) ) != sizeof ( find_all_token_at_level0 ( [ ",","operator"
            ], $two_sides [1] ) ))
                throw new Exception ( "colons count not balance in property" );
            // =================================// foreach ( $two_sides [0] as $token ) {
            // if ($token [1] != "var" && $token != [ ",","operator"
            // ])
            // throw new Exception ( "not valid property declare" );
            // }
            // $commas_pos = find_all_token_at_level0 ( [ ",","operator"
            // ], $two_sides [0] );
            // $vars_arr = array_cut_by_pos ( $two_sides [0], $commas_pos );
            // foreach ( $vars_arr as $vars ) {
            // if (sizeof ( $vars ) != 1)
            // throw new Exception ( "not valid property declare1" );
            // $return = [ "type" => "property","name" => $vars [0] [0],"modifiers" => $modifiers
            // ];
            // }
            if ($list_vars = is_list ( $two_sides [0], "var" ))
                foreach ( $list_vars as $var )
                    $return [] = [ "type" => "property","name" => $var [0],"modifiers" => $modifiers
                    ];
            else
                throw new Exception ( "not valid property declare" );
            // =================================
            // foreach ( $two_sides [1] as $token ) {
            // if ($token [1] != "var" || $token != [ ",","operator"
            // ])
            // throw new Exception ( "not valid property declare" );
            // }
            $commas_pos = find_all_token_at_level0 ( [ ",","operator"
            ], $two_sides [1] );
            $values_arr = array_cut_by_pos ( $two_sides [1], $commas_pos );
            foreach ( $values_arr as $i => $values_tokens ) {
                // if (sizeof ( $values ) != 1)
                // throw new Exception ( "not valid property declare1" );
                $return [$i] ["value"] = parse_expression ( $values_tokens );
                // }
                // =================================
            }
            return $return;
        } else {
            if ($list_vars = is_list ( $vars_section, "var" )) {
                foreach ( $list_vars as $var ) {
                    $return [] = [ "type" => "property","name" => $var [0],"modifiers" => $modifiers
                    ];
                }
            } else
                throw new Exception ( "not valid property" );
            // foreach ( $list_vars as $var ) {
            // // if (sizeof ( $vars ) != 1)
            // // throw new Exception ( "not valid property declare1" );
            // $return [] = [ "type" => "property","name" => $var [0],"modifiers" => $modifiers
            // ];
            // }
            return $return;
        }
    } catch ( Exception $e ) {
        return false;
    }
}
function is_method_def($tokens_arr) {
    $modifiers = [ ];
    $start_func_section = - 1;
    $return = [ ];
    foreach ( $tokens_arr as $i => $token ) {
        if ($token [1] == "modifier") {
            array_push ( $modifiers, $token [0] );
        } else {
            $start_func_section = $i;
            break;
        }
    }
    if ($start_func_section == - 1)
        throw new Exception ( "non-function in method" );
    $func_section = array_slice ( $tokens_arr, $start_func_section );
    $return = is_function_def ( $func_section );
    if (! $return)
        return false;
    $return [2] = $modifiers ? $modifiers : [ "public"
    ];
    return $return;
}
function parse_class($class_def_block) {
    $return = [ ];
    $class_def_block = array_values ( $class_def_block );
    for($i = 0; $i < sizeof ( $class_def_block ); $i ++) {
        $line = $class_def_block [$i];
        if ($result = parse_property ( $line [0] )) {
            foreach ( $result as $property )
                $return [] = $property;
        } elseif ($result = is_method_def ( $line [0] )) {
            $result1 = process_function_def ( $i, $class_def_block, $result, true );
            $result1 ["type"] = "method";
            $result1 ["modifiers"] = $result [2];
            $return [] = $result1;
        } else
            throw new Exception ( "wrong class internal" );
    }
    return [ "type" => "block","body" => $return
    ];
}
function is_catch_line($line, $tokens_arr) {
    $line_tokens = remove_spaces_from_tokens_arr ( $tokens_arr [$line] [0] );
    if ($line_tokens [0] == [ "catch","keyword"
    ]) {
        if (sizeof ( $line_tokens ) == 1)
            return [ "catch","^Exception","\$_e"
            ];
        elseif (sizeof ( $line_tokens ) == 2 && $line_tokens [1] [1] == "class")
            return [ "catch",$line_tokens [1] [0],"\$_e"
            ];
        elseif (sizeof ( $line_tokens ) == 3 && $line_tokens [1] [1] == "class" && $line_tokens [2] [1] == "var")
            return [ "catch",$line_tokens [1] [0],$line_tokens [2] [0]
            ];
        else
            throw new Exception ( "invalid catch" );
    }
    return false;
}
function is_finally_line($line, $tokens_arr) {
    $line_tokens = remove_spaces_from_tokens_arr ( $tokens_arr [$line] [0] );
    if ($line_tokens [0] == [ "finally","keyword"
    ]) {
        if (sizeof ( $line_tokens ) == 1)
            return true;
        else
            throw new Exception ( "invalid finally" );
    }
    return false;
}
function remove_all_spaces(Array $tokens_arr) {
    $return = [ ];
    foreach ( $tokens_arr as $ele ) {
        // if (! is_token ( $tokens_arr ))
        // continue;
        if (! is_token ( $ele ) || $ele [1] != "spaces")
            array_push ( $return, $ele );
    }
    return $return;
}
function process_try(&$line, $tokens_arr) {
    $tabs_lv = $tokens_arr [$line] [1];
    $blocks_type = [ [ "try"
    ]
    ];
    $block_num = 0;
    $blocks = [ [ ]
    ];
    for($i = $line + 1; $i < sizeof ( $tokens_arr ); $i ++) {
        if ($tokens_arr [$i] [1] < $tabs_lv)
            break;
        if ($tokens_arr [$i] [1] == $tabs_lv && ($catch_line_parsed = is_catch_line ( $i, $tokens_arr ))) {
            // $catch_line = remove_all_spaces ( $tokens_arr [$i] [0] );
            $block_num ++;
            array_push ( $blocks_type, $catch_line_parsed );
        } elseif ($tokens_arr [$i] [1] == $tabs_lv && is_finally_line ( $i, $tokens_arr )) {
            $block_num ++;
            $blocks [$block_num] = [ ];
            array_push ( $blocks_type, [ "finally"
            ] );
        } elseif ($tokens_arr [$i] [1] == $tabs_lv) {
            break;
        } elseif ($tokens_arr [$i] [1] > $tabs_lv) {
            if (! isset ( $blocks [$block_num] ))
                $blocks [$block_num] = [ ];
            array_push ( $blocks [$block_num], $tokens_arr [$i] );
        }
    }
    $line = $i - 1;
    $catches = [ ];
    $try = [ ];
    $try_count = 0;
    $finally = [ ];
    $finally_count = 0;
    for($i = 0; $i < sizeof ( $blocks_type ); $i ++) {
        if ($blocks_type [$i] [0] == "catch")
            array_push ( $catches, [ "exception_class" => $blocks_type [$i] [1],"catch_var" => $blocks_type [$i] [2],"body" => structurelize ( $blocks [$i] )
            ] );
        elseif ($blocks_type [$i] [0] == "try") {
            $try_count ++;
            if ($try_count > 1)
                throw new Exception ( "multiple try blocks" );
            $try = structurelize ( $blocks [$i] );
        } elseif ($blocks_type [$i] [0] == "finally") {
            $finally_count ++;
            if ($finally_count > 1)
                throw new Exception ( "multiple try blocks" );
            $finally = structurelize ( $blocks [$i] );
        }
    }
    if (sizeof ( $catches ) < 1)
        throw new Exception ( "try but no catch" );
    return [ "type" => "try","body" => $try,"catch" => $catches,"finally" => $finally
    ];
}
function process_defcat(&$line, $tokens_arr) {
    $tabs_lv = $tokens_arr [$line] [1];
    $defcat = $deffin = [ ];
    $is_in_deffin = false;
    for($i = $line + 1; $i < sizeof ( $tokens_arr ); $i ++) {
        if ($tokens_arr [$i] [1] < $tabs_lv) {
            break;
        } elseif ($tokens_arr [$i] [1] == $tabs_lv && $tokens_arr [$i] [0] [0] == "deffin") {
            $is_in_deffin = true;
        } elseif ($tokens_arr [$i] [1] == $tabs_lv && $tokens_arr [$i] [0] [0] != "deffin") {
            break;
        } elseif ($tokens_arr [$i] [1] > $tabs_lv) {
            if ($is_in_deffin)
                array_push ( $deffin, $tokens_arr [$i] );
            else
                array_push ( $defcat, $tokens_arr [$i] );
        }
    }
    if (! $defcat)
        return false;
    $line = $i;
    $defcat_blocks = [ ];
    $defcat_blocks_i = - 1;
    $defcat_exception_classes = [ ];
    $defcat = rtrim_all_lines_arr ( $defcat );
    for($i = 0; $i < sizeof ( $defcat ); $i ++) {
        if ($defcat [$i] [1] == ($tabs_lv + 1))
            if (sizeof ( $defcat [0] [0] ) == 1 && $defcat [0] [0] [0] [1] == "class") {
                array_push ( $defcat_exception_classes, $defcat [$i] [0] [0] [0] );
                $defcat_blocks_i ++;
            } else
                throw new Exception ( "Invalid defcat exception class" );
        elseif ($defcat [$i] [1] > ($tabs_lv + 1)) {
            if (! isset ( $defcat_blocks [$defcat_blocks_i] ))
                $defcat_blocks [$defcat_blocks_i] = [ ];
            array_push ( $defcat_blocks [$defcat_blocks_i], $defcat [$i] );
        }
    }
    if (sizeof ( array_unique ( $defcat_exception_classes ) ) != sizeof ( $defcat_exception_classes ))
        throw new Exception ( "Non unique catch in defcat" );
    $defcat_rs = [ ];
    foreach ( $defcat_blocks as $i => $defcat_block ) {
        $defcat_blocks [$i] = structurelize ( $defcat_block );
        array_push ( $defcat_rs, [ "exception_class" => $defcat_exception_classes [$i],"catch_var" => '$_e',"body" => $defcat_blocks [$i]
        ] );
    }
    return [ "type" => "defcat","body" => $defcat_rs,"deffin" => structurelize ( $deffin )
    ];
}
function structurelize($tokens_arr) {
    $return = [ ];
    $defcat_rs = [ ];
    // $tokens_arr = remove_blank_lines ( $tokens_arr );
    for($line = 0; $line < sizeof ( $tokens_arr ); $line ++) {
        $tokens_n_lv = $tokens_arr [$line];
        $tokens = ltrim_arr ( $tokens_n_lv [0] );
        // $block_lv = $tokens_n_lv [1];
        if ($tokens [0] [0] == "if" && $tokens [0] [1] == "keyword") {
            $line_rs = process_if ( $line, $tokens_arr );
        } elseif ($tokens [0] [0] == "for" && $tokens [0] [1] == "keyword") {
            $line_rs = process_for ( $line, $tokens_arr );
        } elseif ($tokens [0] [0] == "while" && $tokens [0] [1] == "keyword") {
            $line_rs = process_while ( $line, $tokens_arr );
        } elseif ($func_declare = is_function_def ( $tokens )) {
            $line_rs = process_function_def ( $line, $tokens_arr, $func_declare );
        } elseif ($class_name = is_class_def ( $tokens )) {
            $line_rs = process_class_def ( $line, $tokens_arr, $class_name );
        } elseif ($tokens [0] [0] == "try" && $tokens [0] [1] == "keyword") {
            $line_rs = process_try ( $line, $tokens_arr );
        } elseif ($tokens [0] [0] == "defcat" && $tokens [0] [1] == "keyword") {
            $defcat_rs = process_defcat ( $line, $tokens_arr );
            continue;
        } else
            $line_rs = parse_expression ( $tokens_n_lv [0] );
        array_push ( $return, $line_rs );
    }
    if ($defcat_rs)
        return [ "type" => "block","body" => [ [ "type" => "try","body" => [ "type" => "block","body" => $return
        ],"catch" => $defcat_rs ["body"],"finally" => $defcat_rs ["deffin"]
        ]
        ]
        ];
    return [ "type" => "block","body" => $return
    ];
}
function process_string(string $str) {
    $arr = str_split ( $str );
    $return = [ ];
    for($k = 0; $k < sizeof ( $arr ); $k ++) {
        $v = $arr [$k];
        if ($v == "\\" && isset ( $arr [$k + 1] )) {
            array_push ( $return, $arr [$k + 1] );
            $k ++;
        } else {
            array_push ( $return, $v );
        }
    }
    return implode ( "", $return );
}
function merge_strings($tokens_arr) {
    $return = [ ];
    foreach ( $tokens_arr as $k => $tokens_line ) {
        $return [$k] = [ [ ],$tokens_arr [$k] [1]
        ];
        $current_str = "";
        foreach ( $tokens_line [0] as $k1 => $token ) {
            if ($token [1] == "string") {
                $current_str .= $token [0];
                continue;
            } elseif ($token [1] == "spaces") {
                if ($current_str) {
                    $current_str .= $token [0];
                    continue;
                } else {
                    array_push ( $return [$k] [0], $token );
                }
            } else {
                if ($current_str) {
                    array_push ( $return [$k] [0], [ trim ( $current_str ),"string"
                    ] );
                    if ($tokens_line [0] [$k1 - 1] [1] == "spaces")
                        array_push ( $return [$k] [0], $tokens_line [0] [$k1 - 1] );
                }
                $current_str = "";
                array_push ( $return [$k] [0], $token );
            }
        }
        if ($current_str) {
            array_push ( $return [$k] [0], [ trim ( $current_str ),"string"
            ] );
            if ($tokens_line [0] [sizeof ( $tokens_line [0] ) - 1] [1] == "spaces")
                array_push ( $return [$k] [0], $tokens_line [0] [sizeof ( $tokens_line [0] ) - 1] );
        }
    }
    foreach ( $return as $k => $tokens_line ) {
        foreach ( $tokens_line [0] as $k1 => $token ) {
            if ($token [1] == "string")
                $return [$k] [0] [$k1] [0] = process_string ( $return [$k] [0] [$k1] [0] );
        }
    }
    return $return;
}
function array_merge_unique(&$arr, $arr1) {
    foreach ( $arr1 as $ele1 ) {
        if (! in_array ( $ele1, $arr ))
            array_push ( $arr, $ele1 );
    }
}
function find_all_unique_types_non_block_in_body($type, $input) {
    if (! isset ( $input ["body"] )) {
        if (isset ( $input ["name"] ) && $input ["type"] == $type)
            return [ $input
            ];
        else
            return [ ];
    } else {
        $return = [ ];
        foreach ( $input ["body"] as $clause ) {
            array_merge_unique ( $return, find_all_unique_types_non_block_in_body ( $type, $clause ) );
        }
        return $return;
    }
}
function find_an_obj_in_body($obj, $input) {
    if (isset ( $input ["name"] ) && isset ( $input ["type"] ) && $input ["name"] == $obj ["name"] && $input ["type"] == $obj ["type"]) {
        return true;
    } else if (isset ( $input ["body"] )) {
        foreach ( $input ["body"] as $clause ) {
            if (find_an_obj_in_body ( $obj, $clause ))
                return true;
        }
        return false;
    }
    return false;
}
function array_remove_each(&$arr, $arr1) {
    foreach ( $arr as $k => $ele ) {
        foreach ( $arr1 as $ele1 )
            if ($ele == $ele1)
                unset ( $arr [$k] );
    }
    $arr = array_values ( $arr );
}
function find_all_vars_in_function_def($function) {
    $inner_vars = find_all_unique_types_non_block_in_body ( "var", $function ["body"] );
    array_remove_each ( $inner_vars, $function ["vars"] );
    return $inner_vars;
}
function find_return_in_body($body) {
    return find_an_obj_in_body ( [ "name" => "@return","type" => "function"
    ], $body );
}
function parse(string $path) {
    $content = file_get_contents ( $path );
    $tokens_arr = tokenize ( $content );
    $tokens_arr = merge_strings ( $tokens_arr );
    $structure_arr = structurelize ( $tokens_arr );
    $GLOBALS ["main"] = $structure_arr;
}
parse ( 'test.gtc' );
// print_r ( $classes );
// exit ( 0 );
// =========================================================================================
// =========================================================================================
// =========================================================================================
// =========================================================================================
// =========================================================================================
// =========================================================================================
function php_parse() {
    return array_merge ( $GLOBALS ["funcs"], $GLOBALS ["classes"], [ $GLOBALS ["main"]
    ] );
}
function php_translate_each($words_arr, $addtype = "") {
    $return = [ ];
    foreach ( $words_arr as $k => $word ) {
        if (! is_array ( $word ))
            $word = [ $word,$addtype
            ];
        $return [$k] = php_translate ( $word );
    }
    return ($return);
}
function php_translate($word) {
    if (! $word)
        return "";
    $operators_translate = [ [ ":","="
    ],[ "=","=="
    ],[ "==","==="
    ]
    ];
    if (is_array ( $word )) {
        $type = isset ( $word ["type"] ) ? $word ["type"] : $word [1];
        $name = isset ( $word ["name"] ) ? $word ["name"] : $word [0];
        if (in_array ( $type, [ "class","function"
        ] ))
            return "_" . substr ( $name, 1 );
        elseif ($type == "operator") {
            foreach ( $operators_translate as $pair ) {
                if ($name == $pair [0])
                    return $pair [1];
            }
            return $name;
        } else
            return $name;
    } else {
        if (in_array ( $word [0], [ '@','^'
        ] ))
            return substr ( $word, 1 );
        else
            return $word;
    }
}
function php_reconstruct($parsed, $c = 0) {
    // if ($parsed === null)
    // return "";
    // if (! is_array ( $parsed ))
    // return $parsed;
    if (! $parsed)
        return '';
    if (isset ( $parsed ["type"] ))
        $type = $parsed ["type"];
    else
        $type = $parsed [1];
    $return = "";
    if ($type == "block") {
        $body = $parsed ["body"];
        $return .= "{";
        foreach ( $body as $k => $sub_structure ) {
            $return .= php_reconstruct ( $sub_structure, $c + 1 );
            if ((! isset ( $sub_structure ["type"] ) || $sub_structure ["type"] != "block") && (! isset ( $sub_structure ["body"] ["type"] ) || $sub_structure ["body"] ["type"] != "block"))
                $return .= ";";
        }
        $return .= "}";
        // return $return;
    } elseif ($type == "ifs") {
        $tmp = $parsed ["ifs"];
        $return .= "if (" . cpp_reconstruct ( $tmp [0] ["condition"], $c + 1 ) . ")" . CPP_EOL;
        $return .= cpp_reconstruct ( $tmp [0] ["body"], $c + 1 );
        array_shift ( $tmp );
        foreach ( $tmp as $if ) {
            $return .= "if (" . cpp_reconstruct ( $if ["condition"], $c + 1 ) . ")" . CPP_EOL;
            $return .= cpp_reconstruct ( $if ["body"], $c + 1 );
        }
        if ($parsed ["else"] ["body"])
            $return .= "else " . cpp_reconstruct ( $parsed ["else"], $c + 1 );
    } elseif ($type == "for") {
        $return .= "for (" . php_reconstruct ( $parsed ["init"], $c + 1 ) . ";" . php_reconstruct ( $parsed ["increment"], $c + 1 ) . ";" . php_reconstruct ( $parsed ["terminate"], $c + 1 ) . ")" . PHP_EOL;
        $return .= php_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "while") {
        $return .= "while (" . php_reconstruct ( $parsed ["condition"], $c + 1 ) . ")" . PHP_EOL;
        $return .= php_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "try") {
        $return .= "try" . PHP_EOL;
        $return .= php_reconstruct ( $parsed ["body"], $c + 1 );
        foreach ( $parsed ["catch"] as $catch )
            $return .= "catch (" . php_translate ( [ $catch ["exception_class"],"class"
            ] ) . " " . php_translate ( $catch ["catch_var"] ) . ")" . php_reconstruct ( $catch ["body"], $c + 1 );
        if ($parsed ["finally"])
            $return .= "finally " . php_reconstruct ( $parsed ["finally"], $c + 1 );
    } elseif ($type == "func_def") {
        $return .= "function " . php_translate ( $parsed ["name"] ) . "(";
        $vars_arr = [ ];
        foreach ( $parsed ["vars"] as $var ) {
            if (isset ( $var [1] ))
                $vars_arr [] = $var [0] . "=" . php_reconstruct ( $var [1], $c + 1 );
            else
                $vars_arr [] = $var [0];
        }
        $return .= implode ( ", ", $vars_arr );
        $return .= ")" . PHP_EOL;
        $return .= php_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "function") {
        $return .= " " . php_translate ( $parsed ["name"] ) . "(";
        $vars_arr = [ ];
        if (is_array ( $parsed ["args"] ))
            foreach ( $parsed ["args"] as $arg )
                $vars_arr [] = php_reconstruct ( $arg, $c + 1 );
        $return .= implode ( ", ", $vars_arr );
        $return .= ")" . PHP_EOL;
    } elseif ($type == "class") {
        $return .= php_translate ( $parsed ) . PHP_EOL;
    } elseif ($type == "class_def") {
        $return .= "class " . php_translate ( [ $parsed ["name"],"class"
        ] );
        if ($parsed ["extends"])
            $return .= " extends " . implode ( ", ", php_translate_each ( $parsed ["extends"], "class" ) );
        $return .= PHP_EOL;
        $return .= php_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "expr") {
        if (! is_array ( $parsed ["body"] ))
            $return .= $parsed ["body"];
        else {
            // $return .= "(";
            foreach ( $parsed ["body"] as $node ) {
                if (isset ( $node ["type"] ) && $node ["type"] == "expr")
                    $return .= "(";
                $return .= php_reconstruct ( $node, $c + 1 );
                if (isset ( $node ["type"] ) && $node ["type"] == "expr")
                    $return .= ")";
            } // $return .= ")";
        }
    } elseif ($type == "string") {
        if (isset ( $parsed ["name"] ))
            return "'" . $parsed ["name"] . "'";
        else
            return "'" . $parsed [0] . "'";
    } elseif ($type == "operator") {
        $return .= php_translate ( $parsed );
    } elseif ($type == "assignment") {
        $return .= php_reconstruct ( $parsed ["left"] ) . "=" . php_reconstruct ( $parsed ["right"] );
    } elseif ($type == "inline_block") {
        foreach ( $parsed ["body"] as $expr )
            $return .= php_reconstruct ( $expr, $c + 1 ) . ";";
    } elseif ($type == "property") {
        $return .= implode ( ' ', $parsed ["modifiers"] ) . " ";
        $return .= php_translate ( $parsed ["name"] );
        if (isset ( $parsed ["value"] ))
            $return .= "=" . php_reconstruct ( $parsed ["value"], $c + 1 );
    } elseif ($type == "method") {
        $return .= implode ( " ", $parsed ["modifiers"] ) . " ";
        $return .= "function " . php_translate ( $parsed ["name"] ) . "(";
        $vars_arr = [ ];
        foreach ( $parsed ["vars"] as $var ) {
            if (isset ( $var [1] ))
                $vars_arr [] = $var [0] . "=" . php_reconstruct ( $var [1], $c + 1 );
            else
                $vars_arr [] = $var [0];
        }
        $return .= implode ( ", ", $vars_arr );
        $return .= ")" . PHP_EOL;
        $return .= php_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "array") {
        $return .= "[";
        $tmp_arr = [ ];
        foreach ( $parsed ["body"] as $i => $ele ) {
            $tmp_arr [$i] = "";
            if ($ele ["key"] !== NULL) {
                $tmp_arr [$i] .= php_reconstruct ( $ele ["key"] ) . " => ";
            }
            $tmp_arr [$i] .= php_reconstruct ( $ele ["value"] );
        }
        $return .= implode ( ",", $tmp_arr );
        $return .= "]";
    } elseif ($type == "new") {
        $return .= "new " . php_translate ( [ $parsed ["class"],"class"
        ] ) . "(";
        $tmp_arr = [ ];
        foreach ( $parsed ["params"] as $i => $ele ) {
            $tmp_arr [$i] = "";
            $tmp_arr [$i] .= php_reconstruct ( $ele );
        }
        $return .= implode ( ",", $tmp_arr );
        $return .= ")";
    } else {
        $return .= isset ( $parsed ["name"] ) ? $parsed ["name"] : $parsed [0];
    }
    return $return;
}
// ==========================================================================================
// echo (array_reconstruct ( tokenize ( file_get_contents ( 'test.gtc' ) ) ));
// exit ( 0 );
// echo (array_reconstruct ( parse ( ('test.gtc') ) ));
// exit ( 0 );
// echo PHP_EOL . "========================================" . PHP_EOL . PHP_EOL . "<?php\n" . php_reconstruct ( parse ( 'test.gtc' ) ) ;
// php_reconstruct ( parse ( 'test.gtc' ) );
// print_r ( is_class_def ( tokenize ( "^Student << ^Human:" ) [0] [0] ) );
// print_r ( parse_property ( [ "0" => [ "0" => "\$name","1" => "var"
// ]
// ] ) );
// ===========================================================================================
function cpp_parse() {
    $return = [ "funcs" => [ ],"classes" => [ ],"main" => [ ],"main_vars" => [ ]
    ];
    foreach ( $GLOBALS ["funcs"] as $func ) {
        $func ["inner_vars"] = find_all_vars_in_function_def ( $func );
        $func ["return"] = find_return_in_body ( $func ["body"] );
        array_push ( $return ["funcs"], $func );
    }
    foreach ( $GLOBALS ["classes"] as $class ) {
        array_push ( $return ["classes"], $class );
    }
    $main = $GLOBALS ["main"];
    $main_inner_vars = find_all_vars_in_function_def ( [ "type" => "func_def","name" => "main","body" => $main,"vars" => [ [ "\$argc"
    ],[ "\$argv"
    ]
    ]
    ] );
    $main_return = find_return_in_body ( $main );
    $return ["main"] = $main;
    $return ["main_inner_vars"] = $main_inner_vars;
    $return ["main_return"] = $main_return;
    return $return;
}
function cpp_translate_each($words_arr, $addtype = "") {
    $return = [ ];
    foreach ( $words_arr as $k => $word ) {
        if (! is_array ( $word ))
            $word = [ $word,$addtype
            ];
        $return [$k] = cpp_translate ( $word );
    }
    return ($return);
}
function cpp_translate($word) {
    if (! $word)
        return "";
    $operators_translate = [ [ ":","="
    ],[ "=","=="
    ],[ "==","==="
    ],[ ".","+"
    ]
    ];
    if (is_array ( $word )) {
        $type = isset ( $word ["type"] ) ? $word ["type"] : $word [1];
        $name = isset ( $word ["name"] ) ? $word ["name"] : $word [0];
        if (in_array ( $type, [ "class","function","var"
        ] ))
            return substr ( $name, 1 );
        elseif ($type == "operator") {
            foreach ( $operators_translate as $pair ) {
                if ($name == $pair [0])
                    return $pair [1];
            }
            return $name;
        } else
            return $name;
    } else {
        if (in_array ( $word [0], [ '@','^'
        ] ))
            return substr ( $word, 1 );
        else
            return $word;
    }
}
function cpp_reconstruct($parsed, $c = 0) {
    // if ($parsed === null)
    // return "";
    // if (! is_array ( $parsed ))
    // return $parsed;
    if (! $parsed)
        return '';
    if (isset ( $parsed ["type"] ))
        $type = $parsed ["type"];
    else
        $type = $parsed [1];
    $return = "";
    if ($type == "block") {
        $body = $parsed ["body"];
        $return .= "{";
        foreach ( $body as $k => $sub_structure ) {
            $return .= cpp_reconstruct ( $sub_structure, $c + 1 );
            if ((! isset ( $sub_structure ["type"] ) || $sub_structure ["type"] != "block") && (! isset ( $sub_structure ["body"] ["type"] ) || $sub_structure ["body"] ["type"] != "block"))
                $return .= ";";
        }
        $return .= "}";
        // return $return;
    } elseif ($type == "ifs") {
        $tmp = $parsed ["ifs"];
        $return .= "if (" . cpp_reconstruct ( $tmp [0] ["condition"], $c + 1 ) . ")" . CPP_EOL;
        $return .= cpp_reconstruct ( $tmp [0] ["body"], $c + 1 );
        array_shift ( $tmp );
        foreach ( $tmp as $if ) {
            $return .= "if (" . cpp_reconstruct ( $if ["condition"], $c + 1 ) . ")" . CPP_EOL;
            $return .= cpp_reconstruct ( $if ["body"], $c + 1 );
        }
        if ($parsed ["else"] ["body"])
            $return .= "else " . cpp_reconstruct ( $parsed ["else"], $c + 1 );
    } elseif ($type == "for") {
        $return .= "for (" . cpp_reconstruct ( $parsed ["init"], $c + 1 ) . ";" . cpp_reconstruct ( $parsed ["increment"], $c + 1 ) . ";" . cpp_reconstruct ( $parsed ["terminate"], $c + 1 ) . ")" . CPP_EOL;
        $return .= cpp_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "while") {
        $return .= "while (" . cpp_reconstruct ( $parsed ["condition"], $c + 1 ) . ")" . CPP_EOL;
        $return .= cpp_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "try") {
        $return .= "try" . CPP_EOL;
        $return .= cpp_reconstruct ( $parsed ["body"], $c + 1 );
        foreach ( $parsed ["catch"] as $catch )
            $return .= "catch (" . cpp_translate ( [ $catch ["exception_class"],"class"
            ] ) . " " . cpp_translate ( $catch ["catch_var"] ) . ")" . cpp_reconstruct ( $catch ["body"], $c + 1 );
        if ($parsed ["finally"])
            $return .= cpp_reconstruct ( $parsed ["finally"], $c + 1 );
    } elseif ($type == "func_def") {
        $return_type = "V ";
        if (! $parsed ["return"])
            $return_type = "void ";
        $return .= $return_type . cpp_translate ( $parsed ["name"] ) . "(";
        $vars_arr = [ ];
        foreach ( $parsed ["vars"] as $var ) {
            if (isset ( $var [1] ))
                $vars_arr [] = "V " . $var [0] . "=" . cpp_reconstruct ( $var [1], $c + 1 );
            else
                $vars_arr [] = "V " . $var [0];
        }
        foreach ( $parsed ["inner_vars"] as $inner_var ) {
            array_unshift ( $parsed ["body"], $inner_var );
        }
        $return .= implode ( ", ", $vars_arr );
        $return .= ")" . CPP_EOL;
        $return .= cpp_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "function") {
        $return .= " " . cpp_translate ( $parsed ["name"] ) . "(";
        $vars_arr = [ ];
        if (is_array ( $parsed ["args"] ))
            foreach ( $parsed ["args"] as $arg )
                $vars_arr [] = cpp_reconstruct ( $arg, $c + 1 );
        $return .= implode ( ", ", $vars_arr );
        $return .= ")" . CPP_EOL;
    } elseif ($type == "class") {
        $return .= cpp_translate ( $parsed ) . CPP_EOL;
    } elseif ($type == "class_def") {
        $return .= "class " . cpp_translate ( [ $parsed ["name"],"class"
        ] );
        if ($parsed ["extends"])
            $return .= " extends " . implode ( ", ", cpp_translate_each ( $parsed ["extends"], "class" ) );
        $return .= CPP_EOL;
        $return .= cpp_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "expr") {
        if (! is_array ( $parsed ["body"] ))
            $return .= $parsed ["body"];
        else {
            // $return .= "(";
            foreach ( $parsed ["body"] as $node ) {
                if (isset ( $node ["type"] ) && $node ["type"] == "expr")
                    $return .= "(";
                $return .= cpp_reconstruct ( $node, $c + 1 );
                if (isset ( $node ["type"] ) && $node ["type"] == "expr")
                    $return .= ")";
            } // $return .= ")";
        }
    } elseif ($type == "string") {
        if (isset ( $parsed ["name"] ))
            return "'" . $parsed ["name"] . "'";
        else
            return "'" . $parsed [0] . "'";
    } elseif ($type == "operator") {
        $return .= cpp_translate ( $parsed );
    } elseif ($type == "assignment") {
        $return .= cpp_reconstruct ( $parsed ["left"] ) . "=" . cpp_reconstruct ( $parsed ["right"] );
    } elseif ($type == "inline_block") {
        foreach ( $parsed ["body"] as $expr )
            $return .= cpp_reconstruct ( $expr, $c + 1 ) . ";";
    } elseif ($type == "property") {
        $return .= implode ( ' ', $parsed ["modifiers"] ) . " ";
        $return .= cpp_translate ( $parsed ["name"] );
        if (isset ( $parsed ["value"] ))
            $return .= "=" . cpp_reconstruct ( $parsed ["value"], $c + 1 );
    } elseif ($type == "method") {
        $return .= implode ( " ", $parsed ["modifiers"] ) . " ";
        $return .= "V " . cpp_translate ( $parsed ["name"] ) . "(";
        $vars_arr = [ ];
        foreach ( $parsed ["vars"] as $var ) {
            if (isset ( $var [1] ))
                $vars_arr [] = $var [0] . "=" . cpp_reconstruct ( $var [1], $c + 1 );
            else
                $vars_arr [] = $var [0];
        }
        $return .= implode ( ", ", $vars_arr );
        $return .= ")" . CPP_EOL;
        $return .= cpp_reconstruct ( $parsed ["body"], $c + 1 );
    } elseif ($type == "array") {
        $return .= "[";
        $tmp_arr = [ ];
        foreach ( $parsed ["body"] as $i => $ele ) {
            $tmp_arr [$i] = "";
            if ($ele ["key"] !== NULL) {
                $tmp_arr [$i] .= cpp_reconstruct ( $ele ["key"] ) . " => ";
            }
            $tmp_arr [$i] .= cpp_reconstruct ( $ele ["value"] );
        }
        $return .= implode ( ",", $tmp_arr );
        $return .= "]";
    } elseif ($type == "new") {
        $return .= "new " . cpp_translate ( [ $parsed ["class"],"class"
        ] ) . "(";
        $tmp_arr = [ ];
        foreach ( $parsed ["params"] as $i => $ele ) {
            $tmp_arr [$i] = "";
            $tmp_arr [$i] .= cpp_reconstruct ( $ele );
        }
        $return .= implode ( ",", $tmp_arr );
        $return .= ")";
    } else {
        $return .= isset ( $parsed ["name"] ) ? $parsed ["name"] : $parsed [0];
    }
    return $return;
}
function is_tokens_line($arr) {
    foreach ( $arr as $ele ) {
        if (sizeof ( $ele != 2 ) && isset ( $ele [0] ) && isset ( $ele [1] ) && is_string ( $ele [0] ) && is_string ( $ele [0] ))
            continue;
        else
            return false;
    }
    return true;
}
// =========================================================================================
// echo (array_reconstruct ( tokenize ( file_get_contents ( 'test.gtc' ) ) ));
// exit ( 0 );
// echo (array_reconstruct ( cpp_parse () ));
// // exit ( 0 );
// $parsed = cpp_parse ();
// echo PHP_EOL . "========================================" . PHP_EOL . PHP_EOL .
// "#include<gtlang.h>\n";
// foreach ( $parsed ["funcs"] as $func )
// echo cpp_reconstruct ( $func ) . CPP_EOL;
// foreach ( $parsed ["classes"] as $class )
// echo cpp_reconstruct ( $class ) . CPP_EOL;
// foreach ( $parsed ["main_inner_vars"] as $inner_var ) {
// array_unshift ( $parsed ["main"] ["body"], $inner_var );
// }
// echo "int main(int argc, char *argv[])\n" . cpp_reconstruct ( $parsed ["main"] );
// php_reconstruct ( parse ( 'test.gtc' ) );
// print_r ( is_class_def ( tokenize ( "^Student << ^Human:" ) [0] [0] ) );
// print_r ( parse_property ( [ "0" => [ "0" => "\$name","1" => "var"
// ]
// ] ) );

echo array_reconstruct ( $funcs ) . PHP_EOL . PHP_EOL;
echo array_reconstruct ( $main ) . PHP_EOL . PHP_EOL . PHP_EOL;
// ===========================================================================================
$global_vars = $local_vars = [ ];
$current_func_lv = 0;
$break = $continue = 0;
function assign_var($var, $value) {
    if ($var ["type"] == "var") {
        $var_name = $var ["name"];
        if ($GLOBALS ["current_func_lv"] == 0) {
            $return = $GLOBALS ["global_vars"] [$var_name] = ($value);
            return $return;
        } else {
            $return = $GLOBALS ["local_vars"] [$var_name] = ($value);
            return $return;
        }
    } elseif ($var ["type"] == "var_global") {
        $var_name = $var ["name"];
        if (isset ( $GLOBALS ["global_vars"] [$var_name] )) {
            $return = $GLOBALS ["global_vars"] [$var_name] = ($value);
            return $return;
        } else
            throw new Exception ( "no global var $var_name" );
    } elseif ($var ["type"] == "complex_value") {
        $ref = get_ref ( $var ["name"], gt_eval ( $var ["key"] ) );
        $ref = ($value);
    } else
        throw new Exception ( "not valid left side of assignment" );
}
function &get_ref($value_name, $value_key) {
    if ($value_name ["type"] == "expr") {
        return get_ref ( $value_name ["body"], $value_key );
    } elseif ($value_name ["type"] == "var") {
        $var_name = $value_name ["name"];
        if ($GLOBALS ["current_func_lv"] == 0) {
            $return = &$GLOBALS ["global_vars"] [$var_name];
            return $return;
        } else {
            $return = &$GLOBALS ["local_vars"] [$var_name];
            return $return;
        }
    } elseif ($value_name ["type"] == "complex_value") {
        $ref = get_ref ( value_name ["name"] );
        $key = gt_eval ( $value_key );
        if (isset ( $ref [$key] )) {
            $return = &$ref [$key];
        } else {
            $ref [$key] = "";
            $return = &$ref [$key];
        }
    } else {
        throw new Exception ( "not valid left side of assignment" );
    }
}
function get_value($value) {
    if ($value ["type"] == "var") {
        $var_name = $value ["name"];
        if ($GLOBALS ["current_func_lv"] == 0) {
            $return = $GLOBALS ["global_vars"] [$var_name];
            return $return;
        } else {
            $return = $GLOBALS ["local_vars"] [$var_name];
            return $return;
        }
    } elseif ($value ["type"] == "complex_value") {
        $result = gt_eval ( $value ["name"] );
        return $result [gt_eval ( $value ["key"] )];
    }
}
function &ref_var($var_complex) {
    if ($var_complex ["type"] == "var") {
        $var_name = $var_complex ["name"];
        if ($GLOBALS ["current_func_lv"] == 0) {
            $return = &$GLOBALS ["global_vars"] [$var_name];
            return $return;
        } else {
            $return = &$GLOBALS ["local_vars"] [$var_name];
            return $return;
        }
    }
}
function gt_exec($parsed) {
    if ($parsed ["type"] == "block" || $parsed ["type"] == "inline_block") {
        foreach ( $parsed ["body"] as $cmd ) {
            if ($GLOBALS ["break"] > 0 || $GLOBALS ["break"] > 0) {
                // $GLOBALS ["break"] --;
                return 1;
            }
            if ($cmd ["type"] == "block" || $cmd ["type"] == "inline_block") {
                gt_exec ( $cmd );
                continue;
            } else if ($cmd ["type"] == "ifs") {
                foreach ( $cmd ["ifs"] as $if ) {
                    if (gt_eval ( $if ["condition"] )) {
                        gt_exec ( $if ["body"] );
                        break;
                    }
                }
                gt_exec ( $cmd ["else"] );
            } else if ($cmd ["type"] == "for") {
                gt_eval ( $cmd ["init"] );
                while ( true ) {
                    if ($GLOBALS ["break"]) {
                        $GLOBALS ["break"] = 0;
                        break;
                    }
                    if ($GLOBALS ["continue"] > 1) {
                        $GLOBALS ["continue"] --;
                        break;
                    }
                    if ($GLOBALS ["continue"] == 1) {
                        $GLOBALS ["break"] --;
                        continue;
                    }
                    gt_eval ( $cmd ["increment"] );
                    gt_exec ( $cmd ["body"] );
                    if (! gt_eval ( $cmd ["terminate"] ))
                        break;
                }
            } else if ($cmd ["type"] == "while") {
                while ( true ) {
                    if (! gt_eval ( $cmd ["condition"] ))
                        break;
                    if ($GLOBALS ["break"] > 0) {
                        $GLOBALS ["break"] --;
                        break;
                    }
                    if ($GLOBALS ["continue"] > 1) {
                        $GLOBALS ["continue"] --;
                        break;
                    }
                    if ($GLOBALS ["continue"] == 1) {
                        $GLOBALS ["break"] --;
                        continue;
                    }
                    gt_exec ( $cmd ["body"] );
                }
            } else if ($cmd ["type"] == "try") {
                try {
                    gt_exec ( $cmd ["body"] );
                } catch ( GTException $e ) {
                    $is_catched = false;
                    foreach ( $cmd ["catch"] as $catch ) {
                        if (strpos ( $e->getMessage (), $catch ["exception_class"] . ":" ) === 0) {
                            assign_var ( $catch ["catch_var"], $e );
                            gt_exec ( $catch ["body"] );
                            $is_catched = true;
                            break;
                        }
                    }
                    if (! $is_catched)
                        throw new Exception ( "not catched" );
                }
            } else if (isset ( $cmd ["body"] ) && $cmd ["body"] [0] == [ "name" => "break","type" => "keyword"
            ]) {
                if (sizeof ( $cmd ["body"] ) == 1) {
                    $GLOBALS ["break"] = 1;
                    return 0;
                } else if (sizeof ( $cmd ["body"] ) == 2 && $cmd ["body"] [1] ["type"] == "number") {
                    $GLOBALS ["break"] = $cmd ["body"] [1] ["name"];
                    return 0;
                } else
                    throw new Exception ( "incorrect break" );
            } else if (isset ( $cmd ["body"] ) && $cmd ["body"] [0] == [ "name" => "continue","type" => "keyword"
            ]) {
                if (sizeof ( $cmd ["body"] ) == 1) {
                    $GLOBALS ["continue"] = 1;
                    return 0;
                } else if (sizeof ( $cmd ["body"] ) == 2 && $cmd ["body"] [1] ["type"] == "number") {
                    $GLOBALS ["break"] = $cmd ["body"] [1] ["name"];
                    return 0;
                } else
                    throw new Exception ( "incorrect break" );
            } else {
                assign_var ( [ "name" => "\$answer_var","type" => "var"
                ], gt_eval ( $cmd ) );
            }
        }
    }
}
function find_operator_in_expr($expr) {
    foreach ( $expr ["body"] as $k => $obj ) {
        if ($obj ["type"] == "operator") {
            unset ( $expr ["body"] [$k] );
            return [ "operator" => $obj ["name"],"expr" => array_values ( $expr ["body"] ),"opt_pos" => $k
            ];
        }
    }
    return [ ];
}
function gt_do($opt, $params, $opt_pos) {
    if ($opt == "+") {
        return gt_eval ( $params [0] ) + gt_eval ( $params [1] );
    } elseif ($opt == "-") {
        return gt_eval ( $params [0] ) - gt_eval ( $params [1] );
    } elseif ($opt == "*") {
        return gt_eval ( $params [0] ) * gt_eval ( $params [1] );
    } elseif ($opt == "/") {
        return gt_eval ( $params [0] ) / gt_eval ( $params [1] );
    } elseif ($opt == "%") {
        return gt_eval ( $params [0] ) % gt_eval ( $params [1] );
    } elseif ($opt == "**") {
        return gt_eval ( $params [0] ) ** gt_eval ( $params [1] );
    } elseif ($opt == "&&") {
        return gt_eval ( $params [0] ) && gt_eval ( $params [1] );
    } elseif ($opt == "||") {
        return gt_eval ( $params [0] ) || gt_eval ( $params [1] );
    } elseif ($opt == "!") {
        return ! gt_eval ( $params [0] );
    } elseif ($opt == "==") {
        return gt_eval ( $params [0] ) == gt_eval ( $params [1] );
    } elseif ($opt == "===") {
        return gt_eval ( $params [0] ) === gt_eval ( $params [1] );
    } elseif ($opt == "<") {
        return gt_eval ( $params [0] ) < gt_eval ( $params [1] );
    } elseif ($opt == "<=") {
        return gt_eval ( $params [0] ) <= gt_eval ( $params [1] );
    } elseif ($opt == ">") {
        return gt_eval ( $params [0] ) > gt_eval ( $params [1] );
    } elseif ($opt == ">=") {
        return gt_eval ( $params [0] ) >= gt_eval ( $params [1] );
    } elseif ($opt == "!=") {
        return gt_eval ( $params [0] ) != gt_eval ( $params [1] );
    } elseif ($opt == "++") {
        assign_var ( $params [0], $return1 = ($return0 = gt_eval ( $params [0] )) + 1 );
        if ($opt_pos == 0)
            return $return0;
        else
            return $return1;
    } elseif ($opt == "--") {
        assign_var ( $params [0] ["name"], $return1 = ($return0 = gt_eval ( $params [0] )) - 1 );
        if ($opt_pos == 0)
            return $return0;
        else
            return $return1;
    } elseif ($opt == "+:") {
        assign_var ( $params [0] ["name"], $return = gt_eval ( $params [0] ) + gt_eval ( $params [1] ) );
        return $return;
    } elseif ($opt == "-:") {
        assign_var ( $params [0] ["name"], $return = gt_eval ( $params [0] ) - gt_eval ( $params [1] ) );
        return $return;
    } elseif ($opt == "*:") {
        assign_var ( $params [0] ["name"], $return = gt_eval ( $params [0] ) * gt_eval ( $params [1] ) );
        return $return;
    } elseif ($opt == "/:") {
        assign_var ( $params [0] ["name"], $return = gt_eval ( $params [0] ) / gt_eval ( $params [1] ) );
        return $return;
    } elseif ($opt == "%:") {
        assign_var ( $params [0] ["name"], $return = gt_eval ( $params [0] ) % gt_eval ( $params [1] ) );
        return $return;
    } else {
        throw new Exception ( "unknown operator" );
    }
}
function get_var_global_value($parsed) {
    return $GLOBALS ["global_vars"] [$parsed ["name"]];
}
function get_var_local_value($parsed) {
    return $GLOBALS ["local_vars"] [$parsed ["name"]];
}
function call_function($parsed) {
    // count required params;
    $function = [ ];
    foreach ( $GLOBALS ["funcs"] as $defined_func ) {
        if ($defined_func ["name"] == $parsed ["name"]) {
            $function = $defined_func;
            break;
        }
    }
    if (! $function) {
        try {
            $params_str_arr = $tmp = [ ];
            foreach ( $parsed ["args"] as $i => $arg ) {
                $tmp [] = gt_eval ( $arg );
                $params_str_arr [] = "\$tmp[$i]";
            }
            $params_str = implode ( ",", $params_str_arr );
            if ($parsed ["name"] == "@echo") {
                eval ( php_translate ( $parsed ["name"] ) . "(" . $params_str . ");" );
                return 0;
            } else {
                eval ( '$result = ' . php_translate ( $parsed ["name"] ) . "(" . $params_str . ");" );
                return $result;
            }
        } catch ( Exception $e ) {
            throw new Exception ( "function not defined" );
        }
    }
    if (sizeof ( $parsed ["args"] ) < $function ["vars"]) {
        throw new Exception ( "function call dont more params than function define" );
    }
    $count_required_params = 0;
    foreach ( $function ["vars"] as $var ) {
        if (isset ( $var [1] ))
            break;
        $count_required_params ++;
    }
    if (sizeof ( $parsed ["args"] ) < $count_required_params)
        throw new Exception ( "function call dont have enough params" );
    // assign called params
    foreach ( $parsed ["args"] as $k => $arg ) {
        $var = $function ["vars"] [$k];
        assign_var ( $var, gt_eval ( $arg ) );
    }
    // assign default un-called params
    if ($k < sizeof ( $function ["vars"] )) {
        for($j = $k; $j < sizeof ( $function ["vars"] ); $j ++) {
            $passed_vars_value [] = $function ["vars"] [$j] [1];
        }
    }
    $GLOBALS ["current_func_lv"] ++;
    foreach ( $passed_vars_value as $j => $passed_value ) {
        assign_var ( $function ["vars"] [$j] [0], $passed_value );
    }
    gt_exec ( $function ["body"] );
    $GLOBALS ["current_func_lv"] --;
}
function create_array_var($parsed) {
    $return = [ ];
    foreach ( $parsed ["body"] as $element ) {
        $return [gt_eval ( $element ["key"] )] = gt_eval ( $element ["value"] );
    }
    return $return;
}
function gt_eval($parsed) {
    if (in_array ( $parsed ["type"], [ "number","string"
    ] ))
        return $parsed ["name"];
    if ($parsed ["type"] == "expr") {
        if (sizeof ( $parsed ["body"] ) == 1) {
            return gt_eval ( $parsed ["body"] [0] );
        } else {
            $find_result = find_operator_in_expr ( $parsed );
            $result = gt_do ( $find_result ["operator"], $find_result ["expr"], $find_result ["opt_pos"] );
            return $result;
        }
    }
    if ($parsed ["type"] == "array") {
        return create_array_var ( $parsed );
    }
    if ($parsed ["type"] == "var") {
        return get_value ( $parsed );
    }
    if ($parsed ["type"] == "var_global") {
        return get_var_global_value ( $parsed );
    }
    if ($parsed ["type"] == "function") {
        $backup_vars = $GLOBALS ["local_vars"];
        // $GLOBALS ["current_func_lv"] ++;
        $result = call_function ( $parsed );
        $GLOBALS ["local_vars"] = $backup_vars;
        // $GLOBALS ["current_func_lv"] --;
        return $result;
    }
    if ($parsed ["type"] == "array") {
        return create_array_var ( $parsed );
    }
    if ($parsed ["type"] == "assignment") {
        assign_var ( $parsed ["left"], $result = gt_eval ( $parsed ["right"] ) );
        return $result;
    }
    if ($parsed ["type"] == "complex_value") {
        $result = get_value ( $parsed );
        return $result;
    }
    throw new Exception ( "unsupported expr" );
}
gt_exec ( $main );