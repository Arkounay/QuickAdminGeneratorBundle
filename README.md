# QAG - Quick Admin Generator Bundle for Symfony 5

[![Build Status](https://travis-ci.org/Arkounay/QuickAdminGeneratorBundle.svg?branch=master)](https://travis-ci.org/Arkounay/QuickAdminGeneratorBundle) [![codecov](https://codecov.io/gh/Arkounay/QuickAdminGeneratorBundle/branch/master/graph/badge.svg?token=8HOIPA6PMI)](https://codecov.io/gh/Arkounay/QuickAdminGeneratorBundle)

QAG is a bundle that allows quick and simple administration backends generation for Symfony applications using Doctrine and [Tabler](github.com/tabler/tabler).

![Quick Admin Generator Preview](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-horizontal.png)

## Getting started

Install the dependency:

```
composer require arkounay/quick-admin-generator-bundle
```

also make sure the following line was added in `config/bundles.php`:

```php
Arkounay\Bundle\QuickAdminGeneratorBundle\ArkounayQuickAdminGeneratorBundle::class => ['all' => true],
```

and that assets were installed: ` php bin/console assets:install --symlink`.


Finally, add the following route configuration, for example in `config/routes.yaml`:

```yaml
qag_routes:
    resource: 'Arkounay\Bundle\QuickAdminGeneratorBundle\Crud\RouteLoader'
    type: service
    prefix: '/admin'
```

You will probably want secure the /admin route prefix, to do so you can add the following line in your `security.yaml`:

```yaml
access_control:
     - { path: ^/admin, roles: ROLE_ADMIN }
```

**and that's it, the bundle is ready to be used.**

Now, you can add a Controller that extends `Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud` to add your first crud.

For example, let's say you have a `News` entity.
*(Make sure `News` implements `__toString()`)*

Create a controller for instance `src/Controller/Admin/NewsController.php`, with the following code:

```php
namespace App\Controller\Admin;

use App\Entity\News;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;

class NewsController extends Crud
{
    public function getEntity(): string
    {
        return News::class;
    }
}
```
    
and now refresh `/admin` in your browser. You should see a new "News" item that appeared in the menu, and you should now be able to create, edit, and delete news.

If you use the symfony command to display routes `php bin/console debug:router`, you'll see that some routes avec been generated for you:
```
qag.category                    ANY      ANY      ANY    /admin/category/                  
qag.category_create             ANY      ANY      ANY    /admin/category/create            
qag.category_delete             ANY      ANY      ANY    /admin/category/delete/{id}/      
qag.category_delete_batch       ANY      ANY      ANY    /admin/category/deleteBatch      
qag.category_edit               ANY      ANY      ANY    /admin/category/edit/{id}/        
qag.category_filter_form_ajax   ANY      ANY      ANY    /admin/category/filterFormAjax
```

## Next steps

There are multiple ways to configure and override things in QAG Bundle, depending on the complexity of the project.
You can use annotations for simple and quick tweaks regarding entities fields, override twigs to change appearance, add listeners to create special rules that applies when parsing entities, etc.

See :
1) [Fields configuration](Resources/doc/Fields.md)
    * [Configure Fields by Annotations](Resources/doc/Fields.md#configure-fields-by-annotations)
      - [@QAG\Field](Resources/doc/Fields.md#qagfield)
      - [@QAG\HideInForm](Resources/doc/Fields.md#qaghideinform)
      - [@QAG\HideInList](Resources/doc/Fields.md#qaghideinlist)
      - [@QAG\Ignore](Resources/doc/Fields.md#qagignore)
      - [@QAG\Sort](Resources/doc/Fields.md#qagsort)
      - [@QAG\Crud](Resources/doc/Fields.md#qagcrud)
    * [Configure Fields by Attributes](#configure-fields-by-attributes)
    * [Configure Fields by overriding controllers](#configure-fields-by-overriding-controllers)
    * [Configure Fields by using Listeners](#configure-fields-by-using-listeners)
2) [Controllers, lists, and security](Resources/doc/Controllers.md)
   * [Changing the URL prefix](Resources/doc/Controllers.md#changing-the-url-prefix)
   * [Metadata](Resources/doc/Controllers.md#metadata)
       + [Changing name](Resources/doc/Controllers.md#changing-name)
       + [Adding an icon](Resources/doc/Controllers.md#adding-an-icon)
       + [Adding a description](Resources/doc/Controllers.md#adding-a-description)
       + [Responsive mode](Resources/doc/Controllers.md#responsive-mode)
   * [Permissions](Resources/doc/Controllers.md#permissions)
   * [Filtering the list](Resources/doc/Controllers.md#filtering-the-list)
       + [Filtering through Query Builder](Resources/doc/Controllers.md#filtering-through-query-builder)
       + [Filtering through Filters](Resources/doc/Controllers.md#filtering-through-filters)
   * [Dependency injection](Resources/doc/Controllers.md#dependency-injection)
   * [Overriding the default behaviour](Resources/doc/Controllers.md#overriding-the-default-behaviour)
3) [Actions and routing](Resources/doc/Actions.md)
   - [Normal actions](Resources/doc/Actions.md#normal-actions)
   - [Batch actions](Resources/doc/Actions.md#batch-actions)
   - [Global actions](Resources/doc/Actions.md#global-actions)
   - [Customizing how actions are rendered](Resources/doc/Actions.md#customizing-how-actions-are-rendered)
   - [Routing shorcuts](Resources/doc/Actions.md#routing-shorcuts)
4) [Forms](Resources/doc/Forms.md)
   * [Overriding the Form Builder directly](Resources/doc/Forms.md#overriding-the-form-builder-directly)
   * [Overriding the automatic Form Builder generation by using Event Subscribers](Resources/doc/Forms.md#overriding-the-automatic-form-builder-generation-by-using-event-subscribers)
   * [Overriding the form type](Resources/doc/Forms.md#overriding-the-form-type)
   * [Collections](Resources/doc/Forms.md#collections)
   * [Overriding the form's twig theme](Resources/doc/Forms.md#overriding-the-form-s-twig-theme)
   * [Overriding the form's twig theme for a specific entity](Resources/doc/Forms.md#overriding-the-form-s-twig-theme-for-a-specific-entity)
5) [Configuring menu items and their position](Resources/doc/Menu.md)
   * [Overriding the menu through yaml](Resources/doc/Menu.md#overriding-the-menu-through-yaml)
   * [Overriding the menu by service](Resources/doc/Menu.md#overriding-the-menu-by-service)
   * [Overriding the menu through twig](Resources/doc/Menu.md#overriding-the-menu-through-twig)
   * [Changing the menu orientation](Resources/doc/Menu.md#changing-the-menu-orientation)
   * [Changing the title](Resources/doc/Menu.md#changing-the-title)
   * [Enabling global search](Resources/doc/Menu.md#enabling-global-search)
6) [Overriding the rest of the twigs](Resources/doc/Twig.md)
   * [Theme](Resources/doc/Twig.md#theme)
   * [Overriding creation and edition](Resources/doc/Twig.md#overriding-creation-and-edition)
   * [Overriding lists](Resources/doc/Twig.md#overriding-lists)
   * [Overriding the Dashboard](Resources/doc/Twig.md#overriding-the-dashboard)
   * [Adding custom JavaScript](Resources/doc/Twig.md#adding-custom-javascript)