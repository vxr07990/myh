<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    public function test()
    {
        $this->taskCodecept()
             ->suite('acceptance')
             ->arg('--steps')
             ->run();
    }

    public function xzCreate()
    {
        $name = basename(__DIR__);
        $this->say("Creating `${name}.tar.xz` archive...");
        $this->taskExec('tar')
             ->arg("--exclude='./${name}.tar.xz'")
             ->arg("--exclude='.git'")
             ->arg("--exclude='.phpintel'")
             ->arg("--exclude='.svn'")
             ->arg("--exclude='*.exe'")
             ->arg("--exclude='*.tar.gz'")
             ->arg("--exclude='*.zip'")
             ->arg("--exclude='./logs/*.log'")
             ->arg("--exclude='./logs/*.log.*'")
             ->arg("--exclude='./logs/*.xml'")
             ->arg("--exclude='./test/templates_c/vlayout/*.tpl.php'")
             ->arg('-c')
             ->arg('--xz')
             ->arg("-f ${name}.tar.xz")
             ->arg('.')
             ->run();
    }
}
