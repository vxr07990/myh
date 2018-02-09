<?php

//make sure that the stupid thing runs!

//$basedir = '/var/www/public/Scrum_9/';
$basedir = '';
$script = 'go_live_sirva.php';
$cmd = '/usr/bin/php -f ' . $basedir . $script;
$checkCmd = "ps -ef | grep 'php -f' | grep " . $script . " | grep -v grep";
$final = '/End Contract Import/i';

$success = false;
$counter = 0;

//don't go infin.
while ($counter++ < 25) {
    unset($runOut);
    unset($test);
    $test = exec($checkCmd, $outArray); //, $out)
    print "test: ($test)\n";
    print "outArray (" . print_r($outArray, true) . ")\n";
    if ($test) {
        //already running.
        //do nothign.
        print "already running\n";
    } else {
        //start it again!
        unset($x);
        $x = exec($cmd, $runOut);
        print "x: ($x)\n";
        print "runOut (".print_r($runOut, true).")\n";
        foreach ($runOut as $index => $value) {
            $mat = preg_match($final, $value);
            if ($mat) {
                print "success!\n";
                $success = true;
                break 2;
            } else {
                print "fail\n";
            }
        }
    }
}

print "FINISHED.\n";
if ($success) {
    print "possible successfully.\n";
}
