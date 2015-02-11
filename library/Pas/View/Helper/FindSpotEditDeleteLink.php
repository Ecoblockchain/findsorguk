<?php

/** A view helper class to display the findspot edit delete links
 * @todo Perhaps change to an asserts acl check
 * @todo DRY the access groups to another class, this is used too often
 * @category Pas
 * @package  Pas_View_Helper
 * @subpackage Abstract
 * @since September 27 2011
 * @author Daniel Pett
 * @version 1
 * @uses Zend_View_Helper_Url
 */
class Pas_View_Helper_FindSpotEditDeleteLink extends Zend_View_Helper_Abstract
{

    /** The people with no access
     * @var array
     * @access protected
     */
    protected $_noaccess = array('public');

    /** The restricted users groups
     * @var array
     * @access protected
     */
    protected $_restricted = array('member', 'research', 'hero');

    /** The recording group
     * @var array
     * @access protected
     */
    protected $_recorders = array('flos');

    /** The higher level array
     * @access protected
     * @var array
     */
    protected $_higherLevel = array('admin', 'fa', 'treasure', 'hoard');

    /** The auth object
     * @access protected
     * @var mixed|null
     */
    protected $_auth = NULL;

    /** The missing group message
     * @access protected
     * @var string
     */
    protected $_missingGroup = 'User is not assigned to a group';

    /** The message for no access
     * @access protected
     * @var string
     */
    protected $_message = 'You are not allowed edit rights to this record';

    /** Get the auth object
     * @return mixed|null
     */
    public function getAuth()
    {
        $this->_auth = Zend_Registry::get('auth');
        return $this->_auth;
    }

    /** Get the user's role from identity
     * @access private
     * @return string $role The user's role
     */
    public function getRole()
    {
        if ($this->getAuth()->hasIdentity()) {
            $user = $this->getAuth()->getIdentity();
            $role = $user->role;
        } else {
            $role = null;
        }
        return $role;
    }

    /** Get the user's identity number
     * @access private
     * @return integer $id The user's id number
     */
    public function getUserID()
    {
        if ($this->getAuth()->hasIdentity()) {
            $user = $this->getAuth()->getIdentity();
            $id = $user->id;
        } else {
            $id = null;
        }
        return $id;
    }

    /** Get the user's institution
     * @access private
     * @return string $inst The institution name
     * @throws Pas_Exception_Group
     */
    public function getInst()
    {
        if ($this->getAuth()->hasIdentity()) {
            $user = $this->getAuth()->getIdentity();
            $inst = $user->institution;
            if (is_null($inst)) {
                throw new Pas_Exception_Group($this->_missingGroup);
            }
        } else {
            $inst = null;
        }
        return $inst;
    }

    /** The institution value
     * @access protected
     * @var null
     */
    protected $_institution = NULL;

    /** The created by value
     * @access protected
     * @var  null
     */
    protected $_createdBy = NULL;

    /** Get the created by value
     * @access public
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->_createdBy;
    }

    /** Set the createdby value
     * @param mixed $createdBy
     * @access public
     * @return \Pas_View_Helper_RecordEditDeleteLinks
     */
    public function setCreatedBy($createdBy)
    {
        $this->_createdBy = $createdBy;
        return $this;
    }

    /** Get the institution to use
     * @access public
     * @return string
     */
    public function getInstitution()
    {
        return $this->_institution;
    }

    /** Set the institution to query
     * @access public
     * @param mixed $institution
     * @return \Pas_View_Helper_RecordEditDeleteLinks
     */
    public function setInstitution($institution)
    {
        $this->_institution = $institution;
        return $this;
    }

    /** The controller to use - default to artefacts
     * @var string
     * @access protected
     */
    protected $_controller = 'artefacts';

    /** Get the controller to use
     * @return mixed
     * @access public
     */
    public function getController()
    {
        return $this->_controller;
    }

    /** Set the controller for the partial to use
     * @param mixed $controller
     * @access public
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /** The find ID to use
     * @var null
     * @access protected
     */
    protected $findID = NULL;

    /** The record ID to link to
     * @var null
     * @access protected
     */
    protected $recordID = NULL;

    /** Get the record ID
     * @return mixed
     * @access public
     */
    public function getRecordID()
    {
        return $this->recordID;
    }

    /** Set the recordID to use
     * @access public
     * @param string $recordID
     */
    public function setRecordID($recordID)
    {
        $this->recordID = $recordID;
        return $this;
    }


    /** Get the find ID to use
     * @return mixed
     * @access public
     */
    public function getFindID()
    {
        return $this->findID;
    }

    /** Set the find ID to use
     * @access public
     * @param mixed $findID
     */
    public function setFindID($findID)
    {
        $this->findID = $findID;
        return $this;
    }

    /** Check the user's access by ID number and created by
     * @access private
     * @param  integer $createdBy the created by number for the find
     * @return boolean
     */
    public function checkAccessbyUserID()
    {
        if ($this->getCreatedBy() == $this->getUserID()) {
            return true;
        } else {
            return false;
        }
    }


    /** Check access by the institutional ID
     * @access public
     * @param  string $oldfindID The record ID
     * @return boolean
     */
    public function checkAccessbyInstitution()
    {
        if ($this->getInstitution() == $this->getInst()) {
            return true;
        } else {
            return false;
        }
    }

    /** the class to return
     * @access public
     * \Pas_View_Helper_RecordEditDeleteLinks
     */
    public function findSpotEditDeleteLink()
    {
        return $this;
    }

    /** To string function
     * @access public
     * @return string
     */
    public function __toString()
    {
        $html = '';
        if ($this->checkAccess()) {
            $html .= $this->buildHtml();
        }
        return $html;
    }




    /** Build the HTML links
     *
     * @param  int $ID , $controller
     * @return string $html
     */
    public function buildHtml()
    {
        $editClass = 'btn btn-small btn-warning';
        $deleteClass = 'btn btn-small btn-danger';
        $editurl = $this->view->url(array('module' => 'database', 'controller' => 'findspots', 'action' => 'edit',
            'id' => $this->getRecordID(), 'recordtype' => $this->getController()), null, true);
        $deleteurl = $this->view->url(array('module' => 'database', 'controller' => 'findspots', 'action' => 'delete',
            'id' => $this->getRecordID(), 'recordtype' => $this->getController()), null, true);
        $html = '<div class="btn-group"><a class="' . $editClass . '" href="' . $editurl
            . '" title="Edit spatial data for this record">Edit findspot <i class="icon-edit icon-white"></i></a> <a class="' . $deleteClass . '" href="' . $deleteurl
            . '" title="Delete spatial data">Delete findspot <i class="icon-trash icon-white"></i></a></div>';

        return $html;
    }


    public function checkAccess()
    {
        // If role = public return false
        if (in_array($this->getRole(), $this->_noaccess)) {
            return false;
        }
        //If role in restricted and created = created by return true
        else if (in_array($this->getRole(), $this->_restricted) && $this->getCreatedBy() == $this->getUserID()) {
            return true;
        }
        //If role in recorders and institution = inst or created by = created return true
        else if (in_array($this->getRole(), $this->_recorders) && $this->getInst() == $this->getInstitution()
            || $this->getCreatedBy() == $this->getUserID()
            || in_array($this->getRole(), $this->_recorders) && $this->getInstitution() == 'PUBLIC') {
            return true;
        }
        //If role in higher level return true
        else if (in_array($this->getRole(), $this->_higherLevel)) {
            return true;
        } else {
            return false;
        }
    }
}
