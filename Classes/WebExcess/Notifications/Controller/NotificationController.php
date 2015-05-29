<?php
namespace WebExcess\Notifications\Controller;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "WebExcess.Notifications". *
 *                                                                          *
 *                                                                          */

use TYPO3\Flow\Annotations as Flow;

class NotificationController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \WebExcess\Notifications\Domain\Service\NotificationService
	 * @Flow\Inject(lazy = false)
	 */
	protected $notificationService;

	/**
	 * output the notifications as json
	 * @return string
	 */
	public function getAction() {
		$script = '';

		$notifications = $this->notificationService->getNotifications();
		foreach ($notifications as $notification) {
			$script .= 'window.Typo3Neos.Notification.'.$notification->getType().'(\''.$notification->getTitle().'\', \''.(!empty($notification->getPackageKey())?$notification->getPackageKey().': ':'').$notification->getMessage().'\');';
		}
		$this->notificationService->removeNotifications();

		return
			"console.log('WebExcess.Notifications Check');"
			.$script;
	}

	/**
	 * add a notification
	 * 
	 * @param string $type
	 * @param string $title
	 * @param string $packageKey
	 * @param string $message
	 * 
	 * @return json
	 */
	public function addAction($type, $title, $packageKey='', $message='') {
		$this->notificationService->addNotification($type, $title, $packageKey, $message);

		return json_encode(array('status' => true));
	}

}