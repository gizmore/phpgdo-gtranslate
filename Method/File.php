<?php
namespace GDO\GTranslate\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Path;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\GTranslate\GT;
use GDO\Language\GDT_Language;

/**
 * Translate a file.
 *
 * @author gizmore
 */
final class File extends T
{

	public function isTrivial(): bool
	{
		return false;
	}

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Language::make('from')->all(),
			GDT_Language::make('to')->all(),
			GDT_Path::make('path')->existingFile()->notNull(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addFields(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$from = $this->getLangFrom();
		$to = $this->getLangTo();
		$path = $this->gdoParameterVar('path');
		$text = file_get_contents($path);
		$error = '';
		$translated = GT::t($text, $from, $to, $error);
		if ($error)
		{
			$this->error('err_translate', [$error]);
		}
		else
		{
			$translated = GT::composeTranslation($translated);
			$dest = $path .= '.' . $to;
			file_put_contents($dest, $translated);
			$this->message('msg_gt_translated', [$path, $from, $to]);
		}
	}

}
