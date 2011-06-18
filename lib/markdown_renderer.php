<?php
/**
 * Markdown Renderer
 *
 * Support for rendering content with Markdown Extra and Smartypants.
 *
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

require('shared/markdown.php');
require('shared/smartypants.php');
 
class MarkdownRenderer
{	
	/**
	 * renderContent
	 * Renders content with Markdown Extra and Smartypants.
	 *
	 * @return string	rendered content
	 **/
	
	function renderContent($content)
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