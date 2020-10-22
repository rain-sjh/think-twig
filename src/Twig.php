<?php

/** @noinspection
 * PhpUndefinedNamespaceInspection,
 * PhpUndefinedClassInspection,
 * SpellCheckingInspection,
 * PhpUndefinedMethodInspection,
 * ShortListSyntaxCanBeUsedInspection,
 * PhpUnusedParameterInspection,
 * PhpUndefinedFieldInspection,
 * ReturnTypeCanBeDeclaredInspection,
 * PhpUnused
 */

/**
 * 工具: PhpStorm
 * 作者: 孙家浩
 * 邮箱: yulian1024@vip.qq.com
 * 日期: 2020/3/25
 * 时间: 20:01
 * 侵权必究
 */

namespace think\view\driver;

use think\App;
use think\exception\TemplateNotFoundException;
use think\Loader;
use think\Template;
use think\Exception;
use think\helper\Str;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use TwigExpand;

class Twig
{
	private $app;

	// 模板引擎参数
	protected $config = [
		// 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
		'auto_rule'   => 1,
		// 视图基础目录（集中式）
		'view_base'   => '',
		// 模板起始路径
		'view_path'   => '',
		// 模板文件后缀
		'view_suffix' => 'html.twig',
		// 模板文件名分隔符
		'view_depr'   => DIRECTORY_SEPARATOR,
		// 是否开启模板编译缓存,设为false则每次都会重新编译
		'tpl_cache'   => true,
	];

	public function __construct(App $app, $config = [])
	{
		$this->app = $app;
		$this->config = array_merge($this->config, (array)$config);

		if (empty($this->config['view_path'])) {
			$this->config['view_path'] = $app->getModulePath() . 'view' . DIRECTORY_SEPARATOR;
		}

		if (empty($this->config['cache_path'])) {
			$this->config['cache_path'] = $app->getRuntimePath() . 'temp' . DIRECTORY_SEPARATOR;
		}

		$this->template = new Template($app, $this->config);
	}

	/**
	 * 检测是否存在模板文件
	 * @access public
	 * @param string $template 模板文件或者模板规则
	 * @return bool
	 */
	public function exists($template)
	{
		if ('' === pathinfo($template, PATHINFO_EXTENSION)) {
			// 获取模板文件名
			$template = $this->parseTemplate($template);
		}

		return is_file($template);
	}

	/**
	 * 渲染模板文件
	 * @access public
	 * @param string $template 模板文件
	 * @param array $data 模板变量
	 * @param array $config 模板参数
	 * @return void
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Runtime
	 * @throws Twig_Error_Syntax
	 */
	public function fetch($template, $data = [], $config = [])
	{
		if ('' === pathinfo($template, PATHINFO_EXTENSION)) {
			// 获取模板文件名
			$template = $this->parseTemplate($template);
		}

		// 模板不存在 抛出异常
		if (!is_file($template)) {
			throw new TemplateNotFoundException('template not exists:' . $template, $template);
		}

		$this->twigEngine($template, $data);
	}

	private function twigEngine($template, $data): void
	{
		// 记录视图信息
		$this->app['log']->record('[ VIEW ] ' . $template . ' [ ' . var_export(array_keys($data), true) . ' ]');

		$loader = new FilesystemLoader([
			dirname($template), 
			$this->config['view_path'],
			$this->app->getAppPath() . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR
		]);

		$twig = new Environment($loader, [
			'cache' => $this->config['tpl_cache'] ? $this->config['cache_path'] : false,
		]);

		//自定义模板函数
		if (class_exists('\TwigExpand')) {
			$twig->addExtension(new TwigExpand());
		}

		//函数动态定义
		$twig->registerUndefinedFunctionCallback(static function ($name) {
			if (function_exists($name)) {
				return new TwigFunction($name, $name, ['is_safe' => ['html']]);
			}
			return false;
		});

		$twig->display(basename($template), $data);
	}

	/**
	 * 渲染模板内容
	 * @access public
	 * @param string $template 模板内容
	 * @param array $data 模板变量
	 * @param array $config 模板参数
	 * @return void
	 * @throws Twig_Error_Loader
	 * @throws Twig_Error_Runtime
	 * @throws Twig_Error_Syntax
	 */
	public function display($template, $data = [], $config = [])
	{
		$this->template->display($template, $data);
	}

	/**
	 * 自动定位模板文件
	 * @access private
	 * @param string $template 模板文件规则
	 * @return string
	 */
	private function parseTemplate(string $template): string
	{
		// 分析模板文件规则
		$request = $this->app['request'];

		// 获取视图根目录
		if (strpos($template, '@')) {
			// 跨模块调用
			list($module, $template) = explode('@', $template);
		}

		if ($this->config['view_base']) {
			// 基础视图目录
			$path = $this->config['view_path'];
		} else {
			$path = isset($module) ? $this->app->getAppPath() . $module . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR : $this->config['view_path'];
		}

		$depr = $this->config['view_depr'];

		if (0 !== strpos($template, '/')) {
			$template = str_replace(['/', ':'], $depr, $template);
			$controller = Loader::parseName($request->controller());

			if ($controller) {
				if ('' === $template) {
					// 如果模板文件名为空 按照默认规则定位
					$template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $this->getActionTemplate($request);
				} elseif (false === strpos($template, $depr)) {
					$template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
				}
			}
		} else {
			$template = str_replace(['/', ':'], $depr, substr($template, 1));
		}

		return $path . ltrim($template, '/') . '.' . ltrim($this->config['view_suffix'], '.');
	}

	protected function getActionTemplate($request)
	{
		$rule = [$request->action(true), Loader::parseName($request->action(true)), $request->action()];
		$type = $this->config['auto_rule'];

		return $rule[$type] ?? $rule[0];
	}

	/**
	 * 获取模板引擎参数
	 * @access private
	 * @param string|array $name 参数名
	 * @return mixed
	 */
	public function getConfig(string $name)
	{
		return $this->template->getConfig($name);
	}

	public function __call($method, $params)
	{
		return call_user_func_array([$this->template, $method], $params);
	}
}
