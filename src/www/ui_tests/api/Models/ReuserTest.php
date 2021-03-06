<?php
/***************************************************************
 * Copyright (C) 2020 Siemens AG
 * Author: Gaurav Mishra <mishra.gaurav@siemens.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***************************************************************/
/**
 * @file
 * @brief Tests for Reueser model
 */

namespace Fossology\UI\Api\Test\Models;

use Fossology\UI\Api\Models\Reuser;

/**
 * @class ReuserTest
 * @brief Tests for Reuser model
 */
class ReuserTest extends \PHPUnit\Framework\TestCase
{
  /**
   * @test
   * -# Test constructor and Reuser::getArray()
   */
  public function testReuserConst()
  {
    $expectedArray = [
      "reuse_upload"   => 2,
      "reuse_group"    => 'fossy',
      "reuse_main"     => true,
      "reuse_enhanced" => false
    ];

    $actualReuser = new Reuser(2, 'fossy', true);

    $this->assertEquals($expectedArray, $actualReuser->getArray());
  }

  /**
   * @test
   * -# Test if UnexpectedValueException is thrown for invalid upload and group
   *    id by constructor
   */
  public function testReuserException()
  {
    $this->expectException(\UnexpectedValueException::class);
    $this->expectExceptionMessage("reuse_upload should be integer");
    $object = new Reuser('alpha', 2);
  }

  /**
   * @test
   * -# Test for Reuser::setUsingArray()
   */
  public function testSetUsingArray()
  {
    $expectedArray = [
      "reuse_upload"   => 2,
      "reuse_group"    => 'fossy',
      "reuse_main"     => 'true',
      "reuse_enhanced" => false
    ];

    $actualReuser = new Reuser(1, 'fossy');
    $actualReuser->setUsingArray($expectedArray);

    $expectedArray["reuse_main"] = true;
    $this->assertEquals($expectedArray, $actualReuser->getArray());
  }

  /**
   * @test
   * -# Test if UnexpectedValueException is thrown for invalid upload and group
   *    id by Reuser::setUsingArray()
   */
  public function testSetUsingArrayException()
  {
    $expectedArray = [
      "reuse_upload"   => 'alpha',
      "reuse_group"    => 'fossy',
      "reuse_main"     => 'true',
      "reuse_enhanced" => false
    ];

    $this->expectException(\UnexpectedValueException::class);
    $this->expectExceptionMessage("reuse_upload should be integer");

    $actualReuser = new Reuser(1, 'fossy');
    $actualReuser->setUsingArray($expectedArray);
  }
}
