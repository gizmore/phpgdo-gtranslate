<?php
namespace GDO\GTranslate;

use GDO\Core\GDO_Module;

final class Module_GTranslate extends GDO_Module
{
	public int $priority = 35;
	public string $license = 'MIT';
	
	############
	### Init ###
	############
	public function onLoadLanguage() : void
	{
		$this->loadLanguage('lang/gtrans');
	}
	
}
