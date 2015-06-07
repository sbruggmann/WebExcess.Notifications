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