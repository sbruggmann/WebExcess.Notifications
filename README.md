Neos CMS Notifications
======================

This plugin provides asynchronous notifications in Neos CMS.

Note: This package is still experimental and not for production.

Quick start
-----------

* Include the Plugin's route definitions to your `Routes.yaml` file

```yaml
-
  name: 'WebExcessNotifications'
  uriPattern: '<WebExcessNotificationsSubroutes>'
  subRoutes:
    WebExcessNotificationsSubroutes:
      package: WebExcess.Notifications
```

* Add in your Application a notification

```php
/**
 * @var \WebExcess\Notifications\Domain\Service\NotificationService
 * @Flow\Inject
 */
protected $notificationService;

public yourAction(){
	$this->notificationService->add('info', 'This is an important information');
}
```

* Add in your Shell-Script a notification

```
./flow notification:add error "Product import faild" --message "Got malformed XML"
```

* Visit the Neos backend and the notification appears


Address Users
-------------

`--target "Roles:TYPO3.Neos:Editor,TYPO3.Neos:Administrator"` or `--target "Users:usernamea,usernameb"`

`TYPO3.Neos:Editor\+` means that every user with this role get the notification.

`TYPO3.Neos:Editor\!` means that even if another role has the "+"-suffix, the delivery of the notification ends with the first of these "!"-suffix users.

If there is one "+" or "!" suffix, every other not-suffixed item gets "+" appended.

Important: Escape the characters "+" and "!" if you are in shell! 

**Default behavior:**

The first backend user is the one and only that get the notification.


**Role restricted behavior:**

```
./flow notification:add info "New Version of Neos CMS is installed" --target "Roles:TYPO3.Neos:Editor"
```

The first backend user with role "TYPO3.Neos:Editor" is the one and only that get the notification.


**Role restricted behavior - all users:**

```
./flow notification:add warning "The Export-Module has a new operating Concept" --target "Roles:TYPO3.Neos:Editor\+"
```

All backend users with role "TYPO3.Neos:Editor" get the notification.


**Role restricted behavior - limited:**

```
./flow notification:add error "Newsletter worker has failures" --target "Roles:TYPO3.Neos:Editor\+,TYPO3.Neos:Administrator\!"
```

All backend users with role "TYPO3.Neos:Editor" get the notification until one single user with role "TYPO3.Neos:Administrator" get the notification.


TYPO3.Neos:History Integration
------------------------------

To get this working you have to change the TYPO3\Neos\Controller\Module\Management\HistoryController.php

from:

```php
$view->setTypoScriptPathPattern('resource://TYPO3.Neos/Private/TypoScript/Backend');
```

to:

```php
$view->setTypoScriptPathPatterns(array('resource://TYPO3.Neos/Private/TypoScript/Backend', 'resource://WebExcess.Notifications/Private/TypoScript/History'));
```

and Enable it:

```yaml
WebExcess:
  Notifications:
    addEventLog: TRUE
```