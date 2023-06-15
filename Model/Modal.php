<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use JetBrains\PhpStorm\ExpectedValues;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A helper class that can convert to proper modal attributes, this way you can generate a modal for a button quickly
 */
class Modal
{

    private string $controller = 'modal';
    private ?string $ajaxTarget = null;
    private ?string $html = '<div class="spinner-border mx-auto d-flex" role="status"></div>';

    private string $modalClasses = '';

    private bool $hasUpperRightCloseButton = true;
    private bool $hasCloseButton = true;
    private string $closeButtonLabel;
    private string $closeButtonClass = '';

    private bool $hasSaveButton = false;
    private string $saveButtonlabel;
    private string $saveButtonClass = 'btn-primary';

    private string $backdrop = 'true';
    private bool $keyboard = true;
    private bool $focus = true;

    public function __construct(TranslatorInterface $translator, private string $title) {
        $this->closeButtonLabel = $translator->trans('Close');
        $this->saveButtonlabel = $translator->trans('Save');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    public function hasUpperRightCloseButton(): bool
    {
        return $this->hasUpperRightCloseButton;
    }

    public function setHasUpperRightCloseButton(bool $hasUpperRightCloseButton): self
    {
        $this->hasUpperRightCloseButton = $hasUpperRightCloseButton;
        return $this;
    }

    public function hasCloseButton(): bool
    {
        return $this->hasCloseButton;
    }

    public function setHasCloseButton(bool $hasCloseButton): self
    {
        $this->hasCloseButton = $hasCloseButton;
        return $this;
    }

    public function getCloseButtonLabel(): string
    {
        return $this->closeButtonLabel;
    }

    public function setCloseButtonLabel(string $closeButtonLabel): self
    {
        $this->closeButtonLabel = $closeButtonLabel;
        return $this;
    }

    public function getCloseButtonClass(): string
    {
        return $this->closeButtonClass;
    }

    public function setCloseButtonClass(string $closeButtonClass): self
    {
        $this->closeButtonClass = $closeButtonClass;
        return $this;
    }

    public function hasSaveButton(): bool
    {
        return $this->hasSaveButton;
    }

    public function setHasSaveButton(bool $hasSaveButton): self
    {
        $this->hasSaveButton = $hasSaveButton;
        return $this;
    }

    public function getSaveButtonlabel(): string
    {
        return $this->saveButtonlabel;
    }

    public function setSaveButtonlabel(string $saveButtonlabel): self
    {
        $this->saveButtonlabel = $saveButtonlabel;
        return $this;
    }

    public function getSaveButtonClass(): string
    {
        return $this->saveButtonClass;
    }

    public function setSaveButtonClass(string $saveButtonClass): self
    {
        $this->saveButtonClass = $saveButtonClass;
        return $this;
    }

    public function getKeyboard(): bool
    {
        return $this->keyboard;
    }

    public function setKeyboard(bool $keyboard): self
    {
        $this->keyboard = $keyboard;
        return $this;
    }

    public function getFocus(): bool
    {
        return $this->focus;
    }

    public function setFocus(bool $focus): self
    {
        $this->focus = $focus;
        return $this;
    }

    #[ExpectedValues(['true', 'false', 'static'])]
    public function getBackdrop(): string
    {
        return $this->backdrop;
    }

    public function setBackdrop(#[ExpectedValues(['true', 'false', 'static'])] string $backdrop): self
    {
        $this->backdrop = $backdrop;
        return $this;
    }

    public function getAjaxTarget(): ?string
    {
        return $this->ajaxTarget;
    }

    public function setAjaxTarget(?string $ajaxTarget): self
    {
        $this->ajaxTarget = $ajaxTarget;
        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): self
    {
        $this->html = $html;
        return $this;
    }

    public function getModalClasses(): string
    {
        return $this->modalClasses;
    }

    public function setModalClasses(string $modalClasses): self
    {
        $this->modalClasses = $modalClasses;
        return $this;
    }

    protected function booleanToString(bool $bool): string {
        return $bool ? 'true' : 'false';
    }

    public function toAttributes(): array
    {
        $controller = $this->getController();
        return [
            'data-controller' => $controller,
            "data-$controller-title-value" => $this->getTitle(),
            "data-$controller-has-upper-right-close-button-value" => $this->booleanToString($this->hasUpperRightCloseButton()),
            "data-$controller-has-close-button-value" => $this->booleanToString($this->hasCloseButton()),
            "data-$controller-classes-value" => $this->getModalClasses(),
            "data-$controller-close-button-label-value" => $this->getCloseButtonLabel(),
            "data-$controller-close-button-class-value" => $this->getCloseButtonClass(),
            "data-$controller-has-save-button-value" => $this->booleanToString($this->hasSaveButton()),
            "data-$controller-save-button-label-value" => $this->getSaveButtonlabel(),
            "data-$controller-save-button-class-value" => $this->getSaveButtonClass(),
            "data-$controller-keyboard-value" => $this->booleanToString($this->getKeyboard()),
            "data-$controller-focus-value" => $this->booleanToString($this->getFocus()),
            "data-$controller-backdrop-value" => $this->getBackdrop(),
            "data-$controller-html-value" => $this->getHtml(),
            "data-$controller-ajax-target-value" => $this->getAjaxTarget()
        ];
    }

}