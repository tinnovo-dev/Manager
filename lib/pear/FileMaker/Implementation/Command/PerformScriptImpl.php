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
 require_once dirname(__FILE__) . '/../CommandImpl.php'; class FileMaker_Command_PerformScript_Implementation extends FileMaker_Command_Implementation
{ function FileMaker_Command_PerformScript_Implementation($V0ab34ca9, $Vc6140495, $V2550889a, $V9b479e5e = null)
 { FileMaker_Command_Implementation::FileMaker_Command_Implementation($V0ab34ca9, $Vc6140495); $this->_script = $V2550889a;
$this->V9808b1bf= $V9b479e5e; } function execute() { $V21ffce5b = $this->_getCommandParams(); $V21ffce5b['-findany'] = true; 
 $V0f635d0e = $this->_fm->_execute($V21ffce5b); if (FileMaker::isError($V0f635d0e)) { return $V0f635d0e;
} return $this->_getResult($V0f635d0e); } } 