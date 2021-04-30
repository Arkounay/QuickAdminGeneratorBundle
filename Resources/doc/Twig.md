## Twig

* [Theme](#theme)
* [Overriding creation and edition](#overriding-creation-and-edition)
* [Overriding lists](#overriding-lists)
* [Overriding the Dashboard](#overriding-the-dashboard)
* [Adding custom JavaScript](#adding-custom-javascript)

---

### Theme

This bundle uses [Tabler](github.com/tabler/tabler), which uses [bootstrap](https://getbootstrap.com/). Check out their documentation to see their available classes and components.


### Overriding creation and edition

See [Forms - Overriding the rest of the twigs](Forms.md#overriding-the-form-s-twig-theme-for-a-specific-entity)

### Overriding lists

As we've seen, you can override the "creation" and "edit" page of any entity by creating a twig in a specific folder: `/templates/bundles/ArkounayQuickAdminGeneratorBundle/crud/entities/[route-name]/form.html.twig`.

The same is true for lists, override `/templates/bundles/ArkounayQuickAdminGeneratorBundle/crud/entities/[route-name]/list.html.twig`

You can also override the Controller function `listTwig` to change the path: 

```php
public function formTwig(): string
{
    return 'my/custom/twig/path.html.twig';
}
```

See more at [Fields](Fields.md) documentation to see how to override how the table fields are rendered.

### Overriding the dashboard

Override the twig `crud/index.html.twig` by creating a file in `templates/bundles/ArkounayQuickAdminGeneratorBundle/crud/index.html.twig`

```twig
{% extends '@!ArkounayQuickAdminGenerator/crud/index.html.twig' %}

{% block content %}
    <div class="card">
        <div class="card-body">
            Hi {{ app.user }}
        </div>
    </div>
{% endblock %}
```


If you need some special parameters, override the route.

Create you own controller that extends from `DashboardController` and name it `qag.dashboard`.

```php
namespace App\Controller;
   
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends \Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\DashboardController
{

   /**
    * @Route("/admin/", name="qag.dashboard")
    */
   public function dashboard(): Response
   {
       // do something
       return $this->render('@ArkounayQuickAdminGenerator/crud/index.html.twig');
   }


}
```
Make sure the new route has more priority than the previous route.
```yaml
dashboard_annotations:
    resource: 'App\Controller\DashboardController'
    type: annotation
```

### Adding custom JavaScript

Override `base.html.twig` and add your custom js there.
e.g: 


```
{% block head_js %}
    {{ parent() }}

    {{ encore_entry_script_tags('admin', null, 'admin') }}
{% endblock %}
```

By default, this project uses Turbo, so you should use <a href="https://github.com/symfony/stimulus-bridge">stimulus</a>.

If you don't use stimulus and need to trigger javascript at page load, it's possible to append js at the end of <body>, this way it'll be reloaded and executed upon each page load, but beware of memory leaks / global document listeners.

```
{% block body %}
    {{ parent() }}
    <script>
    // ...
    </script>
{% endblock %}
```

You can disable turbo by override the whole's bundle's js (you can use the sources at `assets/app.js` to override them), or by adding `data-turbo="false"` to the body attribute for example : `{% block body_attributes 'data-turbo="false"' %}`

