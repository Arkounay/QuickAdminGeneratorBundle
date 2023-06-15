# QAG - Quick Admin Generator Bundle

![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/arkounay/QuickAdminGeneratorBundle)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/arkounay/QuickAdminGeneratorBundle/tests.yaml)
![MIT License](https://img.shields.io/github/license/arkounay/QuickAdminGeneratorBundle)
[![codecov](https://codecov.io/gh/Arkounay/QuickAdminGeneratorBundle/branch/master/graph/badge.svg?token=8HOIPA6PMI)](https://codecov.io/gh/Arkounay/QuickAdminGeneratorBundle)


QAG is a bundle that allows quick and simple generation of administration backends for Symfony applications using Doctrine.

- [Documentaton](https://arkounay.github.io/QuickAdminGeneratorBundle/#/)
- [Simple online demo](https://qag-demo.outerark.com/) 
- [Demo repository](https://github.com/Arkounay/qag-demo)

![Quick Admin Generator Preview](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/docs/images/menu-horizontal.png)

## Getting started

Install the dependency:

```
composer require arkounay/quick-admin-generator-bundle
```

also, make sure the following line was added in `config/bundles.php`:

```php
Arkounay\Bundle\QuickAdminGeneratorBundle\ArkounayQuickAdminGeneratorBundle::class => ['all' => true],
```

and that assets were installed: `php bin/console assets:install --symlink`.


Finally, add the following route configuration, for example in `config/routes.yaml`:

```yaml
qag_routes:
    resource: 'Arkounay\Bundle\QuickAdminGeneratorBundle\Crud\RouteLoader'
    type: service
    prefix: '/admin'
```

You will probably want to secure the /admin route prefix. To do so, you can add the following line in your `security.yaml`:

```yaml
access_control:
     - { path: ^/admin, roles: ROLE_ADMIN }
```

**and that's it, the bundle is ready to be used.**

Now, you can add a Controller that extends `Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud` to add your first crud.

For example, let's say you have a `News` entity.

!> Make sure your entity implements `__toString()`!

Create a controller, for instance `src/Controller/Admin/NewsController.php`, with the following code:

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

If you use the symfony command to display routes `php bin/console debug:router`, you'll see that some routes have been generated for you:
```
qag.category                       ANY      ANY      ANY    /admin/category/                  
qag.category_create                ANY      ANY      ANY    /admin/category/create            
qag.category_delete                ANY      ANY      ANY    /admin/category/delete/{id}/      
qag.category_delete_batch          ANY      ANY      ANY    /admin/category/deleteBatch      
qag.category_edit                  ANY      ANY      ANY    /admin/category/edit/{id}/        
qag.category_export                ANY      ANY      ANY    /admin/category/export
qag.category_filter_form_ajax      ANY      ANY      ANY    /admin/category/filterFormAjax
qag.category_toggle_boolean_post   POST     ANY      ANY    /admin/category/toggleBooleanPost/{id}/
```

## Next steps

There are multiple ways to configure and override things in QAG Bundle, depending on the complexity of the project.
You can use [attributes](Fields.md#configure-fields-by-annotations) for simple and quick tweaks regarding entity fields, override Twig templates to change the appearance, add listeners to create special rules that apply when parsing entities, and more.

See :
1) [Fields configuration](Fields.md)
    * [Configure Fields by Annotations](Fields.md#configure-fields-by-annotations)
      - [QAG\Field](Fields.md#qagfield)
      - [QAG\HideInForm](Fields.md#qaghideinform)
      - [QAG\HideInList](Fields.md#qaghideinlist)
      - [QAG\HideInView](Fields.md#qaghideinview)
      - [QAG\HideInExport](Fields.md#qaghideinexport)
      - [QAG\Ignore](Fields.md#qagignore)
      - [QAG\Sort](Fields.md#qagsort)
      - [QAG\Crud](Fields.md#qagcrud)
    * [Configure Fields by Attributes](Fields.md#configure-fields-by-attributes)
    * [Configure Fields by overriding controllers](Fields.md#configure-fields-by-overriding-controllers)
    * [Configure Fields by using Listeners](Fields.md#configure-fields-by-using-listeners)
2) [Controllers, lists, and security](Controllers.md)
   * [Changing the URL prefix](Controllers.md#changing-the-url-prefix)
   * [Metadata](Controllers.md#metadata)
       + [Changing name](Controllers.md#changing-name)
       + [Adding an icon](Controllers.md#adding-an-icon)
       + [Adding a badge with a number](Controllers.md#adding-a-badge-with-a-number)
       + [Adding a description](Controllers.md#adding-a-description)
       + [Responsive mode](Controllers.md#responsive-mode)
   * [Permissions](Controllers.md#permissions)
       + [Security checker](Controllers.md#security-checker)
   * [Filtering the list](Controllers.md#filtering-the-list)
       + [Filtering through Query Builder](Controllers.md#filtering-through-query-builder)
       + [Filtering through Filters](Controllers.md#filtering-through-filters)
   * [Dependency injection](Controllers.md#dependency-injection)
   * [Overriding the default behaviour](Controllers.md#overriding-the-default-behaviour)
3) [Actions and routing](Actions.md)
   - [Normal actions](Actions.md#normal-actions)
   - [Batch actions](Actions.md#batch-actions)
   - [Global actions](Actions.md#global-actions)
   - [Customizing how actions are rendered](Actions.md#customizing-how-actions-are-rendered)
   - [Entity actions display mode (dropdown / expanded)](Actions.md#entity-actions-display-mode-dropdown--expanded)
   - [Routing shorcuts](Actions.md#routing-shorcuts)
4) [Forms](Forms.md)
   * [Overriding the Form Builder directly](Forms.md#overriding-the-form-builder-directly)
   * [Overriding the automatic Form Builder generation by using Event Subscribers](Forms.md#overriding-the-automatic-form-builder-generation-by-using-event-subscribers)
   * [Overriding the form type](Forms.md#overriding-the-form-type)
   * [Collections](Forms.md#collections)
   * [Overriding the form's twig theme](Forms.md#overriding-the-form-s-twig-theme)
   * [Overriding the form's twig theme for a specific entity](Forms.md#overriding-the-form-s-twig-theme-for-a-specific-entity)
   * [Disabling turbo on form submit](Forms.md#disabling-turbo-on-form-submit)
5) [Configuring menu items and their position](Menu.md)
   * [Overriding the menu through yaml](Menu.md#overriding-the-menu-through-yaml)
   * [Overriding the menu by service](Menu.md#overriding-the-menu-by-service)
   * [Overriding the menu through twig](Menu.md#overriding-the-menu-through-twig)
   * [Changing the menu orientation](Menu.md#changing-the-menu-orientation)
   * [Changing the title](Menu.md#changing-the-title)
   * [Enabling global search](Menu.md#enabling-global-search)
   * [Switch to dark Mode](Menu.md#switch-to-dark-mode)
   * [Redirect to a specific route instead of the Dashboard](#redirect-to-a-specific-route-instead-of-the-dashboard)
6) [Overriding the rest of the twigs](Twig.md)
   * [Theme](Twig.md#theme)
   * [Interactive command-line helper](Twig.md#interactive-command-line-helper)
   * [Overriding creation and edition](Twig.md#overriding-creation-and-edition)
   * [Overriding lists](Twig.md#overriding-lists)
   * [Overriding the Dashboard](Twig.md#overriding-the-dashboard)
   * [Adding custom JavaScript](Twig.md#adding-custom-javascript)
