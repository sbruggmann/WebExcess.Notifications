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
     * @param string $type "info", "warning" or "error"
     * @param string $title string
     * @param string $packageKey string
     * @param string $message string
     * @param string $target "Users:usernamea,usernameb" or "Roles:TYPO3.Neos:Editor,TYPO3.Neos:Administrator" or "Roles:TYPO3.Neos+:Editor,TYPO3.Neos:Administrator!" etc.
     *
     * @return void
     */
    public function addCommand($type, $title, $packageKey='', $message='', $target='') {
        $this->notificationService->addNotification(
            $type,
            stripcslashes($title),
            $packageKey,
            stripcslashes($message),
            stripcslashes($target)
        );
    }

    /**
     * remove all notifications
     *
     * @return void;
     */
    public function removeAllCommand(){
        $this->notificationService->removeNotifications();
    }

}