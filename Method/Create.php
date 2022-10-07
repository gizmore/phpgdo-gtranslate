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

		# tpl/
		$ptrn = "/_{$from}\\.php$/";
		$path = $module->filePath('tpl');
		Filewalker::traverse($path, $ptrn, [$this, 'translateTemplateFile']);
	
		# thm/
		$path = $module->filePath('thm');
		Filewalker::traverse($path, $ptrn, [$this, 'translateTemplateFile']);
	}
	
	public function translateTemplateFile(string $entry, string $fullpath, $args=null) : void
	{
		$from = $this->gdoParameterVar('from');
		$to = $this->gdoParameterVar('to');
		$iso = $this->gdoParameterVar('as_iso');
		if ($path = Strings::substrTo($fullpath, "_{$from}.php"))
		{
			$this->translateLangFile($path, $from, $to, $iso);
		}
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
		$translated = self::composeTranslationForLangFile($translated);
		file_put_contents($toPath, $translated);
		return $this->message('msg_gt_translated', [html($path), $from, $to, $iso]);
	}
	
	protected static function composeTranslationForLangFile(string $result) : string
	{
		$s = '';
		$t = json_decode($result, true);
		foreach ($t[0] as $tr)
		{
			$matchesSource = [];
			$matchesTarget = [];
			if (str_starts_with($tr[1], 'namespace GDO'))
			{
				$s .= $tr[1];
			}
			elseif (str_starts_with($tr[1], 'return '))
			{
				$s .= $tr[1];
			}
			elseif (preg_match('/^[\\s#\\/]{2,}/', $tr[1]))
			{
				# Comment adds extra whitespace
				$s .= "\t\n" . $tr[1];
			}
			elseif (
				(preg_match('/^\\s*[\'"]([^\'"]+)[\'"]\\s*=>\\s*(.*)/', $tr[0], $matchesTarget)) &&
				(preg_match('/^\\s*[\'"]([^\'"]+)[\'"]\\s*=>\\s*(.*)/', $tr[1], $matchesSource)))
			{
				$s .= sprintf("\t'%s' => %s", $matchesSource[1], $matchesTarget[2]);
			}
			else
			{
				$s .= $tr[0];
			}
		}
		return $s;
	}
	
}
