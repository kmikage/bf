<?php
// Brainfuck PHP interpreter.
// Ver: 0.1
// 2020.07.31 kei9298d@gmail.com

function show_help() {
    echo('Brainfuck PHP interpreter.'."\n");
    echo('$ php bh.php <BF Source Code>.'."\n");
}

// bf file name.
if (isset($argv[1])) {
    // help mode.
    if($argv[1] == '-h') { show_help(); exit(0); }
    // input file name
    else { $fn = $argv[1]; }
} else { show_help(); exit(1); }

// debug mode flag.
if(isset($argv[2]) && $argv[2] == '-v') { $flg_debug = true; }
else { $flg_debug = false; }

// 配列
$bf_data[0] = 0;
// 配列のポインタ
$bf_pointer = 0;
// 実行中の命令のアドレス
$bf_addr = 0;
// ループで戻ってくるアドレス
$bf_lpbk = 0;

$src = file_get_contents($fn);
if ( $src === false) {
    echo('Error - not open bf souce code.'."\n");
    exit(1);
}

// $bf_source[$bf_pointer] = 1byte bf command.
$bf_source = str_split($src); 

while( $bf_addr < count($bf_source) ) {

    // for debuging.
    if ($flg_debug) { var_dump($bf_addr.' - '.$bf_source[$bf_addr]); }
    if ($flg_debug) { var_dump($bf_data); }

    switch($bf_source[$bf_addr]) {

        // inc
        case '+':
            $bf_data[$bf_pointer]++;
            break;

        // dec
        case '-':
            $bf_data[$bf_pointer]--;
            break;

        // input
        case ',':
            $in = trim(fgets(STDIN));
            $in = substr( $in, 0, 1);
            $bf_data[$bf_pointer] = hexdec(bin2hex($in));
            unset($in);
            break;
        
        // output
        case '.':
            switch($bf_data[$bf_pointer]) {
                case 10:
                    echo("\n");
                    break;          
                case 13:
                    echo("\r");
                    break;
        		default:	
                    echo(hex2bin(dechex($bf_data[$bf_pointer])));
                    break;
            }
            break;

        // pointer shift right
        case '>':
            $bf_pointer++;
            if (!isset($bf_data[$bf_pointer])) { $bf_data[$bf_pointer] = 0; }
            break;

        // pointer shift left
        case '<':
            $bf_pointer--;
            break;


        // start loop
        case '[':
            // loop back point
            $bf_lpbk = $bf_addr;
            break;

        // end loop
        case ']':
            if ($bf_data[$bf_pointer] == 0) {
               $bf_lpbk = 0;
            } else {
                // jump to loop back point
               $bf_addr = $bf_lpbk;
            }
            break;
    }
    $bf_addr++;
}

exit(0);
