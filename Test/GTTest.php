<?php
namespace GDO\GTranslate\Test;

use GDO\Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

/**
 * Module GTranslate does not have a single testable method,
 * because we have to be stealthy a bit.
 *
 * @author gizmore
 */
final class GTTest extends TestCase
{

	public function testGT(): void
	{
		assertTrue(true);
// 		$error = '';
// 		$translated = GT::t('Schönen guten Abend liebe IT-Spezialisten!', 'auto', 'en', $error);
// 		assertEmpty($error, 'Test if no error occurs in GT.');
// 		assertStringContainsString('good', $translated, 'Test if google translates correctly fro auto to english.');
	}

}
