# Actions and routing

- [Normal actions](#normal-actions)
- [Batch actions](#batch-actions)
- [Global actions](#global-actions)
- [Customizing how actions are rendered](#customizing-how-actions-are-rendered)
- [Routing shorcuts](#routing-shorcuts)

---      

There are 3 types of actions : **normal actions**, **batch actions** and **global actions**. 
Every action will require its own route that can be quickly generated automatically.

#### Normal actions

Normal actions are action that can be used against a single item.

To add a normal action, override the `getActions()` method.
```php
public function getActions(): ?Actions
{
    return parent::getActions()->add('custom');
}
```

Every actions lead to a custom route, so you will need to add one too in the controller. A simple function like this will work: 

```php
public function customAction($entity)
{
    $this->addFlash('success', "Custom action for entity #{$entity->getId()} triggered!");
    return $this->redirectToList();
}
```

If you check the routes, you'll see a new route has been created:

```
qag.category_custom           ANY      ANY      ANY    /hadmin/category/custom/{id}/
```

That's because QAG Admin assumes every function ending with "Action" are a new route.
You are not forced to use the `Action` suffix, you can create routes the Classic symfony way:

```php
/**
 * @Route("/admin/custom-route/{id}", name="qag.category_custom")
 */
public function customAction($entity)
{
    $this->addFlash('success', "Custom action for entity #{$entity->getId()} triggered!");
    return $this->redirectToList();
}
```
and this will work too.

The default route expected by actions and generates by automatic routes is `qag.[controller_route_name]_[action_name]`.

So, for example if you need to add a "Show", or a "Duplicate" action, you can use this method.
Extra actions are automatically put in a dropdown.

![Actions](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/docs/images/actions.png)

#### Batch actions

Batch actions works the same way as regular action, except they can affect multiple entities at once. Here's an example:

```php
public function getBatchActions(): ?Actions
{
    return parent::getBatchActions()->add('archive');
}

public function archiveBatchAction(): RedirectResponse
{
    if (!$this->isCsrfTokenValid('batch', $this->request->request->get('token'))) {
        return $this->redirectToList();
    }
    $checked = $this->request->request->get('batch-actions');
    foreach ($checked as $k => $v) {
        $entity = $this->repository->find($k);
        // do something with $entity, like $entity->softDelete()...
    }
    $this->em->flush();
    
    return $this->redirectToList();
}
```

![Batch Actions](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/docs/images/actions-batch.png)

If you need to remove batch actions, you can simply override getBatchActions and return `null`. This will remove the checkboxes altogether:

```php
public function getBatchActions(): ?Actions
{
    return null;
}
```

#### Global actions

Global actions are actions that don't apply on an existing item, such as "Create".
Here's an example:

```php
public function getGlobalActions(): ?Actions
{
    return parent::getGlobalActions()->add('export');
}

public function exportAction(): Response
{
    // do something
}
```

![Customizing Actions](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/docs/images/actions-global.png)

If you check the routes just like for "normal" actions, you'll see a small difference:

```
qag.category_export           ANY      ANY      ANY    /hadmin/category/export
```

There is no `/{id}`, because QAG detects that `export` is on the Global Actions list for this controller.


#### Customizing how actions are rendered

You can use the Action class to change how actions are rendered:

```php
public function getActions(): ?Actions
{
    $customAction = new Action('custom');
    $customAction->setLabel('My custom label');
    $customAction->addDropDownClass('text-green');
    
    return parent::getActions()->add($customAction);
}
```

![Customizing Actions](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/docs/images/actions-custom.png)

This can be useful to change a button's appearance.

Note that Actions have multiple variables, such as icon, class, dropdown class, custom href... that can be used or not depending on the context. 

For more control over how actions are displayed, you will need to override their corresponding twig. 


#### Routing shorcuts

As we've seen, every function ending with "Action" will create a new route.
There are some extra tips.
- if you want to force a special GET/POST type, you can prefix "Action" with their type
- If you want a global route (that doesn't contain `/{id}/`) and for some reason your Action isn't in the Global Action list, you can write "Global" in its name to remove the id.

```php
public function customGetAction()
{
    // ...
}

public function customGlobalPostAction()
{
    // ...
}
```

will generate
```
  qag.category_customGet          GET      ANY      ANY    /hadmin/category/customGet/{id}/   
  qag.category_customGlobalPost   POST     ANY      ANY    /hadmin/category/customGlobalPost 
```

Keep in mind you can just use classic symfony routes.