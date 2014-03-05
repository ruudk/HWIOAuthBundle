<?php

namespace HWI\Bundle\OAuthBundle\Tests\Functional;

use HWI\Bundle\OAuthBundle\Controller\ConnectController;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\SecurityContext;

class ConnectControllerTest extends BaseTestCase
{
    public function testIndex()
    {
        $client = $this->createClient();
        $this->importDatabaseSchema();

        $container = $client->getContainer();
        $container->get('security.context')->setToken(new AnonymousToken('foo', 'bar'));

        $controller = new ConnectController();
        $controller->setContainer($container);

        $session = new Session(new MockArraySessionStorage());

        $request = Request::create('/');
        $request->setSession($session);
        $exception = new AccountNotLinkedException();
        $exception->setResourceOwnerName('facebook');
        $token = new OAuthToken('123');
        $exception->setToken($token);
        $request->attributes->set(SecurityContext::AUTHENTICATION_ERROR, $exception);

        $container->enterScope('request');
        $container->set('request', $request);
        $key = time();
        $response = $controller->connectAction($request);
        $container->leaveScope('request');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $request = Request::create('/');
        $request->setSession($session);

        $container->enterScope('request');
        $container->set('request', $request);

        sleep(2);

        $response = $controller->registrationAction($request, $key);

        echo $response->getContent();

        sleep(2);

        $response = $controller->registrationAction($request, $key);

        echo $response->getContent();

        $container->leaveScope('request');
    }
}
