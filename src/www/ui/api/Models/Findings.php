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
<<<<<<< HEAD
   * @var array $copyright
   * List of copyright
   */
  private $copyright;

  /**
=======
>>>>>>> d3ebbda52 (feat(rest): Get file info from hash)
   * Findings constructor.
   *
   * @param array $scanner    Licenses found by scanners
   * @param array $conclusion Licenses concluded by users
<<<<<<< HEAD
   * @param array $copyright  Copyright for the file
   */
  public function __construct($scanner = null, $conclusion = null, $copyright = null)
  {
    $this->setScanner($scanner);
    $this->setConclusion($conclusion);
    $this->setCopyright($copyright);
=======
   */
  public function __construct($scanner = null, $conclusion = null)
  {
    $this->setScanner($scanner);
    $this->setConclusion($conclusion);
>>>>>>> d3ebbda52 (feat(rest): Get file info from hash)
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
<<<<<<< HEAD
   * @return array
   */
  public function getCopyright()
  {
    return $this->copyright;
  }

  /**
=======
>>>>>>> d3ebbda52 (feat(rest): Get file info from hash)
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
<<<<<<< HEAD
   * @param array $copyrights
   */
  public function setCopyright($copyright)
  {
    if (is_array($copyright)) {
      $this->copyright = $copyright;
    } elseif (is_string($copyright)) {
      $this->copyright = [$copyright];
    } elseif ($copyright === null && empty($this->copyright)) {
      $this->copyright = null;
    }
  }

  /**
=======
>>>>>>> d3ebbda52 (feat(rest): Get file info from hash)
   * Get the object as associative array
   *
   * @return array
   */
  public function getArray()
  {
    return [
      'scanner'     => $this->getScanner(),
<<<<<<< HEAD
      'conclusion'  => $this->getConclusion(),
      'copyright'  => $this->getCopyright()
=======
      'conclusion'  => $this->getConclusion()
>>>>>>> d3ebbda52 (feat(rest): Get file info from hash)
    ];
  }
}
