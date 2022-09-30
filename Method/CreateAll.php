<?php
namespace GDO\GTranslate\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\Language\GDT_Language;
use GDO\Core\GDT_Char;
use PhpMyAdmin\Plugins\Transformations\Text_Plain_Link;
use GDO\Language\Trans;
use GDO\Admin\MethodAdmin;
use GDO\Util\FileUtil;
use GDO\GTranslate\GT;

/**
 * Create an automated language pack with google translate.
 * @deprecated Use the official API
 * @author gizmore
 * @since 7.0.1
 */
final class CreateAll extends MethodForm
{
	use MethodAdmin;
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
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
		foreach (Trans::$PATHS as $path)
		{
			$this->translateLangFile($path, $from, $to, $iso);
		}
	}
	
	public function translateLangFile(string $path, string $from, string $to, string $iso) : void
	{
		$fromPath = $path . "_{$from}.php";
		$toPath = $path . "_{$iso}.php";

		if (FileUtil::isFile($toPath))
		{
			$this->message('msg_gt_skip_file_exist', [html($fromPath)]);
		}

		$text = file_get_contents($fromPath);
		$translated = GT::t($text, $from, $to);
		file_put_contents($toPath, $translated);
		$this->message('msg_gt_translated', [html($path), $from, $to, $iso]);
	}
	
}
