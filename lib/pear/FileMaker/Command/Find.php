<?php
/**
 * FileMaker PHP API.
 *
 * @package FileMaker
 *
 * Copyright � 2005-2006, FileMaker, Inc.� All rights reserved.
 * NOTE:� Use of this source code is subject to the terms of the FileMaker
 * Software License which accompanies the code.� Your use of this source code
 * signifies your agreement to such license terms and conditions.� Except as
 * expressly granted in the Software License, no other copyright, patent, or
 * other intellectual property license or right is granted, either expressly or
 * by implication, by FileMaker.
 */

/**
 * Include parent and delegate classesa.
 */
require_once dirname(__FILE__) . '/../Command.php';
require_once dirname(__FILE__) . '/../Implementation/Command/FindImpl.php';

/**
 * Find records.
 *
 * @package FileMaker
 */
class FileMaker_Command_Find extends FileMaker_Command
{
    /**
     * Implementation
     *
     * @var FileMaker_Command_Find_Implementation
     * @access private
     */
    var $_impl;

    /**
     * Find command constructor.
     *
     * @param FileMaker_Implementation $fm The FileMaker_Implementation object the command was created by.
     * @param string $layout The layout to find records in.
     */
    function FileMaker_Command_Find($fm, $layout)
    {
        $this->_impl =& new FileMaker_Command_Find_Implementation($fm, $layout);
    }

    /**
     * Add a find criterion.
     *
     * @param string $fieldname The field being tested.
     * @param string $testvalue The value to test against.
     */
    function addFindCriterion($fieldname, $testvalue)
    {
        $this->_impl->addFindCriterion($fieldname, $testvalue);
    }

    /**
     * Clear all existing find criteria.
     */
    function clearFindCriteria()
    {
        $this->_impl->clearFindCriteria();
    }

    /**
     * Add a sorting rule to the find command.
     *
     * @param string $fieldname The field to sort by.
     * @param integer $precedence 1-9, should we sort by this field first, last, etc.
     * @param mixed $order FILEMAKER_SORT_ASCEND, FILEMAKER_SORT_DESCEND, or a custom value list.
     */
    function addSortRule($fieldname, $precedence, $order = null)
    {
        $this->_impl->addSortRule($fieldname, $precedence, $order);
    }

    /**
     * Clear all existing sorting rules.
     */
    function clearSortRules()
    {
        $this->_impl->clearSortRules();
    }

    /**
     * Switch between AND and OR searches.
     *
     * @param integer $operator FILEMAKER_FIND_AND or FILEMAKER_FIND_OR.
     */
    function setLogicalOperator($operator)
    {
        $this->_impl->setLogicalOperator($operator);
    }

    /**
     * Request only part of the result set.
     *
     * @param integer $skip The number of records to skip past.
     * @param integer $max The maximum number of records to return.
     */
    function setRange($skip = 0, $max = null)
    {
        $this->_impl->setRange($skip, $max);
    }

    /**
     * Return the current range settings.
     *
     * @return array An associative array with two keys: 'skip' for
     * the current skip setting, and 'max' for the current maximum
     * number of records. If either key does not have a value it will
     * be set to NULL.
     */
    function getRange()
    {
        return $this->_impl->getRange();
    }

}
