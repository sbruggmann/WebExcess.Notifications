<?php
namespace WebExcess\Notifications\Command;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "WebExcess.Notifications". *
 *                                                                          *
 *                                                                          */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class NotificationCommandController extends CommandController {

	/**
	 * @var \WebExcess\Notifications\Domain\Service\NotificationService
	 * @Flow\Inject
	 */
	protected $notificationService;

	/**
	 * add a notification
	 * 
	 * @param string $type
	 * @param string $title
	 * @param string $packageKey
	 * @param string $message
	 * 
	 * @return void
	 */
	public function addCommand($type, $title, $packageKey='', $message='') {
		$this->notificationService->addNotification($type, $title, $packageKey, $message);
	}

}