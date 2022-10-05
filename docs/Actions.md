# Actions and routing

- [Normal actions](#normal-actions)
- [Batch actions](#batch-actions)
- [Global actions](#global-actions)
- [Customizing how actions are rendered](#customizing-how-actions-are-rendered)
- [Modals](#modals)
- [Routing shorcuts](#routing-shorcuts)

---      

There are 3 types of actions : **normal actions**, **batch actions** and **global actions**. 
Every action will require its own route that can be quickly generated automatically.

#### Normal actions

Normal actions are action that can be used against a single item.

To add a normal action, override the `getActions()` method.
```php
public function getActions(): Actions
{
    return parent::getActions()->add('custom');
}
```

Every action leads to a custom route, so you will need to add one too in the controller. A simple function like this will work: 

```php
public function customAction($entity)
{
    $this->addFlash('success', "Custom action for entity #{$entity->getId()} triggered!");
    return $this->redirectToList();
}
```

If you check the routes, you'll see a new route has been created:

```
qag.category_custom           ANY      ANY      ANY    /admin/category/custom/{id}/
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
public function getBatchActions(): Actions
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

If you need to remove every batch actions, you can simply override getBatchActions and return en empty actions `new Actions()`. This will remove the checkboxes altogether:

```php
public function getBatchActions(): Actions
{
    return new Actions();
}
```

#### Global actions

Global actions are actions that don't apply on an existing item, such as "Create".
Here's an example:

```php
public function getGlobalActions(): Actions
{
    return parent::getGlobalActions()->add('stats');
}

public function statsAction(): Response
{
    // do something
}
```

![Customizing Actions](https://raw.githubusercontent.com/Arkounay/QuickAdminGeneratorBundle/master/docs/images/actions-global.png)

If you check the routes just like for "normal" actions, you'll see a small difference:

```
qag.category_stats           ANY      ANY      ANY    /admin/category/stats
```

There is no `/{id}`, because QAG detects that `statsAction` doesn't have a dependency on an untyped parameter or a parameter that is the same type as the entity's controller.


#### Modals

Through a built-in stimulus controller, you can configure an action to open a modal instead of a link. The `Modal` class can help you to do that easily.

```php
public function getActions($entity): Actions
{
    $actions = parent::getActions($entity);

    // create a modal with custom html
    $modalAction = new Action('modal');
    $modal = new Modal($this->translator, $entity->getName());
    $modal->setHtml("Hi, my name is <strong>{$entity->getName()}</strong>");
    $modalAction->setModal($modal);
    $actions->add($modalAction);

    // or through ajax
    $ajaxModalAction = new Action('Ajax modal');
    $modal = new Modal($this->translator, 'Ajax modal');
    $modal->setAjaxTarget($this->generateUrl('qag.category_modal_detail', ['id' => $entity->getId()]));
    $ajaxModalAction->setModal($modal);
    $actions->add($ajaxModalAction);

    return $actions;
}
```

The modal class has many options and uses bootstrap's modal system:

- `setTitle`: The title of the modal
- `setAjaxTarget`: The optional url that will be fetched when the modal opens, its data will replace the modal's body. Emty by default.
- `setHtml`: The HTML that will be displayed when the modal opens. By default, it's a loader.
- `setModalClasses`: The classes that will be appended after `modal-dialog`. For example, specify `modal-dialog-centered` to vertically center a modal (checkout bootstrap's documention for more information)
- `setHasUpperRightCloseButton`: If the modal has an upper right close button (x)
- `setHasCloseButton`: If the modal has a close button
- `setCloseButtonLabel`: The label of the close button
- `setCloseButtonClass`: The class of the close button
- `setHasSaveButton`: If the modal has a save button. When a save button is pressed, the form inside the modal's body will be submitted if there is one.
- `setSaveButtonlabel`: The label of the save button
- `setSaveButtonClass`: The class of the save button (default "btn-primary")
- `setBackdrop`: The backdrop value (check bootstrap's documentation to know more)
- `setKeyboard`: If the modal can be closed by pressing esc
- `setFocus`: If the modal takes the focus when it opens

A modal can also be open by calling the corresponding stimulus controller manually:
```html
<button data-controller="modal" data-modal-title-value="Modal" data-modal-html-value="Simple modal">
    Simple modal
</button>
```


#### Customizing how actions are rendered

You can use the Action class to change how actions are rendered:

```php
public function getActions(): Actions
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

By default, every function ending with "Action" will create a new route.
There are some extra tips.
- If the function name contains either `Get` or `Post`, it will only accept the corresponding request methods
- If the function name contains `Json` or `Api` it will have default `json` format

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
  qag.category_custom_get          GET      ANY      ANY    /admin/category/customGet/{id}/   
  qag.category_custom_post         POST     ANY      ANY    /admin/category/customGlobalPost 
```

Keep in mind you can just use classic symfony routes if your function names doesn't end with `Action`.