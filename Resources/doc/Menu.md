# Menu

* [Overriding the menu through yaml](#overriding-the-menu-through-yaml)
* [Overriding the menu by service](#overriding-the-menu-by-service)
* [Overriding the menu through twig](#overriding-the-menu-through-twig)
* [Changing the menu orientation](#changing-the-menu-orientation)
* [Changing the title](#changing-the-title)
* [Enabling global search](#enabling-global-search)

---

By default, the menu is generated automatically.
One menu item will be created for each Crud Controllers detected, and it will be sorted alphabetically.

You can however change how it's done, and even add child items.

## Overriding the menu through yaml

in `config/qag.yaml` (if there is no file, create one), add the following:

```yaml
arkounay_quick_admin_generator:
    menu:
        items:
            - App\Controller\NewsController
            - App\Controller\CategoryController
            - crud: App\Controller\Admin\AgencyController
              label: 'Item with subitems'
              children:
                - App\Controller\UserController
                - { label: 'google', url: 'https://www.google.com', target: '_blank' }
            # etc...
```

- Items specifying a crud controller will still be hidden when their controller's `isEnabled` returns false, and still behave as usual.
- If a `crud` isn't specified, you can specifiy how an item is renderer through the `label`, `url`, `route`, `route_params`, `target` and `attr` attributes
- The `children` attribute allows adding submenu items.

![Menu](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-subitems.png)


## Overriding the menu by service

Create a class that implements `MenuInterface` (or extends `Menu`).
The easiest way is to extend `Menu`, and add the proper configuration in services.yaml

```yaml
    Arkounay\Bundle\QuickAdminGeneratorBundle\Menu\MenuInterface: '@App\Admin\Menu'

    App\Admin\Menu:
        arguments:
            $cruds: !tagged_iterator quickadmin.crud
            $config: '%quick_admin_generator%'
```

```php
namespace App\Admin;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Menu\Menu as BaseMenu;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;

class Menu extends BaseMenu
{

    protected function createMenuItem(array $cruds, $node, Request $request): ?MenuItem
    {
        $menuItem = parent::createMenuItem($cruds, $node, $request);
        // $node will be a crud class or will contain what's in the arkounay_quick_admin_generator.menu.items yaml definition
        return $menuItem;
    }
    
    protected function createDashboardMenuItem(Request $request): ?MenuItem
    {
        // returning Null will remove the Dashboard item
        return null;
    }

}
```

## Overriding the menu through twig

Create the file `/templates/bundles/ArkounayQuickAdminGeneratorBundle/base.html.twig`
In it, you can override the block `menu_items` and do what you want there.
```twig
{% extends '@!ArkounayQuickAdminGenerator/base.html.twig' %}

{% block menu_items %}
{% endblock %}
```
If you want to configure how the dashboard item looks, you can override the block `menu_items_dashboard`.

## Changing the menu orientation

By default, the menu is horizontal. 

![Horizontal Menu](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-horizontal.png)

You can make it vertical by editing the yaml:
```yaml
arkounay_quick_admin_generator:
    menu:
        theme: 'vertical'
```

![Vertical Menu](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-vertical.png)

## Changing the title

You can change the title by editing the `arkounay_quick_admin_generator.title`
```yaml
arkounay_quick_admin_generator:
    title: 'Custom Title'
```
or override the twig block `main_title` for more control.

## Enabling global search

You can enable global search by setting `arkounay_quick_admin_generator.global_search` to `true`

```yaml
arkounay_quick_admin_generator:
    global_search: true
```

You can modify the results with the `qag.events.quick_search_item` and `qag.events.quick_search_crud` events.