<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Mapper;

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\NotImplementedException;
use Bluz\Controller\Controller;
use Bluz\Controller\ControllerException;
use Bluz\Crud\AbstractCrud;
use Bluz\Http\RequestMethod;
use Bluz\Proxy\Acl;
use Bluz\Proxy\Request;
use Bluz\Proxy\Router;

/**
 * Mapper for controller
 *
 * @package  Bluz\Rest
 * @author   Anton Shevchuk
 */
abstract class AbstractMapper
{
    /**
     * @var string HTTP Method
     */
    protected $method = RequestMethod::GET;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var array identifier
     */
    protected $primary;

    /**
     * @var string relation list
     */
    protected $relation;

    /**
     * @var string relation Id
     */
    protected $relationId;

    /**
     * @var array params of query
     */
    protected $params = [];

    /**
     * @var array query data
     */
    protected $data = [];

    /**
     * @var AbstractCrud instance of CRUD
     */
    protected $crud;

    /**
     * [
     *     METHOD => [
     *         'module' => 'module',
     *         'controller' => 'controller',
     *         'acl' => 'privilege',
     *     ],
     * ]
     *
     * @var array
     */
    protected $map = [];

    /**
     * Prepare params
     *
     * @return array
     */
    abstract protected function prepareParams(): array;

    /**
     * @param AbstractCrud $crud
     */
    public function __construct(AbstractCrud $crud)
    {
        $this->crud = $crud;
    }

    /**
     * Add mapping data
     *
     * @param string $method
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function addMap($method, $module, $controller, $acl = null)
    {
        $this->map[strtoupper($method)] = [
            'module' => $module,
            'controller' => $controller,
            'acl' => $acl
        ];
    }

    /**
     * Add mapping for HEAD method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function head($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::HEAD, $module, $controller, $acl);
    }

    /**
     * Add mapping for GET method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function get($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::GET, $module, $controller, $acl);
    }

    /**
     * Add mapping for POST method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function post($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::POST, $module, $controller, $acl);
    }

    /**
     * Add mapping for PATCH method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function patch($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::PATCH, $module, $controller, $acl);
    }

    /**
     * Add mapping for PUT method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function put($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::PUT, $module, $controller, $acl);
    }

    /**
     * Add mapping for DELETE method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function delete($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::DELETE, $module, $controller, $acl);
    }

    /**
     * Add mapping for OPTIONS method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function options($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::OPTIONS, $module, $controller, $acl);
    }

    /**
     * Run
     *
     * @return Controller
     * @throws ControllerException
     * @throws ForbiddenException
     * @throws NotImplementedException
     */
    public function run()
    {
        $this->prepareRequest();
        return $this->dispatch();
    }

    /**
     * Prepare request for processing
     *
     * @throws \Bluz\Controller\ControllerException
     */
    protected function prepareRequest()
    {
        // HTTP method
        $method = Request::getMethod();
        $this->method = strtoupper($method);

        // get path
        // %module% / %controller% / %id% / %relation% / %id%
        $path = Router::getCleanUri();

        $this->params = explode('/', rtrim($path, '/'));

        // module
        $this->module = array_shift($this->params);

        // controller
        $this->controller = array_shift($this->params);

        $data = Request::getParams();

        unset($data['_method'], $data['_module'], $data['_controller']);

        $this->data = $data;

        $primary = $this->crud->getPrimaryKey();
        $this->primary = array_intersect_key($this->data, array_flip($primary));
    }

    /**
     * Dispatch REST or CRUD controller
     *
     * @return mixed
     * @throws ForbiddenException
     * @throws NotImplementedException
     */
    protected function dispatch()
    {
        // check implementation
        if (!isset($this->map[$this->method])) {
            throw new NotImplementedException;
        }

        $map = $this->map[$this->method];

        // check permissions
        if (isset($map['acl'])) {
            if (!Acl::isAllowed($this->module, $map['acl'])) {
                throw new ForbiddenException;
            }
        }

        // dispatch controller
        return Application::getInstance()->dispatch(
            $map['module'],
            $map['controller'],
            $this->prepareParams()
        );
    }
}
