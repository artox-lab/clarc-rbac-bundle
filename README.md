Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require artox-lab/clarc-rbac-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require artox-lab/clarc-rbac-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    ArtoxLab\Bundle\ClarcRbacBundle\ArtoxLabClarcRbacBundle::class => ['all' => true],
];
```

Usage example
----------------------------------------
### Step 1: Configuration

Configure symfony messenger with clarc-bundle middleware

```yaml
# config/packages/messenger.yaml

framework:
    messenger:
    
        buses:
            command.bus:
                middleware:
                    - artox_lab_clarc.bus.validation
                    - artox_lab_clarc_rbac.rules_voter_middleware
            query.bus:
                middleware:
                    - artox_lab_clarc.bus.validation
                    - artox_lab_clarc_rbac.rules_voter_middleware
```

### Step 2: Create voter

Create your own voter for some command

```php
<?php

namespace App\UseCases\Commands\Image\Add;

use ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\AbstractCommandVoter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class Voter extends AbstractCommandVoter
{
    public function __construct(private Security $security, private RolesProvider $rolesProvider)
    {
    }

    //@phpcs:ignore
    protected function hasRequestAccess(UserInterface $user, mixed $subject): bool
    {
        if ($this->security->isGranted(ROLES::ROLE_ADMIN) === true) {
            return true;
        }

        return false;
    }

    //@phpcs:ignore
    protected function hasCliAccess(UserInterface $user, mixed $subject): bool
    {
        return true;
    }

    protected function isPublic(): bool
    {
        return false;
    }
}
```
### Step 3: Print annotation

Use your voter for command in annotation through the RulesVoter
```php
<?php

namespace App\UseCases\Commands\Image\Add;

use ArtoxLab\Bundle\ClarcBundle\Core\UseCases\Commands\AbstractCommand;
use ArtoxLab\Bundle\ClarcRbacBundle\Core\Interfaces\Security\Annotation\RulesVoter;
use Symfony\Component\Validator\Constraints as Assert;

/** @psalm-suppress MissingConstructor */
#[RulesVoter(Voter::class)]
class Command extends AbstractCommand
{
}
```