# Campainer API Library for PHP

## What is this bundle doing ?

This bundle provides Campaigner APIs.

See [Campaigner API User Guide](docs/Campaigner-Elements-API-User-Guide.pdf) for the list of web services and methods.

## Usage

```php
use Broco\Campaigner\Soap\ContactManager;

$contact_manager = new ContactManager(ContactManager::DEFAULT_WSDL, $username, $password);

$contact_manager->ListContactFields();
```

## Credit

Code largely extracted from https://github.com/ExSituMarketing/EXS-CampaignerBundle
