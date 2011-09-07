<?php
/**
 * 路由解析器接口
 * 职责: 路由解析, 返回路由对象
 * 实现路由解析器必须实现该接口的doParser()方法
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class AbstractWindRouter extends WindHandlerInterceptorChain {
	protected $moduleKey = 'm';
	protected $controllerKey = 'c';
	protected $actionKey = 'a';
	protected $module;
	protected $controller = 'index';
	protected $action = 'run';
	protected $reverse = "%s?%s=%s&%s=%s&%s=%s&";
	/**
	 * @var AbstractWindRoute
	 */
	protected $currentRoute = null;
	
	protected $routeMap = array();

	/**
	 * 解析请求参数，并返回路由结果
	 * @return string
	 */
	abstract public function route();

	/**
	 * 创建Url，并返回
	 * @return string
	 */
	abstract public function assemble($action, $args = array(), $route = null);

	/* (non-PHPdoc)
	 * @see WindModule::setConfig()
	 */
	public function setConfig($config) {
		parent::setConfig($config);
		if ($this->_config) {
			$this->module = $this->getConfig('module', 'default-value', $this->module);
			$this->controller = $this->getConfig('controller', 'default-value', $this->controller);
			$this->action = $this->getConfig('action', 'default-value', $this->action);
			$this->moduleKey = $this->getConfig('module', 'url-param', $this->moduleKey);
			$this->controllerKey = $this->getConfig('controller', 'url-param', $this->controllerKey);
			$this->actionKey = $this->getConfig('action', 'url-param', $this->actionKey);
			$this->createRoute($this->getConfig('rules'));
		}
	}

	/**
	 * 设置路由变量信息
	 * @param string $params
	 */
	protected function setParams($params) {
		foreach ($params as $key => $value) {
			if ($this->actionKey === $key && $value)
				$this->setAction($value);
			elseif ($this->controllerKey === $key && $value)
				$this->setController($value);
			elseif ($this->moduleKey === $key && $value)
				$this->setModule($value);
			else
				$this->getRequest()->setAttribute($value, $key);
		}
	}
    
	/**
	 * 创建过滤链 
	 * @param array $config
	 */
	protected function createRoute($config) {
		if (!$config) return ;
		$index = 0;
		foreach ($config as $ruleName => $rule) {
			$class = isset($rule['class']) ? $rule['class'] : 'WIND:router.route.WindRoute';
			$class = Wind::import($class);
			$instance = $this->getSystemFactory()->createInstance($class);
			if (!$instance instanceof AbstractWindRoute) continue;
			$this->routeMap[$ruleName] = $index ++;
			$instance->setConfig($rule);
			$this->addRoute($instance);
		}
	}
	
	/**
	 * 添加路由协议对象,如果添加的路由协议已经存在则抛出异常
	 * @param Object $routeInstance
	 * @throws WindException
	 * @return 
	 */
	public function addRoute($routeInstance, $current = false) {
		if ($current)
			$this->currentRoute = $routeInstance;
		$this->addInterceptors($routeInstance);
	}

	/**
	 * 获得业务操作
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * 获得业务对象
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * 设置action信息
	 * @param string $action
	 * @return
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * 设置controller信息
	 * @param string $controller
	 * @return
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @return the $module
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @param string $module
	 */
	public function setModule($module) {
		$this->module = $module;
	}

	/**
	 * @return the $moduleKey
	 */
	public function getModuleKey() {
		return $this->moduleKey;
	}

	/**
	 * @return the $controllerKey
	 */
	public function getControllerKey() {
		return $this->controllerKey;
	}

	/**
	 * @return the $actionKey
	 */
	public function getActionKey() {
		return $this->actionKey;
	}

	/**
	 * @param field_type $moduleKey
	 */
	public function setModuleKey($moduleKey) {
		$this->moduleKey = $moduleKey;
	}

	/**
	 * @param field_type $controllerKey
	 */
	public function setControllerKey($controllerKey) {
		$this->controllerKey = $controllerKey;
	}

	/**
	 * @param field_type $actionKey
	 */
	public function setActionKey($actionKey) {
		$this->actionKey = $actionKey;
	}
	
	/**
	 * 设置当前使用的route
	 * Enter description here ...
	 * @param AbstractWindRoute $route
	 */
	public function setCurrentRoute($route) {
		$this->currentRoute = $route;
	}
	
	/**
	 * 根据rule的规则名称，从路由链中获得该路由的对象
	 * @param string $key
	 * @return AbstractWindRoute
	 */
	public function getRoute($rule) {
		$index = $this->routeMap[$rule];
		return isset($this->_interceptors[$index]) ? $this->_interceptors[$index] : null;
	}
	
}