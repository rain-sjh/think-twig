# think-twig
tp6 twig模板
	如需拓展twig模板函数,请在extend下创建 TwigExpand.php 并实现接口
	
	use Twig\Extension\AbstractExtension;
    use Twig\NodeVisitor\NodeVisitorInterface;
    use Twig\TokenParser\TokenParserInterface;
    use Twig\TwigFunction;
    use Twig\TwigFilter;

	class TwigExpand extends AbstractExtension
     {
     	/**
     	 * 过滤器 拓展.
     	 * @return TwigFilter[]
     	 */
     	public function getFilters()
     	{
     		return [
     		];
     	}
     
     	/**
     	 * 函数 拓展.
     	 * @return TwigFunction[]
     	 */
     	public function getFunctions()
     	{
     		return [
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
     }
