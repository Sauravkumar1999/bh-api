<?php

namespace App\Utilities;

use App\Contracts\TranslationLoaderContract;
use Exception;
use Illuminate\Translation\FileLoader;

class TranslationLoaderManager extends FileLoader
{

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        $fileTranslations = parent::load($locale, $group, $namespace);

        $loaderTranslations = $this->getTranslationsFromLoader($locale, $group, $namespace);

        if ('native' === config('translation.preferred_loader')) {
            return array_replace_recursive($loaderTranslations, $fileTranslations);
        }

        return array_replace_recursive($fileTranslations, $loaderTranslations);
    }

    /**
     * Load the messages for the given locale from custom drivers.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     *
     * @return array
     */
    protected function getTranslationsFromLoader(
        string $locale,
        string $group,
        ?string $namespace = null
    ): array
    {
        $driver = config('translation.drivers.' . config('translation.driver'));

        $this->isLoaderValid($driver['loader']);

        $loader = new $driver['loader'];

        return $loader->load($locale, $group, $namespace);
    }

    /**
     * Check if the translation loader driver implements our
     * translation loader contract or not.
     *
     * @param string $loader
     *
     * @throws \Exception
     */
    protected function isLoaderValid(string $loader): void
    {
        $instance = new $loader;

        if (!in_array(TranslationLoaderContract::class, class_implements($instance))) {
            throw new Exception($loader . ' does not implement our ' . TranslationLoaderContract::class . ' interface');
        }
    }
}
