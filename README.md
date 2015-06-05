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
