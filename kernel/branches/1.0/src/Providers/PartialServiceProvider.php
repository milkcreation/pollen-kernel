<?php

declare(strict_types=1);

namespace Pollen\Kernel\Providers;

use Pollen\Partial\PartialManager;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Partial\Drivers\AccordionDriver;
use Pollen\Partial\Drivers\BreadcrumbDriver;
use Pollen\Partial\Drivers\BurgerButtonDriver;
use Pollen\Partial\Drivers\CookieNoticeDriver;
use Pollen\Partial\Drivers\CurtainMenuDriver;
use Pollen\Partial\Drivers\DropdownDriver;
use Pollen\Partial\Drivers\DownloaderDriver;
use Pollen\Partial\Drivers\FlashNoticeDriver;
use Pollen\Partial\Drivers\GridTableDriver;
use Pollen\Partial\Drivers\HolderDriver;
use Pollen\Partial\Drivers\ImageLightboxDriver;
use Pollen\Partial\Drivers\MenuDriver;
use Pollen\Partial\Drivers\ModalDriver;
use Pollen\Partial\Drivers\NoticeDriver;
use Pollen\Partial\Drivers\ProgressDriver;
use Pollen\Partial\Drivers\SidebarDriver;
use Pollen\Partial\Drivers\SliderDriver;
use Pollen\Partial\Drivers\SpinnerDriver;
use Pollen\Partial\Drivers\TabDriver;
use Pollen\Partial\Drivers\TagDriver;
use Pollen\Container\BaseServiceProvider;

class PartialServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        PartialManagerInterface::class,
        AccordionDriver::class,
        BreadcrumbDriver::class,
        BurgerButtonDriver::class,
        CookieNoticeDriver::class,
        CurtainMenuDriver::class,
        DropdownDriver::class,
        FlashNoticeDriver::class,
        HolderDriver::class,
        GridTableDriver::class,
        ImageLightboxDriver::class,
        MenuDriver::class,
        ModalDriver::class,
        NoticeDriver::class,
        ProgressDriver::class,
        SidebarDriver::class,
        SliderDriver::class,
        SpinnerDriver::class,
        TabDriver::class,
        TagDriver::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            PartialManagerInterface::class,
            function () {
                return new PartialManager([], $this->getContainer());
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
            AccordionDriver::class,
            function () {
                return new AccordionDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            BreadcrumbDriver::class,
            function () {
                return new BreadcrumbDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            BurgerButtonDriver::class,
            function () {
                return new BurgerButtonDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            CookieNoticeDriver::class,
            function () {
                return new CookieNoticeDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            CurtainMenuDriver::class,
            function () {
                return new CurtainMenuDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            DropdownDriver::class,
            function () {
                return new DropdownDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            DownloaderDriver::class,
            function () {
                return new DownloaderDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            FlashNoticeDriver::class,
            function () {
                return new FlashNoticeDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            GridTableDriver::class,
            function () {
                return new GridTableDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            HolderDriver::class,
            function () {
                return new HolderDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            ImageLightboxDriver::class,
            function () {
                return new ImageLightboxDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            MenuDriver::class,
            function () {
                return new MenuDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            ModalDriver::class,
            function () {
                return new ModalDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            NoticeDriver::class,
            function () {
                return new NoticeDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            ProgressDriver::class,
            function () {
                return new ProgressDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SidebarDriver::class,
            function () {
                return new SidebarDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SliderDriver::class,
            function () {
                return new SliderDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SpinnerDriver::class,
            function () {
                return new SpinnerDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            TabDriver::class,
            function () {
                return new TabDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );

        $this->getContainer()->add(
            TagDriver::class,
            function () {
                return new TagDriver($this->getContainer()->get(PartialManagerInterface::class));
            }
        );
    }
}