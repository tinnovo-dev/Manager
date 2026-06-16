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
 require_once dirname(__FILE__) . '/../CommandImpl.php'; class FileMaker_Command_Find_Implementation extends FileMaker_Command_Implementation
{ var $_findCriteria = array(); var $Vd65662c5 = array(); var $Va9136a07 = array(); var $Vf951bdce;
 var $V83f28691; var $V85fd701e; function FileMaker_Command_Find_Implementation($V0ab34ca9, $Vc6140495)
 { FileMaker_Command_Implementation::FileMaker_Command_Implementation($V0ab34ca9, $Vc6140495); } function &execute()
 { $V21ffce5b = $this->_getCommandParams(); $this->_setSortParams($V21ffce5b); $this->_setRangeParams($V21ffce5b); 
 if (count($this->_findCriteria) || $this->_recordId) { $V21ffce5b['-find'] = true; } else { $V21ffce5b['-findall'] = true;
} if ($this->_recordId) { $V21ffce5b['-recid'] = $this->_recordId; } if ($this->Vf951bdce) { $V21ffce5b['-lop'] = $this->Vf951bdce;
} foreach ($this->_findCriteria as $Vd1148ee8 => $Ve9de89b0) { $V21ffce5b[$Vd1148ee8] = $Ve9de89b0;
$V21ffce5b[$Vd1148ee8 . '.op'] = 'bw'; } $V0f635d0e = $this->_fm->_execute($V21ffce5b); if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e; } return $this->_getResult($V0f635d0e); } function addFindCriterion($Vd1148ee8, $Ve9de89b0)
 { $this->_findCriteria[$Vd1148ee8] = $Ve9de89b0; } function clearFindCriteria() { $this->_findCriteria = array();
} function addSortRule($Vd1148ee8, $Vffbd028a, $V70a17ffa = null) { $this->Vd65662c5[$Vffbd028a] = $Vd1148ee8;
if ($V70a17ffa !== null) { $this->Va9136a07[$Vffbd028a] = $V70a17ffa; } } function clearSortRules()
 { $this->Vd65662c5= array(); $this->Va9136a07= array(); } function setLogicalOperator($V4b583376)
 { switch ($V4b583376) { case FILEMAKER_FIND_AND: case FILEMAKER_FIND_OR: $this->Vf951bdce= $V4b583376;
break; } } function setRange($V08b43519 = 0, $V2ffe4e77 = null) { $this->V83f28691= $V08b43519; $this->V85fd701e= $V2ffe4e77;
} function getRange() { return array('skip' => $this->V83f28691, 'max' => $this->V85fd701e); } function _setSortParams(&$V21ffce5b)
 { foreach ($this->Vd65662c5 as $Vffbd028a => $Vd1148ee8) { $V21ffce5b['-sortfield.' . $Vffbd028a] = $Vd1148ee8;
} foreach ($this->Va9136a07 as $Vffbd028a => $V70a17ffa) { $V21ffce5b['-sortorder.' . $Vffbd028a] = $V70a17ffa;
} } function _setRangeParams(&$V21ffce5b) { if ($this->V83f28691) { $V21ffce5b['-skip'] = $this->V83f28691;
} if ($this->V85fd701e) { $V21ffce5b['-max'] = $this->V85fd701e; } } } 