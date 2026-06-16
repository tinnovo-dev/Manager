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
 require_once dirname(__FILE__) . '/../Error/Validation.php'; class FileMaker_Field_Implementation
{ var $_layout; var $_name; var $_autoEntered = false; var $_global = false; var $_maxRepeat = 1;
 var $_validationMask = 0; var $_validationRules = array(); var $_result; var $_type; var $_valueList = null;
 var $V58631db1; function FileMaker_Field_Implementation(&$Vc6140495) { $this->_layout =& $Vc6140495;
} function getName() { return $this->_name; } function &getLayout() { return $this->_layout; }
 function isAutoEntered() { return $this->_autoEntered; } function isGlobal() { return $this->_global;
} function getRepetitionCount() { return $this->_maxRepeat; } function validate($V2063c160, $Vcb5e100e = null)
 { $V1c0c74f6 = true; if ($Vcb5e100e === null) { $V1c0c74f6 = false; $Vcb5e100e =& new FileMaker_Error_Validation($this->_layout->_impl->_fm);
} foreach ($this->getValidationRules() as $V981c1e7b) { switch ($V981c1e7b) { case FILEMAKER_RULE_NOTEMPTY:
 if (empty($V2063c160)) { $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160); } break; } } if ($V1c0c74f6) {
 return $Vcb5e100e; } else { return $Vcb5e100e->numErrors() ? $Vcb5e100e : true; } } function getLocalValidationRules()
 { $V6b55d9ec = array(); foreach (array_keys($this->_validationRules) as $V981c1e7b) { switch ($V981c1e7b) {
 case FILEMAKER_RULE_NOTEMPTY: $V6b55d9ec[] = $V981c1e7b; break; } } return $V6b55d9ec; } function getValidationRules()
 { return array_keys($this->_validationRules); } function getValidationMask() { return $this->_validationMask;
} function hasValidationRule($Ve289cc97) { return $Ve289cc97 & $this->_validationMask; } function describeValidationRule($Ve289cc97)
 { if (is_array($this->_validationRules[$Ve289cc97])) { return $this->_validationRules[$Ve289cc97];
} return null; } function describeLocalValidationRules() { $V6b55d9ec = array(); foreach ($this->_validationRules as $V981c1e7b => $V1dee80c7) {
 switch ($V981c1e7b) { case FILEMAKER_RULE_NOTEMPTY: $V6b55d9ec[$V981c1e7b] = $V1dee80c7; break; }
} return $V6b55d9ec; } function describeValidationRules() { return $this->_validationRules; } function getResult()
 { return $this->_result; } function getType() { return $this->_type; } function getValueList()
 { $Vb4a88417 = $this->_layout->loadExtendedInfo(); if (FileMaker::isError($Vb4a88417)) { return $Vb4a88417;
} return $this->_layout->getValueList($this->_valueList); } function getStyleType() { $Vb4a88417 = $this->_layout->loadExtendedInfo();
if (FileMaker::isError($Vb4a88417)) { return $Vb4a88417; } return $this->V58631db1; } } 