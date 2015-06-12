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
     * @var \TYPO3\Flow\Security\AccountRepository
     * @Flow\Inject
     */
    protected $accountRepository;

    /**
     * @var \TYPO3\Neos\Domain\Service\UserService
     * @Flow\Inject
     */
    protected $userService;

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
     * @param string $target     who should get the notification (append rule: default empty = fcfs / + = show it to all / ! = delete it after me)
     */
    public function addNotification($type, $title, $packageKey='', $message='', $target='') {
        $targetUsers = $this->getTargetUsersArrayByString($target);

        $newNotification = $this->objectManager->get('WebExcess\\Notifications\\Domain\\Model\\Notification');
        $newNotification->setType( $type );
        $newNotification->setTitle( $title );
        $newNotification->setPackageKey( $packageKey );
        $newNotification->setMessage( $message );
        $newNotification->setTargetUsers($targetUsers);

        $this->notificationRepository->add( $newNotification );

        if( isset($this->settings['addEventLog']) && $this->settings['addEventLog']===TRUE ){
            $eventData = array('type'=>$type, 'title'=>$title, 'packageKey'=>$packageKey, 'message'=>$message, 'targetUsers'=>$targetUsers);
            $this->eventService->emit('WebExcess.Notification', $eventData);
        }
    }

    /**
     * return the notifications
     *
     * @return \TYPO3\Flow\Persistence\QueryResultInterface $notifications
     */
    public function getNotifications() {
        $notificationsPool = $this->notificationRepository->findAll();
        $notifications = array();

        $currentUser = $this->userService->getCurrentUser();
        $currentUserAccountIdentifiers = array();
        /** @var \TYPO3\Flow\Security\Account $account */
        foreach ($currentUser->getAccounts() as $account) {
            $currentUserAccountIdentifiers[] = $account->getAccountIdentifier();
            $currentUserAccountIdentifiers[] = $account->getAccountIdentifier().'+';
            $currentUserAccountIdentifiers[] = $account->getAccountIdentifier().'!';
        }

        /** @var \WebExcess\Notifications\Domain\Model\Notification $notification */
        foreach ($notificationsPool as $notification) {
            $targetUsers = $notification->getTargetUsers();
            if( !empty($targetUsers) ) {
                $targetUserIntersection = array_intersect($targetUsers, $currentUserAccountIdentifiers);
                if( count($targetUserIntersection)>0 ) {
                    /**
                     * $action values:
                     * 0 = remove the notification (default fcfs)
                     * 1 = remove me from the list (+ flag)
                     * 2 = remove the notification (! flag)
                     */
                    $action = 0;
                    foreach ($targetUserIntersection as $intersectionUser) {
                        if( substr($intersectionUser, -1)=='!' ){
                            $action = max($action, 2);
                        }elseif( substr($intersectionUser, -1)=='+' ){
                            $action = max($action, 1);
                        }
                    }
                    if( $action===1 ) {
                        $notification->setTargetUsers( array_diff($targetUsers, $currentUserAccountIdentifiers) );
                        if( count($notification->getTargetUsers())>0 ) {
                            $this->notificationRepository->update($notification);
                        }
                    }
                    if( $action!==1 || count($notification->getTargetUsers())==0 ) {
                        $this->notificationRepository->remove($notification);
                    }
                    $notifications[] = $notification;

                }
            }else{
                $notifications[] = $notification;
                $this->notificationRepository->remove($notification);
            }
        }

        $this->persistenceManager->persistAll();

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

    /**
     * @param string $target
     * @return array
     */
    private function getTargetUsersArrayByString($target='') {
        $targetUsers = array();
        if( !empty($target) ){
            if( strpos($target, 'Users:')===0 ){
                $targetUsers = explode(',', substr($target, 6));
            }elseif( strpos($target, 'Roles:')===0 ){
                /** @var array $targetRoles */
                $targetRoles = explode(',', substr($target, 6));
                foreach ($targetRoles as $targetRole) {
                    $userAppend = '';
                    if( substr($targetRole, -1)=='+' ){
                        $userAppend = '+';
                        $targetRole = substr($targetRole, 0, -1);
                    }elseif( substr($targetRole, -1)=='!' ){
                        $userAppend = '!';
                        $targetRole = substr($targetRole, 0, -1);
                    }elseif( strpos($target, '+')!==false || strpos($target, '!')!==false ) {
                        $userAppend = '+';
                    }
                    $accounts = $this->accountRepository->findByRoleIdentifiers($targetRole);
                    /** @var \TYPO3\Flow\Security\Account $account */
                    foreach ($accounts as $account) {
                        $targetUsers[] = $account->getAccountIdentifier().$userAppend;
                    }
                }
            }
        }
        return $targetUsers;
    }

}
