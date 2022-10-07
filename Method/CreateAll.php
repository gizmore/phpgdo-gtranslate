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

/**
 * Create an automated language pack with google translate.
 * @deprecated Use the official API
 * @author gizmore
 * @since 7.0.1
 */
final class CreateAll extends MethodForm
{
	use MethodAdmin;
	
	public function isTrivial(): bool
	{
		return false;
	}
	
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
		$create = Create::make();
		foreach (Trans::$PATHS as $path)
		{
			$create->translateLangFile($path, $from, $to, $iso);
		}
	}
	
}
