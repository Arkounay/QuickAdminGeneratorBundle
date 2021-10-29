## Forms

* [Overriding the Form Builder directly](#overriding-the-form-builder-directly)
* [Overriding the automatic Form Builder generation by using Event Subscribers](#overriding-the-automatic-form-builder-generation-by-using-event-subscribers)
* [Overriding the form type](#overriding-the-form-type)
* [Collections](#collections)
* [Overriding the form's twig theme](#overriding-the-form-s-twig-theme)
* [Overriding the form's twig theme for a specific entity](#overriding-the-form-s-twig-theme-for-a-specific-entity)

---

?> You can override a form's field label and type through [Fields](Fields.md).
However, you can get more control by either overriding the form builder directly, or changing the FormType altogether.

### Overriding the Form Builder directly


```php
protected function buildForm($entity, bool $creation): FormBuilderInterface
{
    $builder = parent::buildForm($entity, $creation);
    
    $builder->add('custom_field', TextType::class, [
        'mapped' => false
    ]);
    
    return $builder;
}
```

this works like classic symfony forms.

### Overriding the automatic Form Builder generation by using Event Subscribers

You can override a specific form field with an event subscriber. This is useful if you want how the Form Builder generation works for certain fields. Subscribe to the `qag.events.form.field`.

For example, if you want to use a MediaType (from Artgris/MediaBundle) everytime there is a field called "image", you can do this:

```php
<?php


namespace App\Listener\AdminForm;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Field;
use Artgris\Bundle\MediaBundle\Form\Type\MediaType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormBuilderInterface;

class FormImageListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'qag.events.form.field' => 'formEvent',
        ];
    }

    public function formEvent(GenericEvent $event): void
    {
        $formBuilder = $event->getSubject();

        /** @var Field $field */
        $field = $event->getArgument('field');

        // you can also get the entity itself with $event->getArgument('entity');

        if (self::IsImage($field)) {
            /** @var FormBuilderInterface $formBuilder */
            $formBuilder->add($field->getIndex(), MediaType::class, [
                'required' => false,
                'conf' => 'default'
            ]);
            $event->stopPropagation(); // don't forget to stop the propagation
        }
    }

    private static function IsImage(Field $field): bool
    {
        return $field->getIndex() === 'image' && $field->getType() === 'string';
    }

}
```

### Overriding the form type

As we've seenn, by default a Form Builder is automatically generated and used from the Fields data. You may want to avoid that and use a classic Symfony FormType instead.

To do that, override the `overrideFormType` method, and just return the form type you want to use instead:
```php
protected function overrideFormType($entity, bool $creation): ?string
{
    return NewsType::class;
}
```
By doing this, the earlier `buildForm` method and its events will not be called anymore.



### Collections

[Ux Collection](https://github.com/arkounay/ux-collection) is included to handle collections :

```php
use Arkounay\Bundle\UxCollectionBundle\Form\UxCollectionType;
// ...
protected function buildForm($entity, bool $creation): FormBuilderInterface
{
    $builder = parent::buildForm($entity, $creation);

    $builder
        ->add('articles', UxCollectionType::class, [
            'entry_type' => ArticleType::class,
            'by_reference' => false,
            'add_label' => 'Add an article'
        ]);
    ;

    return $builder;
}
```


### Overriding the form's twig theme

- You can override `quick-admin/templates/bundles/ArkounayQuickAdminGeneratorBundle/base.html.twig` and change the used form_theme there:
```twig
{% extends '@!ArkounayQuickAdminGenerator/base.html.twig' %}

{% block form_theme %}
    {%- form_theme form 'my/custom/form/form_theme.html.twig' -%}
{% endblock %}
```

- or you can override directly the form theme by creating a file `templates/bundles/ArkounayQuickAdminGeneratorBundle/form/form_theme.html.twig` and extending it
```twig
{% extends '@!ArkounayQuickAdminGenerator/form/form_theme.html.twig' %}

{% block form_row %}
    kaboom
{% endblock %}
```

### Overriding the form's twig theme for a specific entity

To override how the form is rendered for a specific entity during creation / edition, you can create a new file in `/templates/bundles/ArkounayQuickAdminGeneratorBundle/crud/entities/[route-name]/form.html.twig`

Example:
```twig
{% extends '@ArkounayQuickAdminGenerator/crud/form.html.twig' %}

{% block form_content %}
    {{ form_start(form) }}
    <div class="row">
        <div class="col-md-6">
            {{ form_row(form.name) }}
        </div>
        <div class="col-md-6">
            {{ form_row(form.news) }}
        </div>
        {{ form_rest(form) }}
    </div>
    {{ form_end(form) }}
{% endblock %}
``` 

You can also override the twig's default path through the Controller, with the `formTwig` method:
```php
public function formTwig(): string
{
    return 'my/custom/twig/path.html.twig';
}
```

