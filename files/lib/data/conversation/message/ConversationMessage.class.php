<?php
namespace wcf\data\conversation\message;
use wcf\data\DatabaseObject;
use wcf\system\bbcode\MessageParser;

/**
 * Represents a conversation message.
 *
 * @author	Marcel Werk
 * @copyright	2009-2012 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.conversation
 * @subpackage	data.conversation.message
 * @category 	Community Framework
 */
class ConversationMessage extends DatabaseObject {
	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'conversation_message';
	
	/**
	 * @see	wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'messageID';
	
	/**
	 * Returns the formatted text of this message.
	 *
	 * @return string
	 */
	public function getFormattedMessage() {
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse($this->message, $this->enableSmilies, $this->enableHtml, $this->enableBBCodes);
	}
}