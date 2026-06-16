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
 require_once dirname(__FILE__) . '/../CommandImpl.php'; class FileMaker_Command_FindRequest_Implementation extends FileMaker_Command_Implementation
{ var $_findCriteria = array(); var $V14e415e9; function FileMaker_Command_FindRequest_Implementation()
 { $this->V14e415e9= false; } function addFindCriterion($Vd1148ee8, $Ve9de89b0) { $this->_findCriteria[$Vd1148ee8] = $Ve9de89b0;
} function setOmit($V2063c160) { $this->V14e415e9= $V2063c160; } function clearFindCriteria() {
 $this->_findCriteria = array(); } } 