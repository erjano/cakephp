<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Controller\Component\Auth;

use Cake\Controller\Component\Auth\FallbackPasswordHasher;
use Cake\Controller\Component\Auth\SimplePasswordHasher;
use Cake\Controller\Component\Auth\WeakPasswordHasher;
use Cake\TestSuite\TestCase;

/**
 * Test case for FallbackPasswordHasher
 *
 */
class FallbackPasswordHasherTest extends TestCase {

/**
 * Tests that only the first hasher is user for hashing a password
 *
 * @return void
 */
	public function testHash() {
		$hasher = new FallbackPasswordHasher(['hashers' => ['Weak', 'Simple']]);
		$weak = new WeakPasswordHasher();
		$this->assertSame($weak->hash('foo'), $hasher->hash('foo'));

		$simple = new SimplePasswordHasher();
		$hasher = new FallbackPasswordHasher(['hashers' => ['Weak', 'Simple']]);
		$this->assertSame($weak->hash('foo'), $hasher->hash('foo'));
	}

/**
 * Tests that the check method will check with configured hashers until a match
 * is found
 *
 * @return void
 */
	public function testCheck() {
		$hasher = new FallbackPasswordHasher(['hashers' => ['Weak', 'Simple']]);
		$weak = new WeakPasswordHasher();
		$simple = new SimplePasswordHasher();

		$hash = $simple->hash('foo');
		$otherHash = $weak->hash('foo');
		$this->assertTrue($hasher->check('foo', $hash));
		$this->assertTrue($hasher->check('foo', $otherHash));
	}

/**
 * Tests that the password only needs to be re-built according to the first hasher
 *
 * @return void
 */
	public function testNeedsRehash() {
		$hasher = new FallbackPasswordHasher(['hashers' => ['Simple', 'Weak']]);
		$weak = new WeakPasswordHasher();
		$otherHash = $weak->hash('foo');
		$this->assertTrue($hasher->needsRehash($otherHash));

		$simple = new SimplePasswordHasher();
		$hash = $simple->hash('foo');
		$this->assertFalse($hasher->needsRehash($hash));
	}
}