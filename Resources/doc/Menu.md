# Menu

* [Overriding the menu through yaml](#overriding-the-menu-through-yaml)
* [Overriding the menu by service](#overriding-the-menu-by-service)
* [Overriding the menu through twig](#overriding-the-menu-through-twig)
* [Changing the menu orientation](#changing-the-menu-orientation)
* [Changing the title](#changing-the-title)

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
            # etc...
```

This allows you to specify a custom order for the menu items. 
They will still be hidden when their controller's `isEnabled` returns false, and still behave as usual.
This is useful if you want a simple and quick customization.

## Overriding the menu by service

Create a class with an `_invoke` method
```php
namespace App\Twig;


use App\Controller\CategoryController;
use App\Controller\NewsController;
use App\Controller\ProductController;

class MenuExtension
{

    public function __invoke(iterable $cruds)
    {
        return [
            $cruds[NewsController::class],
            'Products' => [
                $cruds[ProductController::class],
                $cruds[CategoryController::class]
            ]
        ];
    }

}
```

then in `config/qag.yaml` (if there is no file, create one), add the following:

```yaml
arkounay_quick_admin_generator:
    menu:
        items: 'App\Twig\MenuExtension'
```

this allows you to have subitems in a menu.

[Menu](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-subitems.png)

However, there is no dependency injection.

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

<img src="https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-horizontal.png" alt="Horizontal Menu" align="center" />

You can make it vertical by editing the yaml:
```yaml
arkounay_quick_admin_generator:
    menu:
        theme: 'vertical'
```

<img src="https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-vertical.png" alt="Vertical Menu" align="center" />



## Changing the title

You can change the title by editing the `arkounay_quick_admin_generator.title`
```yaml
arkounay_quick_admin_generator:
    title: 'Custom Title'
```
or override the twig block `main_title` for more control.