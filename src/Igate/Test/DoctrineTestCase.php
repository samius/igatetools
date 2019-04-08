<?php

namespace Igate\Test;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DoctrineTestCase extends KernelTestCase
{
    /**
     * @var array Kes, which testClass database is created.
     */
    private static $createdDatabases = array();

    /**
     * @var string
     */
    private static $backupDatabaseFileName;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        if ($this->isDatabaseCreated()) {
            $this->restoreDatabase();
        } else {
            $this->createDatabase();
            $this->em->getConnection()->beginTransaction();
            $this->afterDatabaseCreated();
            $this->em->getConnection()->commit();
            $this->backupDatabase();
            $this->markDatabaseCreated();
        }
    }

    /**
     * @return bool
     */
    private function isDatabaseCreated()
    {
        return isset(self::$createdDatabases[\get_called_class()]);
    }

    private function markDatabaseCreated()
    {
        self::$createdDatabases[\get_called_class()] = true;
    }

    /**
     * Creates database schema according to Doctrine schema tool
     * @throws \RuntimeException
     */
    private function createDatabase()
    {
        if (!$this->em->getConnection()->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SqlitePlatform) {
            throw new \RuntimeException('Database is not sqlite, cant create source file.');
        }
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropDatabase();
        $tool->createSchema($allMetadata);
    }

    /**
     * Tests override this method, so they can insert testData before backup is created
     */
    protected function afterDatabaseCreated()
    {
    }

    /**
     * Database has some test data inserted. Lets backup this data, so we do not have to instert it before every test
     */
    private function backupDatabase()
    {
        \copy($this->getDatabaseFileName(), $this->getBackupDatabaseFileName());
        self::$backupDatabaseFileName = $this->getBackupDatabaseFileName();
    }

    private function restoreDatabase()
    {
        \copy($this->getBackupDatabaseFileName(), $this->getDatabaseFileName());
    }

    /**
     * @return string
     */
    private function getDatabaseFileName()
    {
        return $this->em->getConnection()->getDatabase();
    }

    /**
     * @return string
     */
    private function getBackupDatabaseFileName()
    {
        return $this->getDatabaseFileName() . '.default';
    }

    /**
     * Remove sqlite file with database. It will be created again from backup
     */
    protected function tearDown()
    {
        $conn = $this->em->getConnection();
        $conn->close();
        $this->em->clear();
        $dbFilePath = $this->getDatabaseFileName();
        if (\file_exists($dbFilePath)) {
            \unlink($dbFilePath);
        }

    }

    /**
     * Remove backup sqlite file.
     */
    public static function tearDownAfterClass()
    {
        if (\file_exists(self::$backupDatabaseFileName)) {
            \unlink(self::$backupDatabaseFileName);
        }
        unset(self::$createdDatabases[\get_called_class()]);
    }
}
