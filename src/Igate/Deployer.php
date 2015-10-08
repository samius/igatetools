<?php
namespace Igate;

/**
 * @author milan
 *
 *
 * Project deploy helper
 * It`s possible to override, if some methods don`t meet requirements of specific project
 */
class Deployer
{
    private $keepOldBackups = 3;//how many backups will be saved at one time


    private $originalDir;


    private $workDir;


    private $basename;


    private $buildDir;

    private $environment;


    public function __construct($originalDir, $environment='prod')
    {
        $this->originalDir = $originalDir;
        $this->workDir = dirname($originalDir); //parent dir
        $this->basename = basename($originalDir);
        $this->buildDir = $this->workDir . DIRECTORY_SEPARATOR . $this->basename . '_build';
        $this->backupDir = $this->workDir . DIRECTORY_SEPARATOR . $this->getBackupDirBasename() . DateTime::now()->format('ymd-His');
        $this->environment = $environment;
    }

    public function cloneToBuildDir($gitUrl)
    {
        if (file_exists($this->buildDir)) {
            $cmd = "rm -rf {$this->buildDir}";
            $this->exec($cmd);
        }
        $cmd = "git clone $gitUrl {$this->buildDir}";
        $this->exec($cmd, "Cloning git repository to build dir");
    }

    public function setBranch($branchName)
    {
        $this->cd($this->buildDir);
        $cmd = "git checkout $branchName";
        $this->exec($cmd, "Cloning git repository to build dir");
    }

    /**
     * Original directory must contain parametersTemp.yml, the parameters file with the same version as deploying
     */
    public function copyTempPreferences()
    {
        $cmd = "mv {$this->originalDir}/parameters.yml {$this->buildDir}/app/config/parameters.yml";
        $this->exec($cmd, 'Copying original/parameters.yml to build/app/config/parameters.yml');
    }
    
    public function backupDb()
    {
        $this->cd($this->originalDir);

        $cmd = "php app/console igate:db:dump";
        $this->exec($cmd, 'Dumping database');
    }

    /**
     * @deprecated
     */
    public function gitPull()
    {
        $this->cd($this->buildDir);
        $this->exec('git reset --hard');
        $this->exec('git clean -f');
        $this->exec('git pull');
    }

    public function composerInstall()
    {
        $this->cd($this->buildDir);
        $this->exec("export SYMFONY_ENV={$this->environment}");
        $this->exec("php composer.phar install --no-dev", "Installing composer dependencies");
    }

    public function clean()
    {
        $this->cd($this->buildDir);
        $cmd = "app/console cache:clear {$this->getEnvSwitch()} --no-debug";
        $this->exec($cmd, 'Clearing cache');
        $cmd = "app/console cache:warmup {$this->getEnvSwitch()}";
        $this->exec($cmd, 'Warming up cache');

        $this->exec("chmod a+w {$this->buildDir}/app/cache -R");
        $this->exec("chmod a+w {$this->buildDir}/app/logs -R");
    }
    
    public function makeAssets()
    {
        $this->cd($this->buildDir);
        $this->exec("app/console assets:install --symlink");
        $this->exec("app/console assetic:dump {$this->getEnvSwitch()} --no-debug");
    }

    public function migrateDb()
    {
        $this->cd($this->buildDir);
        $cmd = 'yes|app/console doctrine:migrations:migrate --allow-no-migration';
        $this->exec($cmd, 'Applying migrations');
    }

    public function swapDirs()
    {
        $this->exec("mv {$this->originalDir} {$this->backupDir}");
        $this->exec("mv {$this->buildDir} {$this->originalDir}");
    }

    public function deleteOldBackups()
    {
        $this->cd($this->workDir);
        $alldirs = glob($this->getBackupDirBasename() . '*');
        //sorts in reverse order... newest first
        rsort($alldirs);

        $oldDirs = array_slice($alldirs, $this->keepOldBackups);

        $this->exec("rm -rf " . implode(' ', $oldDirs), 'Deleting old backups. ' . $this->keepOldBackups . ' will remain');
    }

    private function cd($path)
    {
        chdir($path);
        echo "-- cd $path\n";
    }

    private function exec($cmd, $description = '')
    {
        echo "################################################################################\n";
        if ($description) {
            echo mb_strtoupper($description) . "\n";
        }
        echo "executing: $cmd";
        echo "\n################################################################################\n";

        exec($cmd, $output, $returnVal);

        echo implode("\n", $output);
        echo "\n";
        echo "\n";

        if ($returnVal != 0) {
            echo "!!!!!!! FAILED !!!!!!!\n";
            var_dump($returnVal);
            throw new \RuntimeException($returnVal);
        }
    }
    
    private function getEnvSwitch()
    {
        return '--env=' . $this->environment;
    }

    private function getBackupDirBasename()
    {
        return $this->basename . '_back_';
    }

    /**
     * @param int $keepOldBackups
     * @return Deployer
     */
    public function setKeepOldBackups($keepOldBackups)
    {
        $this->keepOldBackups = $keepOldBackups;
        return $this;
    }
}
