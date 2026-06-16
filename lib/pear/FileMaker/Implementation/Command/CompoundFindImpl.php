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
 require_once dirname(__FILE__) . '/../CommandImpl.php'; class FileMaker_Command_CompoundFind_Implementation extends FileMaker_Command_Implementation
{ var $_findCriteria = array(); var $Vd65662c5 = array(); var $Va9136a07 = array(); var $V83f28691;
 var $V85fd701e; var $Vad2bfd5a = array(); function FileMaker_Command_CompoundFind_Implementation($V0ab34ca9, $Vc6140495)
 { FileMaker_Command_Implementation::FileMaker_Command_Implementation($V0ab34ca9, $Vc6140495); } function &execute()
 { $V090cbceb= null; $V8ac10dab = 0; $V31c3c8cf = 0; $V40677621 = 1; $Ve2942a04 = 1; $V21ffce5b = $this->_getCommandParams(); 
 $this->_setSortParams($V21ffce5b); $this->_setRangeParams($V21ffce5b); ksort($this->Vad2bfd5a); 
 $V31c3c8cf=count($this->Vad2bfd5a); foreach ($this->Vad2bfd5a as $V70a17ffa => $V9a7aa128) { 
 $V15c46c6e = $V9a7aa128->_impl->_findCriteria; $V8ac10dab = count($V15c46c6e); if($V8ac10dab > 1){
 $V090cbceb = $V090cbceb.'('; } foreach ($V15c46c6e as $Vd1148ee8 => $Ve9de89b0) { $V21ffce5b['-q'.$Ve2942a04] = $Vd1148ee8;
$V21ffce5b['-q'.$Ve2942a04.'.'."value"] = $Ve9de89b0; $V090cbceb=$V090cbceb.'q'.$Ve2942a04; $Ve2942a04++; 
 if($Ve2942a04 <= $V8ac10dab){ $V090cbceb = $V090cbceb.','; } } if($V8ac10dab >1 ){ $V090cbceb=$V090cbceb.")";
} $V40677621++; if($V40677621 <= $V31c3c8cf){ $V4b22ce92 = $this->Vad2bfd5a[$V40677621]; if($V4b22ce92->_impl->_omit == true){
 $V090cbceb = $V090cbceb.'-'; }else{ $V090cbceb = $V090cbceb.'+'; } } } $V21ffce5b['-query'] = $V090cbceb; 
 $V21ffce5b['-findquery'] = true; $V0f635d0e = $this->_fm->_execute($V21ffce5b); if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e; } return $this->_getResult($V0f635d0e); } function add($Vffbd028a, $Vd0dff0df)
 { $this->Vad2bfd5a[$Vffbd028a] = $Vd0dff0df; } function addSortRule($Vd1148ee8, $Vffbd028a, $V70a17ffa = null)
 { $this->Vd65662c5[$Vffbd028a] = $Vd1148ee8; if ($V70a17ffa !== null) { $this->Va9136a07[$Vffbd028a] = $V70a17ffa;
} } function clearSortRules() { $this->Vd65662c5= array(); $this->Va9136a07= array(); } function setRange($V08b43519 = 0, $V2ffe4e77 = null)
 { $this->V83f28691= $V08b43519; $this->V85fd701e= $V2ffe4e77; } function getRange() { return array('skip' => $this->V83f28691,
 'max' => $this->V85fd701e); } function _setSortParams(&$V21ffce5b) { foreach ($this->Vd65662c5 as $Vffbd028a => $Vd1148ee8) {
 $V21ffce5b['-sortfield.' . $Vffbd028a] = $Vd1148ee8; } foreach ($this->Va9136a07 as $Vffbd028a => $V70a17ffa) {
 $V21ffce5b['-sortorder.' . $Vffbd028a] = $V70a17ffa; } } function _setRangeParams(&$V21ffce5b) {
 if ($this->V83f28691) { $V21ffce5b['-skip'] = $this->V83f28691; } if ($this->V85fd701e) { $V21ffce5b['-max'] = $this->V85fd701e;
} } } 