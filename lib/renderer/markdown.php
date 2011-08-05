<?php
/**
 * Markdown Renderer
 *
 * Support for rendering content with Markdown Extra and Smartypants.
 *
 * @package Gir-CMS
 * @author Jeffory <jeffory@c0d.in>
 **/

require('lib/shared/markdown.php');
require('lib/shared/smartypants.php');
 
class MarkdownRenderer
{	
	/**
	 * Renders content using both Markdown Extra and Smartypants.
	 *
	 * @return string	rendered content
	 * @access public
	 **/
	public function renderContent($content)
	{
		$markdownParser = new MarkdownExtra_Parser;
		
		$renderFixes = array(
					'/^(#+)/m' => '$1#',	// Take the headings down a step, as H1 is usually the main website heading
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