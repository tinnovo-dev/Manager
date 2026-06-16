<?php
/**
 * FileMaker PHP API.
 *
 * @access private
 * @package FileMaker
 *
 * Copyright © 2005-2006, FileMaker, Inc. All rights reserved.
 * NOTE: Use of this source code is subject to the terms of the FileMaker
 * Software License which accompanies the code. Your use of this source code
 * signifies your agreement to such license terms and conditions. Except as
 * expressly granted in the Software License, no other copyright, patent, or
 * other intellectual property license or right is granted, either expressly or
 * by implication, by FileMaker.
 */
 require_once dirname(__FILE__) . '/../Field.php'; require_once dirname(__FILE__) . '/Parser/FMPXMLLAYOUT.php';
 class FileMaker_Layout_Implementation { var $_fm; var $_name; var $_fields = array(); var $_relatedSets = array();
 var $_valueLists = array(); var $_database; var $_extended = false; function FileMaker_Layout_Implementation(&$V0ab34ca9)
 { $this->_fm =& $V0ab34ca9; } function getName() { return $this->_name; } function getDatabase()
 { return $this->_database; } function listFields() { return array_keys($this->_fields); } function &getField($V972bf3f0)
 { if (isset($this->_fields[$V972bf3f0])) { return $this->_fields[$V972bf3f0]; } return $Vcb5e100e =& new FileMaker_Error($this->_fm, 'Field Not Found');
} function &getFields() { return $this->_fields; } function listRelatedSets() { return array_keys($this->_relatedSets);
} function &getRelatedSet($Vaca007a7) { if (isset($this->_relatedSets[$Vaca007a7])) { return $this->_relatedSets[$Vaca007a7];
} return $Vcb5e100e =& new FileMaker_Error($this->_fm, 'RelatedSet Not Found'); } function &getRelatedSets()
 { return $this->_relatedSets; } function listValueLists() { $Vb4a88417 = $this->loadExtendedInfo();
if (FileMaker::isError($Vb4a88417)) { return $Vb4a88417; } return array_keys($this->_valueLists); }
 function getValueList($V993fcb1e) { $Vb4a88417 = $this->loadExtendedInfo(); if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417; } return isset($this->_valueLists[$V993fcb1e]) ? $this->_valueLists[$V993fcb1e] : null;
} function getValueLists() { $Vb4a88417 = $this->loadExtendedInfo(); if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417; } return $this->_valueLists; } function loadExtendedInfo() { if (!$this->_extended) {
 $V0f635d0e = $this->_fm->_execute(array('-db' => $this->_fm->getProperty('database'), '-lay' => $this->getName(),
 '-view' => null), 'FMPXMLLAYOUT'); $V3643b863 =& new FileMaker_Parser_FMPXMLLAYOUT($this->_fm); $Vb4a88417 = $V3643b863->parse($V0f635d0e);
if (FileMaker::isError($Vb4a88417)) { return $Vb4a88417; } $V3643b863->setExtendedInfo($this); $this->_extended = true;
} return $this->_extended; } } 