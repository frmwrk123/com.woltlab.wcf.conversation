<?php
namespace wcf\data\modification\log;
use wcf\data\conversation\Conversation;
use wcf\system\log\modification\ConversationModificationLogHandler;
use wcf\system\WCF;

/**
 * Represents a list of modification logs for conversation log page.
 *
 * @author	Alexander Ebert
 * @copyright	2001-2012 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.conversation
 * @subpackage	data.modification.log
 * @category	Community Framework
 */
class ConversationLogModificationLogList extends ModificationLogList {
	/**
	 * conversation object type id
	 * @var	integer
	 */
	public $conversationObjectTypeID = 0;
	
	/**
	 * conversation object
	 * @var	wcf\data\conversation\Conversation
	 */
	public $conversation = null;
	
	/**
	 * @see	wbb\data\DatabaseObjectList::__construct()
	 */
	public function __construct() {
		parent::__construct();
		
		// get object types
		$conversationObjectType = ConversationModificationLogHandler::getInstance()->getObjectType('com.woltlab.wcf.conversation.conversation');
		$this->conversationObjectTypeID = $conversationObjectType->objectTypeID;
	}
	
	/**
	 * Initializes the conversation log modification log list.
	 * 
	 * @param	wcf\data\conversation\Conversation	$conversation
	 */
	public function setConversation(Conversation $conversation) {
		$this->conversation = $conversation;
	}
	
	/**
	 * @see	wcf\data\DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(modification_log.logID) AS count
			FROM	wcf".WCF_N."_modification_log modification_log
			WHERE	modification_log.objectTypeID = ?
				AND modification_log.objectID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->conversationObjectTypeID,
			$this->conversation->conversationID
		));
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * @see	wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		$sql = "SELECT	modification_log.*
			FROM	wcf".WCF_N."_modification_log modification_log
			WHERE	modification_log.objectTypeID = ?
				AND modification_log.objectID = ?
				".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute(array(
			$this->conversationObjectTypeID,
			$this->conversation->conversationID
		));
		$this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));
		
		// use table index as array index
		$objects = array();
		foreach ($this->objects as $object) {
			$objectID = $object->{$this->getDatabaseTableIndexName()};
			$objects[$objectID] = $object;
			
			$this->indexToObject[] = $objectID;
		}
		$this->objectIDs = $this->indexToObject;
		$this->objects = $objects;
		
		foreach ($this->objects as &$object) {
			$object = new ViewableConversationModificationLog($object);
		}
		unset($object);
	}
}
