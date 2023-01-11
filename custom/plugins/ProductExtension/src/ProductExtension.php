<?php declare(strict_types=1);

namespace ProductExtension;

use ProductExtension\Services\ProductCustomFieldService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class ProductExtension extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $service = $this->makeProductCustomFieldSetService();
        $service->createProductCustomFieldSet($installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        $service = $this->makeProductCustomFieldSetService();
        $service->deleteProductCustomFieldSet($uninstallContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $service = $this->makeProductCustomFieldSetService();
        $service->activateProductCustomField($activateContext->getContext());
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        parent::deactivate($deactivateContext);
        $service = $this->makeProductCustomFieldSetService();
        $service->deactivateProductCustomField($deactivateContext->getContext());
    }


    private function makeProductCustomFieldSetService(): ProductCustomFieldService
    {
        /** @var EntityRepository $repository */
        $repository = $this->container->get('custom_field_set.repository');
        return new ProductCustomFieldService($repository);
    }
}
