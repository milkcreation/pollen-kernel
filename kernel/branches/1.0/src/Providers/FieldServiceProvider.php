<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Field\FieldManager;
use Pollen\Field\FieldManagerInterface;
use Pollen\Field\Drivers\ButtonDriver;
use Pollen\Field\Drivers\CheckboxDriver;
use Pollen\Field\Drivers\CheckboxCollectionDriver;
use Pollen\Field\Drivers\ColorpickerDriver;
use Pollen\Field\Drivers\DatepickerDriver;
use Pollen\Field\Drivers\DatetimeJsDriver;
use Pollen\Field\Drivers\FileDriver;
use Pollen\Field\Drivers\FileJsDriver;
use Pollen\Field\Drivers\HiddenDriver;
use Pollen\Field\Drivers\LabelDriver;
use Pollen\Field\Drivers\NumberDriver;
use Pollen\Field\Drivers\NumberJsDriver;
use Pollen\Field\Drivers\PasswordDriver;
use Pollen\Field\Drivers\PasswordJsDriver;
use Pollen\Field\Drivers\RadioDriver;
use Pollen\Field\Drivers\RadioCollectionDriver;
use Pollen\Field\Drivers\RepeaterDriver;
use Pollen\Field\Drivers\RequiredDriver;
use Pollen\Field\Drivers\SelectDriver;
use Pollen\Field\Drivers\SelectImageDriver;
use Pollen\Field\Drivers\SelectJsDriver;
use Pollen\Field\Drivers\SubmitDriver;
use Pollen\Field\Drivers\SuggestDriver;
use Pollen\Field\Drivers\TextDriver;
use Pollen\Field\Drivers\TextareaDriver;
use Pollen\Field\Drivers\TextRemainingDriver;
use Pollen\Field\Drivers\TinymceDriver;
use Pollen\Field\Drivers\ToggleSwitchDriver;
use Pollen\Container\BootableServiceProvider;

class FieldServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        FieldManagerInterface::class,
        ButtonDriver::class,
        CheckboxDriver::class,
        CheckboxCollectionDriver::class,
        ColorpickerDriver::class,
        FileDriver::class,
        FileJsDriver::class,
        DatepickerDriver::class,
        DatetimeJsDriver::class,
        HiddenDriver::class,
        LabelDriver::class,
        NumberDriver::class,
        NumberJsDriver::class,
        PasswordDriver::class,
        PasswordJsDriver::class,
        RadioDriver::class,
        RadioCollectionDriver::class,
        RepeaterDriver::class,
        RequiredDriver::class,
        SelectDriver::class,
        SelectImageDriver::class,
        SelectJsDriver::class,
        SubmitDriver::class,
        SuggestDriver::class,
        TextDriver::class,
        TextareaDriver::class,
        TextRemainingDriver::class,
        TinymceDriver::class,
        ToggleSwitchDriver::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            FieldManagerInterface::class,
            function () {
                return new FieldManager([], $this->getContainer());
            }
        );

        $this->registerDrivers();
    }

    /**
     * Déclaration des pilotes par défaut.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(
            ButtonDriver::class,
            function () {
                return new ButtonDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            CheckboxDriver::class,
            function () {
                return new CheckboxDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            CheckboxCollectionDriver::class,
            function () {
                return new CheckboxCollectionDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            ColorpickerDriver::class,
            function () {
                return new ColorpickerDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            DatepickerDriver::class,
            function () {
                return new DatepickerDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            DatetimeJsDriver::class,
            function () {
                return new DatetimeJsDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            FileDriver::class,
            function () {
                return new FileDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            FileJsDriver::class,
            function () {
                return new FileJsDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            HiddenDriver::class,
            function () {
                return new HiddenDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            LabelDriver::class,
            function () {
                return new LabelDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            NumberDriver::class,
            function () {
                return new NumberDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            NumberJsDriver::class,
            function () {
                return new NumberJsDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            PasswordDriver::class,
            function () {
                return new PasswordDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            PasswordJsDriver::class,
            function () {
                return new PasswordJsDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            RadioDriver::class,
            function () {
                return new RadioDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            RadioCollectionDriver::class,
            function () {
                return new RadioCollectionDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            RepeaterDriver::class,
            function () {
                return new RepeaterDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            RequiredDriver::class,
            function () {
                return new RequiredDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SelectDriver::class,
            function () {
                return new SelectDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SelectImageDriver::class,
            function () {
                return new SelectImageDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SelectJsDriver::class,
            function () {
                return new SelectJsDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SubmitDriver::class,
            function () {
                return new SubmitDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SuggestDriver::class,
            function () {
                return new SuggestDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            TextDriver::class,
            function () {
                return new TextDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            TextareaDriver::class,
            function () {
                return new TextareaDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            TextRemainingDriver::class,
            function () {
                return new TextRemainingDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            TinymceDriver::class,
            function () {
                return new TinymceDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            ToggleSwitchDriver::class,
            function () {
                return new ToggleSwitchDriver($this->getContainer()->get(FieldManagerInterface::class));
            }
        );
    }
}