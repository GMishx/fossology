<?php
/*
Copyright (C) 2014, Siemens AG

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

namespace Fossology\Lib\Test;

use Fossology\Lib\Data\Types;

// PHP unit 7 compatibility
if (class_exists('\PHPUnit\Framework\TestCase') && !class_exists('\PHPUnit_Framework_TestCase')) {
  class_alias('PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}

class EnumMapTestBase extends \PHPUnit_Framework_TestCase {

  /** @var Types */
  private $types;

  /**
   * @param Types $types
   */
  protected function setTypes($types) {
    $this->types = $types;
  }

  /**
   * @param int $type
   * @param string $expectedTypeName
   * @throws \Exception
   */
  protected function checkMapping($type, $expectedTypeName) {
    $typeName = $this->types->getTypeName($type);

    assertThat($typeName, is($expectedTypeName));
    assertThat($this->types->getTypeByName($typeName), is($type));
  }
}