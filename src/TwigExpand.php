<?php
/**
 * Twig 模板 拓展
 * User: 孙家浩
 * Date: 2019/4/4
 * Time: 11:24
 */

namespace think\view\driver;

use Twig\Extension\AbstractExtension;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;
use Twig\TwigFilter;

class TwigExpand extends AbstractExtension
{
	/**
	 * 过滤器 拓展.
	 * @return Twig_Filter[]
	 */
	public function getFilters()
	{
		return [
			new TwigFilter('getCity', [$this, 'getCity']),
		];
	}

	/**
	 * 函数 拓展.
	 * @return Twig_Function[]
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('load', [$this, 'load']),
			new TwigFunction('is_controller', [$this, 'is_controller']),
			new TwigFunction('is_action', [$this, 'is_action']),
			new TwigFunction('avatar', [$this, 'avatar']),
		];
	}

	/**
	 * Token的解析器 拓展.
	 * @return array|TokenParserInterface[]
	 */
	public function getTokenParsers()
	{
		return [];
	}

	/**
	 * 节点访问器 拓展.
	 * @return array|NodeVisitorInterface[]
	 */
	public function getNodeVisitors()
	{
		return [];
	}

	/**
	 * 测试 拓展.
	 *
	 * @return Twig_Test[]
	 */
	public function getTests()
	{
		return [];
	}

	/**
	 * 运算符 拓展.
	 * @return array<array> First array of unary operators, second array of binary operators
	 */
	public function getOperators()
	{
		return [];
	}

	public function load($path = '')
	{

		$path = '/static/' . $path;

		return $path;
	}

	public function is_controller($name = '', $select = '')
	{
		if (strtolower(Request()->controller()) == strtolower($name)) {
			return $select;
		}
		return '';
	}

	public function is_action($name = '', $select = '')
	{
		if (strtolower(Request()->action()) == strtolower($name)) {
			return $select;
		}
		return '';
	}

	public function avatar($path = '', $gender = 0)
	{
		if (strtolower(substr($path, 0, 4)) != 'http') {
			if (file_exists(public_path('/public/upload/') . $path) && !empty($path)) {
				$path = '/upload/' . $path;
			} else {
				if (empty($gender)) {
					$path = '/static/rain/image/Unknown.jpg';
				} elseif ($gender == 1) {
					$path = '/static/rain/image/male.png';
				} elseif ($gender == 2) {
					$path = '/static/rain/image/female.png';
				}
			}
		}
		return $path;
	}

	public function getCity($ip = '')
	{
		$res = '未知';
		if (!empty($ip)) {
			$data = getCity($ip);
			if ($data['isp_id'] == 'local') {
				$res = $data['isp'];
			} else {
				$res = $data['country'] . $data['area'] . $data['region'] . $data['city'] . $data['county'] . $data['isp'];
			}
		}
		return $res;
	}
}
