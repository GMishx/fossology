<?php
/***************************************************************
 * Copyright (C) 2020 Siemens AG
 *
 * SPDX-License-Identifier: OSL-3.0
 ***************************************************************/
/**
 * @file
 * @brief Findings model
 */
namespace Fossology\UI\Api\Models;

/**
 * @class Findings
 * @brief Model holding information about license findings and conclusions
 */
class Findings
{

  /**
   * @var array $scanner
   * List of scanner findings
   */
  private $scanner;

  /**
   * @var array $conclusion
   * List of user conclusions
   */
  private $conclusion;

  /**
   * Findings constructor.
   *
   * @param array $scanner    Licenses found by scanners
   * @param array $conclusion Licenses concluded by users
   */
  public function __construct($scanner = null, $conclusion = null)
  {
    $this->setScanner($scanner);
    $this->setConclusion($conclusion);
  }

  /**
   * @return array
   */
  public function getScanner()
  {
    return $this->scanner;
  }

  /**
   * @return array
   */
  public function getConclusion()
  {
    return $this->conclusion;
  }

  /**
   * @param array $scanner
   */
  public function setScanner($scanner)
  {
    if (is_array($scanner)) {
      $this->scanner = $scanner;
    } elseif (is_string($scanner)) {
      $this->scanner = [$scanner];
    } elseif ($scanner === null && empty($this->scanner)) {
      $this->scanner = null;
    }
  }

  /**
   * @param array $conclusion
   */
  public function setConclusion($conclusion)
  {
    if (is_array($conclusion)) {
      $this->conclusion = $conclusion;
    } elseif (is_string($conclusion)) {
      $this->conclusion = [$conclusion];
    } elseif ($conclusion === null && empty($this->conclusion)) {
      $this->conclusion = null;
    }
  }

  /**
   * Get the object as associative array
   *
   * @return array
   */
  public function getArray()
  {
    return [
      'scanner'     => $this->getScanner(),
      'conclusion'  => $this->getConclusion()
    ];
  }
}
