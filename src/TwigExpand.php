<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection,SenselessMethodDuplicationInspection */

/**
 * 工具: PhpStorm
 * 作者: 孙家浩
 * 邮箱: yulian1024@vip.qq.com
 * 日期: 2020/3/25
 * 时间: 20:01
 * 侵权必究
 */


use Twig\Extension\AbstractExtension;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\TwigTest;

class TwigExpand extends AbstractExtension
{
	/**
	 * 过滤器 拓展.
	 * @return TwigFilter[]
	 */
	public function getFilters()
	{
		return [];
	}

	/**
	 * 函数 拓展.
	 * @return TwigFunction[]
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('css', [$this, 'load'], ['is_safe' => ['all']]),
			new TwigFunction('js', [$this, 'load'], ['is_safe' => ['all']]),
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
	 * @return array|TwigTest[]
	 */
	public function getTests()
	{
		return [
		];
	}

	/**
	 * 运算符 拓展.
	 * @return array<array> First array of unary operators, second array of binary operators
	 */
	public function getOperators()
	{
		return [
		];
	}

	protected function load(...$paths): string
	{
		$path = '';
		foreach ($paths as $item) {
			$suffix = substr($item, -4);
			if ($suffix === '.css') {
				$path .= "<link rel=\"stylesheet\" href=\"/static/{$item}\">";
			}
			$suffix = substr($item, -3);
			if ($suffix === '.js') {
				$path .= "<script src=\"/static/{$item}\"></script>";
			}
		}

		return $path;
	}
}