<?php
namespace GDO\GTranslate\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT;
use GDO\Core\GDT_Char;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Language\GDT_Language;
use GDO\Language\Trans;

/**
 * Create an automated language pack with google translate.
 *
 * @since 7.0.1
 * @author gizmore
 * @deprecated Use the official API
 */
final class CreateAll extends MethodForm
{

	use MethodAdmin;

	public function isTrivial(): bool
	{
		return false;
	}

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Language::make('from')->notNull(),
			GDT_Language::make('to')->notNull()->all(),
			GDT_Char::make('as_iso')->length(2)->notNull(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form): GDT
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
