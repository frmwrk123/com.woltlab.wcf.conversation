<?php
namespace wcf\data\conversation;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\WCF;

/**
 * Represents a viewable conversation.
 *
 * @author	Marcel Werk
 * @copyright	2009-2012 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf.conversation
 * @subpackage	data.conversation
 * @category 	Community Framework
 */
class ViewableConversation extends DatabaseObjectDecorator {
	/**
	 * Summary of the participants
	 * 
	 * @var summary of the participants
	 */
	protected $__participantSummary = null;
	
	/**
	 * user profile object
	 * @var wcf\data\user\UserProfile
	 */
	protected $userProfile = null;
	
	/**
	 * last poster's profile
	 * @var wcf\data\user\UserProfile
	 */
	protected $lastPosterProfile = null;
	
	/**
	 * @see wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\conversation\Conversation';
	
	/**
	 * Returns the user profile object.
	 * 
	 * @return wcf\data\user\UserProfile
	 */
	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = new UserProfile(new User(null, $this->getDecoratedObject()->data));
		}
		
		return $this->userProfile;
	}
	
	/**
	 * Returns the last poster's profile object.
	 * 
	 * @return wcf\data\user\UserProfile
	 */
	public function getLastPosterProfile() {
		if ($this->lastPosterProfile === null) {
			if ($this->userID && $this->userID == $this->lastPosterID) {
				$this->lastPosterProfile = $this->getUserProfile();
			}
			else {
				$this->lastPosterProfile = new UserProfile(new User(null, array(
					'userID' => $this->lastPosterID,
					'username' => $this->lastPoster,
					'avatarID' => $this->lastPosterAvatarID,
					'avatarName' => $this->lastPosterAvatarName,
					'avatarExtension' => $this->lastPosterAvatarExtension,
					'width' => $this->lastPosterAvatarWidth,
					'height' => $this->lastPosterAvatarHeight,
					'email' => $this->lastPosterEmail,
					'disableAvatar' => $this->lastPosterDisableAvatar
				)));
			}
		}
		
		return $this->lastPosterProfile;
	}

	/**
	 * Gets the number of pages in this conversation.
	 *
	 * @return integer
	 */
	public function getPages() {
		if (WCF::getUser()->conversationMessagesPerPage) {
			$messagesPerPage = WCF::getUser()->conversationMessagesPerPage;
		}
		else {
			$messagesPerPage = CONVERSATION_MESSAGES_PER_PAGE;
		}
		
		return intval(ceil(($this->replies + 1) / $messagesPerPage));
	}
	
	/**
	 * Returns a summary of the participants.
	 * 
	 * @return	array<wcf\data\user\User>
	 */
	public function getParticipantSummary() {
		if ($this->__participantSummary === null) {
			$this->__participantSummary = array();
			
			if ($this->participantSummary) {
				$data = unserialize($this->participantSummary);
				if ($data !== false) {
					foreach ($data as $userData) {
						$this->__participantSummary[] = new User(null, array(
							'userID' => $userData['userID'],
							'username' => $userData['username'],
							'hideConversation' => $userData['hideConversation']
						));
					}
				}
			}
		}
		
		return $this->__participantSummary;
	}
}