<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 3/6/2017
 * Time: 8:34 AM
 */

$go = $argv[1] == '--go';



$Directory = new RecursiveDirectoryIterator('one-off scripts/master-scripts');
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);


$noVersioning = [];
$needsUpdates = [];

foreach($Regex as $path)
{
    $contents = file_get_contents($path[0]);
    if(!preg_match('/call_ms_function_ver/',$contents))
    {
        $noVersioning[$path[0]] = $contents;
    } else {
        $match = [];
        if(preg_match('/print \".*RUNNING: \" . __FILE__.*\";/', $contents, $match))
        {
            if(!preg_match('/\\\\e/', $match[0]))
            {
                $needsUpdates[$path[0]] = [$contents, $match];
            }
        }
        if(preg_match('/print \".*FINISHED: \" . __FILE__.*\";/', $contents, $match))
        {
            if(!preg_match('/\\\\e/', $match[0]))
            {
                $needsUpdates[$path[0]] = [$contents, $match];
            }
        } else {
            $needsUpdates[$path[0]] = [$contents, $match];
        }
    }
}

if(count($noVersioning))
{
    echo sprintf('Files without any versioning (%d):', count($noVersioning)).PHP_EOL;
}
foreach($noVersioning as $path => $c)
{
    echo $path.PHP_EOL;
}

if(count($needsUpdates))
{
    echo sprintf('Files needing updates (%d):',count($needsUpdates)).PHP_EOL;
}
foreach($needsUpdates as $path => $c)
{
    echo $path.PHP_EOL;
}

if($go)
{
    foreach($noVersioning as $path => $c)
    {
        $c = preg_replace('/<\?php\s+/',
'<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

'
                          ,$c);
        if(!preg_match('/print \".*FINISHED: \" . __FILE__.*\";/', $c)) {
            $c .= '

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";';
        }
        file_put_contents($path, $c);
    }

    foreach($needsUpdates as $path => $c)
    {
        $c = preg_replace('/print "SKIPPING: " . __FILE__ . "<br \\/>\\\\n";/',
                          'print "\\e[33mSKIPPING: " . __FILE__ . "<br />\\n\\e[0m";',
                          $c[0]);
        $c = preg_replace('/print "RUNNING: " . __FILE__ . "<br \\/>\\\\n";/',
                          'print "\\e[32mRUNNING: " . __FILE__ . "<br />\\n\\e[0m";',
                          $c);
        if(!preg_match('/print \".*FINISHED: \" . __FILE__.*\";/', $c))
        {
            $c = preg_replace('/\?>\\s*$/','',$c);
            $c.='

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";';
        } else {
            $c = preg_replace('/print "FINISHED: " . __FILE__ . "<br \\/>\\\\n";/',
                              'print "\\e[94mFINISHED: " . __FILE__ . "<br />\\n\\e[0m";',
                              $c);
        }
        file_put_contents($path, $c);
    }
}
