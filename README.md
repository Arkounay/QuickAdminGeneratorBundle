# QAG - Quick Admin Generator Bundle for Symfony 5

QAG is a bundle that allows quick and simple administration backends generation for Symfony applications using Doctrine.

The theme used is [Tabler](github.com/tabler/tabler).

Disclaimer: this is primarily made for fun and for my specific use cases :sweat_smile: there will be funcitonnal tests in the demo repo.

<img src="https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/Resources/doc/images/menu-horizontal.png" alt="Horizontal Menu" align="center" />

## Getting started

Install the dependency:

```
composer install arkounay/quick-admin-generator-bundle
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

Now, you can add a Controller that extends `Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud` to add your first entity.

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
1) [Controllers, lists, and security](Resources/doc/Controllers.md)
2) [Actions and routing](Resources/doc/Actions.md)
3) [Fields](Resources/doc/Fields.md)
4) [Forms](Resources/doc/Forms.md)
5) [Configuring menu items and their position](Resources/doc/Menu.md)
6) [Overriding the rest of the twigs](Resources/doc/Twig.md)
7) Demo (coming soon)