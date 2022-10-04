<?php
namespace GDO\GTranslate\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;

/**
 * Add a single translation to all supported languages, auto translated. It is recommended to use german over english for utilitzing this method, as it has more specific nouns*esses.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class AddTrans extends MethodForm
{
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_AntiCSRF::make(),
		);
		$form->addFields(GDT_Submit::make());
	}
	
}
