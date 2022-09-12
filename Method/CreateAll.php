<?php
namespace GDO\GTranslate\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\Language\GDT_Language;
use GDO\Core\GDT_Char;

final class CreateAll extends MethodForm
{
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			GDT_Language::make('from')->notNull(),
			GDT_Language::make('from')->notNull()->all(),
			GDT_Char::make('as_iso')->notNull(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form)
	{
		
	}
	
}
