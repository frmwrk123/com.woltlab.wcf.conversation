<?php
namespace wcf\data\conversation;
use wcf\data\DatabaseObject;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\breadcrumb\IBreadcrumbProvider;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a conversation.
 *
 * @author	Marcel Werk
 * @copyright	2009-2012 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.conversation
 * @subpackage	data.conversation
 * @category 	Community Framework
 */
class Conversation extends DatabaseObject implements IBreadcrumbProvider, IRouteController {
	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'conversation';
	
	/**
	 * @see	wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'conversationID';
	
	/**
	 * @see	wcf\system\request\IRouteController::getID()
	 */
	public function getID() {
		return $this->conversationID;
	}
	
	/**
	 * @see	wcf\system\request\IRouteController::getTitle()
	 */
	public function getTitle() {
		return $this->subject;
	}
	
	/**
	 * @see wcf\system\breadcrumb\IBreadcrumbProvider::getBreadcrumb()
	 */
	public function getBreadcrumb() {
		return new Breadcrumb($this->subject, LinkHandler::getInstance()->getLink('Conversation', array(
			'object' => $this
		)));
	}
	
	/**
	 * Returns true, if this conversation is new for the active user.
	 *
	 * @return boolean
	 */
	public function isNew() {
		if ($this->lastPostTime > $this->lastVisitTime) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Gets a specific user conversation.
	 * 
	 * @param	integer		$conversationID
	 * @param	integer		$userID
	 * @return	wcf\data\conversation\ViewableConversation
	 */
	public static function getUserConversation($conversationID, $userID) {
		$sql = "SELECT 		conversation_to_user.*, conversation.*
			FROM		wcf".WCF_N."_conversation conversation
			LEFT JOIN	wcf".WCF_N."_conversation_to_user conversation_to_user
			ON		(conversation_to_user.participantID = ? AND conversation_to_user.conversationID = conversation.conversationID)
			WHERE		conversation.conversationID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($userID, $conversationID));
		$row = $statement->fetchArray();
		if ($row !== false) {
			return new Conversation(null, $row);
		}
		
		return null;
	}
	
	/**
	 * Returns true, if the active user has the permission to read this conversation.
	 * 
	 * @return boolean
	 */
	public function canRead() {
		if (!WCF::getUser()->userID) return false;
		
		if ($this->isDraft && $this->userID == WCF::getUser()->userID) return true;
		
		if ($this->participantID == WCF::getUser()->userID && $this->hideConversation != 2) return true;
		
		return false;
	}
}
