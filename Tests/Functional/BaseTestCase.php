<?php

namespace HWI\Bundle\OAuthBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    static protected function createKernel(array $options = array())
    {
        return self::$kernel = new AppKernel(
            isset($options['config']) ? $options['config'] : 'config.yml'
        );
    }

    protected function setUp()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().'/HWIOAuthBundle/');
    }

    protected final function importDatabaseSchema()
    {
        $em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $schemaTool->dropDatabase();
            $schemaTool->createSchema($metadata);
        }
    }

    public function assertClientRedirectEquals($expected, Client $client)
    {
        $this->assertEquals($expected, $client->getResponse()->headers->get('Location'));
    }
}
