<?php
	require_once('shared/markdown.php');
	require_once('shared/smartypants.php');
	
	class Markdown
	{
		function __construct($content)
		{
			if (!empty($content))
				$this->render($content);
		}
		
		function render($content)
		{
			$markdownParser = new MarkdownExtra_Parser;
			
			$renderFixes = array(
						'/^(#+)/m' => '$1#',	// Take the headings down a step, H1 is the main website heading :)
						'/<(\/|)s>/m' => '<$1del>',
					);
			
			foreach ($renderFixes as $pattern => $replacement)
			{
				$content = preg_replace($pattern, $replacement, $content);
			}
			
			$content = $markdownParser->transform($content);
			$content = SmartyPants($content);
			
			return $content;
		}
	}