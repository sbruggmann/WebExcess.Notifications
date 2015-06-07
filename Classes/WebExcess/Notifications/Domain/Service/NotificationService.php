<?php
namespace WebExcess\Notifications\Domain\Service;

/*                                                                          *
 * This script belongs to the TYPO3 Flow package "WebExcess.Notifications". *
 *                                                                          *
 *                                                                          */

use TYPO3\Flow\Annotations as Flow;

/**
 * An notification service
 *
 * @Flow\Scope("singleton")
 */
class NotificationService {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var \WebExcess\Notifications\Domain\Repository\NotificationRepository
	 * @Flow\Inject(lazy = false)
	 */
	protected $notificationRepository;

    /**
     * @var \TYPO3\Neos\EventLog\Domain\Service\EventEmittingService
     * @Flow\Inject
     */
    protected $eventService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

	/**
	 * add notifications with javascript
	 * 
	 * @param string $type       ok, info, warning, error
	 * @param string $title      the notification
	 * @param string $packageKey prepend the package key
	 * @param string $message    only for warning and error
	 */
	public function addNotification($type, $title, $packageKey='', $message='') {
		$newNotification = $this->objectManager->get('WebExcess\\Notifications\\Domain\\Model\\Notification');
		$newNotification->setType( $type );
		$newNotification->setTitle( $title );
		$newNotification->setPackageKey( $packageKey );
		$newNotification->setMessage( $message );

		$this->notificationRepository->add( $newNotification );

        if( isset($this->settings['addEventLog']) && $this->settings['addEventLog']===TRUE ){
            $this->eventService->emit('WebExcess.Notification', array('type'=>$type, 'title'=>$title, 'packageKey'=>$packageKey, 'message'=>$message));
        }
	}

	/**
	 * return the notifications
	 * 
	 * @return \TYPO3\Flow\Persistence\QueryResultInterface $notifications
	 */
	public function getNotifications() {
		$notifications = $this->notificationRepository->findAll();
		return $notifications;
	}

	/**
	 * remove the notified notifications
	 * 
	 * @return void
	 */
	public function removeNotifications() {
		$this->notificationRepository->removeAll();
		$this->persistenceManager->persistAll();
	}

}
