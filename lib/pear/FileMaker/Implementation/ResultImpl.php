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
 class FileMaker_Result_Implementation { var $_fm; var $_layout; var $_records; var $_tableCount;
 var $_foundSetCount; var $_fetchCount; function FileMaker_Result_Implementation(&$V0ab34ca9) { $this->_fm = &$V0ab34ca9;
} function &getLayout() { return $this->_layout; } function &getRecords() { return $this->_records;
} function getFields() { return $this->_layout->listFields(); } function getRelatedSets() { return $this->_layout->listRelatedSets();
} function getTableRecordCount() { return $this->_tableCount; } function getFoundSetCount() { return $this->_foundSetCount;
} function getFetchCount() { return $this->_fetchCount; } } 