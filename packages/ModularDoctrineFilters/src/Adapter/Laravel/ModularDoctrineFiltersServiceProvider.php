<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Adapter\Laravel;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Symplify\ModularDoctrineFilters\Contract\Filter\FilterInterface;
use Symplify\ModularDoctrineFilters\Contract\FilterManagerInterface;
use Symplify\ModularDoctrineFilters\FilterManager;
use Symplify\PackageBuilder\Adapter\Laravel\Container\DefinitionFinder;

/**
 * @property Application $app
 */
final class ModularDoctrineFiltersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FilterManagerInterface::class, function (Application $application) {
            return new FilterManager($application->make(EntityManagerInterface::class));
        });

        $this->app->alias(FilterManagerInterface::class, FilterManager::class);
    }

    public function boot(FilterManagerInterface $filterManager): void
    {
        $filterDefinitions = DefinitionFinder::findAllByType($this->app->getBindings(), FilterInterface::class);

        foreach ($filterDefinitions as $name => $definition) {
            $filterManager->addFilter($name, $this->app->make($name));
        }
    }
}
