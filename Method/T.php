<?php
namespace GDO\GTranslate\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\GTranslate\GT;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\Language\GDT_Language;
use GDO\Core\GDT_String;
use GDO\User\GDO_User;

/**
 * Translate a string with google translate.
 * Defaults source language to auto.
 * Defaults target language to user language.
 * 
 * @author gizmore
 * @TODO make use of the official gtranslate API.
 */
final class T extends MethodForm
{
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			GDT_Language::make('from')->all(),
			GDT_Language::make('to')->all(),
			GDT_String::make('text')->notNull()->max(1800),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addFields(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$from = $this->getLangFrom();
		$to = $this->getLangTo();
		$text = $this->gdoParameterVar('text');
		$translated = GT::t($text, $from, $to);
		$this->message('msg_translation', [$translated]);
	}

	private function getLangFrom() : string
	{
		if ($from = $this->gdoParameterVar('from'))
		{
			return $from;
		}
		return 'auto';
	}
	
	private function getLangTo() : string
	{
		if ($to = $this->gdoParameterVar('to'))
		{
			return $to;
		}
		return GDO_User::current()->getLangISO();
	}
	
}