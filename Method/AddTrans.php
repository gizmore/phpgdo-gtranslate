<?php
namespace GDO\GTranslate\Method;

use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Add a single translation to all supported languages, auto translated. It is recommended to use german over english for utilitzing this method, as it has more specific nouns*esses.
 *
 * @since 7.0.1
 * @author gizmore
 */
final class AddTrans extends MethodForm
{

	public function isTrivial(): bool
	{
		return false;
	}

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(

			GDT_AntiCSRF::make(),
		);
		$form->addFields(GDT_Submit::make());
	}

}
