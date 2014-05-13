<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Application;

use Bluz\Tests\TestCase;
use Bluz\Application\Application;

/**
 * ApplicationTest
 *
 * @author   Anton Shevchuk
 * @created  21.05.13 10:24
 */
class ApplicationTest extends TestCase
{
    /**
     * @covers \Bluz\Application\Application::reflection
     * @return void
     */
    public function testReflection()
    {
        $controllerFile = dirname(__FILE__) .'/../Fixtures/ConcreteControllerWithData.php';

        $reflectionData = $this->getApp()->reflection($controllerFile);

        /** @var \closure $controllerClosure */
        $controllerClosure = require $controllerFile;

        $this->assertEquals($reflectionData, $controllerClosure('a', 'b', 'c'));
    }

    /**
     * Check all getters of Application
     *
     * @return void
     */
    public function testGetPackages()
    {
        $this->assertInstanceOf('\Bluz\Acl\Acl', $this->getApp()->getAcl());
        $this->assertInstanceOf('\Bluz\Auth\Auth', $this->getApp()->getAuth());
        // cache disabled for testing
        $this->assertInstanceOf('\Bluz\Common\Nil', $this->getApp()->getCache());
        $this->assertInstanceOf('\Bluz\Config\Config', $this->getApp()->getConfig());
        $this->assertInstanceOf('\Bluz\Db\Db', $this->getApp()->getDb());
        $this->assertInstanceOf('\Bluz\EventManager\EventManager', $this->getApp()->getEventManager());
        $this->assertInstanceOf('\Bluz\View\Layout', $this->getApp()->getLayout());
        $this->assertInstanceOf('\Bluz\Logger\Logger', $this->getApp()->getLogger());
        $this->assertInstanceOf('\Bluz\Mailer\Mailer', $this->getApp()->getMailer());
        $this->assertInstanceOf('\Bluz\Messages\Messages', $this->getApp()->getMessages());
        $this->assertInstanceOf('\Bluz\Registry\Registry', $this->getApp()->getRegistry());
        $this->assertInstanceOf('\Bluz\Http\Request', $this->getApp()->getRequest());
        $this->assertInstanceOf('\Bluz\Http\Response', $this->getApp()->getResponse());
        $this->assertInstanceOf('\Bluz\Router\Router', $this->getApp()->getRouter());
        $this->assertInstanceOf('\Bluz\Session\Session', $this->getApp()->getSession());
        $this->assertInstanceOf('\Bluz\Translator\Translator', $this->getApp()->getTranslator());
        $this->assertInstanceOf('\Bluz\View\View', $this->getApp()->getView());
    }
}
