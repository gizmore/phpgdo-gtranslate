<?php
namespace GDO\GTranslate\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\Language\GDT_Language;
use GDO\Core\GDT_Char;
use GDO\Language\Trans;
use GDO\Admin\MethodAdmin;
use GDO\Util\FileUtil;
use GDO\GTranslate\GT;
use GDO\Core\GDT_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDO_Module;
use GDO\Util\Filewalker;
use GDO\Util\Strings;

/**
 * Create an automated language pack with google translate.
 * 
 * @example gdo gt.create kass,en,de,de, 
 * @deprecated Use the official API
 * @author gizmore
 * @since 7.0.1
 */
final class Create extends MethodForm
{
	use MethodAdmin;
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			# optional
			GDT_Checkbox::make('force')->initial('0'),
			# positional
			GDT_Module::make('module')->notNull()->installed()->uninstalled(),
			GDT_Language::make('from')->notNull(),
			GDT_Language::make('to')->notNull()->all(),
			GDT_Char::make('as_iso')->length(2)->notNull(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$from = $this->gdoParameterVar('from');
		$to = $this->gdoParameterVar('to');
		$iso = $this->gdoParameterVar('as_iso');
		/** @var $module GDO_Module **/
		$module = $this->gdoParameterValue('module');
		$moduleName = $module->getModuleName();
		foreach (Trans::$PATHS as $path)
		{
			if (strpos($path, "/GDO/{$moduleName}") !== false)
			{
				$this->translateLangFile($path, $from, $to, $iso);
			}
		}
		
		$path = $module->filePath('tpl');
		$ptrn = "/_{$from}.php$/";
		Filewalker::traverse($path, $ptrn, [$this, 'translteTemplateFile']);
	}
	
	public function translteTemplateFile(string $entry, string $fullpath, $args=null) : void
	{
		$from = $this->gdoParameterVar('from');
		$to = $this->gdoParameterVar('to');
		$iso = $this->gdoParameterVar('as_iso');
		$path = Strings::rsubstrFrom($fullpath, "_{$from}.php");
		$this->translateLangFile($path, $from, $to, $iso);
	}
	
	public function translateLangFile(string $path, string $from, string $to, string $iso)
	{
		$fromPath = $path . "_{$from}.php";
		$toPath = $path . "_{$iso}.php";
		
		if (FileUtil::isFile($toPath) && (!$this->gdoParameterValue('force')))
		{
			return $this->message('msg_gt_skip_file_exist', [html($fromPath)]);
		}
		
		$text = file_get_contents($fromPath);
		$error = '';
		$translated = GT::t($text, $from, $to, $error);
		if ($error)
		{
			return $this->error('err_translate', [$error]);
		}
		$translated = GT::composeTranslation($translated);
		file_put_contents($toPath, $translated);
		return $this->message('msg_gt_translated', [html($path), $from, $to, $iso]);
	}
	
}
