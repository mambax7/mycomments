<?php
//  Author: Trabis
//  URL: http://www.xuups.com
//  E-Mail: lusopoemas@gmail.com

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 *
 *
 * @package     kernel
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A Comment
 *
 * @package     kernel
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */
class MycommentsComment extends XoopsObject
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
        $this->initVar('com_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('com_pid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_modid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('com_icon', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('com_title', XOBJ_DTYPE_TXTBOX, null, true, 255, true);
        $this->initVar('com_text', XOBJ_DTYPE_TXTAREA, null, true, null, true);
        $this->initVar('com_created', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_modified', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_uid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('com_ip', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('com_sig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_itemid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_rootid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_status', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('com_exparams', XOBJ_DTYPE_OTHER, null, false, 255);
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('dobr', XOBJ_DTYPE_INT, 0, false);
        //yup, lets handle the module
        $this->initVar('mid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        $this->initVar('version', XOBJ_DTYPE_INT, 100, false);
        $this->initVar('last_update', XOBJ_DTYPE_INT, null, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('isactive', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dirname', XOBJ_DTYPE_OTHER, null, true);
        $this->initVar('hasmain', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasadmin', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hassearch', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasconfig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hascomments', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('hasnotification', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Is this comment on the root level?
     *
     * @return bool
     **/
    public function isRoot()
    {
        return ($this->getVar('com_id') == $this->getVar('com_rootid'));
    }
}

/**
 * XOOPS comment handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS comment class objects.
 *
 *
 * @package     kernel
 * @subpackage  comment
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */
class MycommentsCommentHandler extends XoopsObjectHandler
{
    /**
     * Create a {@link XoopsComment}
     *
     * @param bool $isNew Flag the object as "new"?
     *
     * @return object
     */
    public function &create($isNew = true)
    {
        $comment = new MycommentsComment();
        if ($isNew) {
            $comment->setNew();
        }

        return $comment;
    }

    /**
     * Retrieve a {@link XoopsComment}
     *
     * @param int $id ID
     *
     * @return object {@link XoopsComment}, FALSE on fail
     **/
    public function &get($id)
    {
        $comment = false;
        $id      = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('xoopscomments') . ' WHERE com_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $comment;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                $comment = new MycommentsComment();
                $comment->assignVars($this->db->fetchArray($result));
            }
        }

        return $comment;
    }

    /**
     * Write a comment to database
     *
     * @param object|XoopsObject $comment
     * @return bool
     */
    public function insert(XoopsObject $comment)
    {
        if ('mycommentscomment' !== strtolower(get_class($comment))) {
            return false;
        }
        if (!$comment->isDirty()) {
            return true;
        }
        if (!$comment->cleanVars()) {
            return false;
        }
        foreach ($comment->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($comment->isNew()) {
            $com_id = $this->db->genId('xoopscomments_com_id_seq');
            $sql    = sprintf(
                'INSERT INTO %s (com_id, com_pid, com_modid, com_icon, com_title, com_text, com_created, com_modified, com_uid, com_ip, com_sig, com_itemid, com_rootid, com_status, com_exparams, dohtml, dosmiley, doxcode, doimage, dobr) VALUES (%u, %u, %u, %s, %s, %s, %u, %u, %u, %s, %u, %u, %u, %u, %s, %u, %u, %u, %u, %u)',
                              $this->db->prefix('xoopscomments'),
                $com_id,
                $com_pid,
                $com_modid,
                $this->db->quoteString($com_icon),
                $this->db->quoteString($com_title),
                $this->db->quoteString($com_text),
                $com_created,
                $com_modified,
                $com_uid,
                $this->db->quoteString($com_ip),
                $com_sig,
                $com_itemid,
                              $com_rootid,
                $com_status,
                $this->db->quoteString($com_exparams),
                $dohtml,
                $dosmiley,
                $doxcode,
                $doimage,
                $dobr
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET com_pid = %u, com_icon = %s, com_title = %s, com_text = %s, com_created = %u, com_modified = %u, com_uid = %u, com_ip = %s, com_sig = %u, com_itemid = %u, com_rootid = %u, com_status = %u, com_exparams = %s, dohtml = %u, dosmiley = %u, doxcode = %u, doimage = %u, dobr = %u WHERE com_id = %u',
                           $this->db->prefix('xoopscomments'),
                $com_pid,
                $this->db->quoteString($com_icon),
                $this->db->quoteString($com_title),
                $this->db->quoteString($com_text),
                $com_created,
                $com_modified,
                $com_uid,
                $this->db->quoteString($com_ip),
                $com_sig,
                $com_itemid,
                $com_rootid,
                $com_status,
                           $this->db->quoteString($com_exparams),
                $dohtml,
                $dosmiley,
                $doxcode,
                $doimage,
                $dobr,
                $com_id
            );
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($com_id)) {
            $com_id = $this->db->getInsertId();
        }
        $comment->assignVar('com_id', $com_id);

        return true;
    }

    /**
     * Delete a {@link XoopsComment} from the database
     *
     * @param object|XoopsObject $comment
     * @return bool
     */
    public function delete(XoopsObject $comment)
    {
        if ('mycommentscomment' !== strtolower(get_class($comment))) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE com_id = %u', $this->db->prefix('xoopscomments'), $comment->getVar('com_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get some {@link XoopsComment}s
     *
     * @param object $criteria
     * @param bool   $id_as_key Use IDs as keys into the array?
     *
     * @return array Array of {@link XoopsComment} objects
     **/
    public function getObjects($criteria = null, $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT c.* ,m.* FROM ' . $this->db->prefix('xoopscomments') . ' c, ' . $this->db->prefix('modules') . ' m';
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql   .= ' ' . $criteria->renderWhere();
            $sql   .= ' AND c.com_modid = m.mid';
            $sort  = ('' != $criteria->getSort()) ? $criteria->getSort() : 'com_id';
            $sql   .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $comment = new MycommentsComment();
            $comment->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $comment;
            } else {
                $ret[$myrow['com_id']] =& $comment;
            }
            unset($comment);
        }

        return $ret;
    }

    /**
     * Count Comments
     *
     * @param object $criteria {@link CriteriaElement}
     *
     * @return int Count
     **/
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('xoopscomments');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result =& $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Delete multiple comments
     *
     * @param object $criteria {@link CriteriaElement}
     *
     * @return bool
     **/
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix('xoopscomments');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Get a list of comments
     *
     * @param object $criteria {@link CriteriaElement}
     *
     * @return array Array of raw database records
     **/
    public function getList($criteria = null)
    {
        $comments = $this->getObjects($criteria, true);
        $ret      = [];
        foreach (array_keys($comments) as $i) {
            $ret[$i] = $comments[$i]->getVar('com_title');
        }

        return $ret;
    }

    /**
     * Retrieves comments for an item
     *
     * @param int    $module_id Module ID
     * @param int    $item_id   Item ID
     * @param string $order     Sort order
     * @param int    $status    Status of the comment
     * @param int    $limit     Max num of comments to retrieve
     * @param int    $start     Start offset
     *
     * @return array Array of {@link XoopsComment} objects
     **/
    public function getByItemId($module_id, $item_id, $order = null, $status = null, $limit = null, $start = 0)
    {
        $criteria = new CriteriaCompo(new Criteria('com_modid', (int)$module_id));
        $criteria->add(new Criteria('com_itemid', (int)$item_id));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', (int)$status));
        }
        if (isset($order)) {
            $criteria->setOrder($order);
        }
        if (isset($limit)) {
            $criteria->setLimit($limit);
            $criteria->setStart($start);
        }

        return $this->getObjects($criteria);
    }

    /**
     * Gets total number of comments for an item
     *
     * @param int $module_id Module ID
     * @param int $item_id   Item ID
     * @param int $status    Status of the comment
     *
     * @return array Array of {@link XoopsComment} objects
     **/
    public function getCountByItemId($module_id, $item_id, $status = null)
    {
        $criteria = new CriteriaCompo(new Criteria('com_modid', (int)$module_id));
        $criteria->add(new Criteria('com_itemid', (int)$item_id));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', (int)$status));
        }

        return $this->getCount($criteria);
    }

    /**
     * Get the top {@link XoopsComment}s
     *
     * @param int    $module_id
     * @param int    $item_id
     * @param strint $order
     * @param int    $status
     *
     * @return array Array of {@link XoopsComment} objects
     **/
    public function getTopComments($module_id, $item_id, $order, $status = null)
    {
        $criteria = new CriteriaCompo(new Criteria('com_modid', (int)$module_id));
        $criteria->add(new Criteria('com_itemid', (int)$item_id));
        $criteria->add(new Criteria('com_pid', 0));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', (int)$status));
        }
        $criteria->setOrder($order);

        return $this->getObjects($criteria);
    }

    /**
     * Retrieve a whole thread
     *
     * @param int $comment_rootid
     * @param int $comment_id
     * @param int $status
     *
     * @return array Array of {@link XoopsComment} objects
     **/
    public function getThread($comment_rootid, $comment_id, $status = null)
    {
        $criteria = new CriteriaCompo(new Criteria('com_rootid', (int)$comment_rootid));
        $criteria->add(new Criteria('com_id', (int)$comment_id, '>='));
        if (isset($status)) {
            $criteria->add(new Criteria('com_status', (int)$status));
        }

        return $this->getObjects($criteria);
    }

    /**
     * Update
     *
     * @param object &$comment    {@link XoopsComment} object
     * @param string $field_name  Name of the field
     * @param mixed  $field_value Value to write
     *
     * @return bool
     **/
    public function updateByField(&$comment, $field_name, $field_value)
    {
        $comment->unsetNew();
        $comment->setVar($field_name, $field_value);

        return $this->insert($comment);
    }

    /**
     * Delete all comments for one whole module
     *
     * @param  int $module_id ID of the module
     * @return bool
     **/
    public function deleteByModule($module_id)
    {
        return $this->deleteAll(new Criteria('com_modid', (int)$module_id));
    }

    /**
     * Change a value in multiple comments
     *
     * @param string $fieldname  Name of the field
     * @param string $fieldvalue Value to write
     * @param object $criteria   {@link CriteriaElement}
     *
     * @return bool
     **/
    /*
     function updateAll($fieldname, $fieldvalue, $criteria = null)
     {
     $set_clause = is_numeric($fieldvalue) ? $filedname.' = '.$fieldvalue : $filedname.' = '.$this->db->quoteString($fieldvalue);
     $sql = 'UPDATE '.$this->db->prefix('xoopscomments').' SET '.$set_clause;
     if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
     $sql .= ' '.$criteria->renderWhere();
     }
     if (!$result = $this->db->query($sql)) {
     return false;
     }

     return true;
     }
     */
}
