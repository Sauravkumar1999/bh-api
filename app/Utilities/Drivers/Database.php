<?php

namespace App\Utilities\Drivers;

use App\Contracts\TranslationLoaderContract;
use App\Models\Translation;
use Exception;

class Database implements TranslationLoaderContract
{
    /**
     * Returns all translations for the given locale, group and namespace.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     *
     * @return array
     */
    public function load(string $locale, string $group, ?string $namespace = null): array
    {
        $driver = config('translation.drivers.' . config('translation.driver'));

        $defaultModel = $driver['entity'];

        $this->isModelValid($defaultModel);

        return $defaultModel::getTranslationsForGroup($locale, $group, $namespace);
    }

    /**
     * Check if the database model implements our model
     * contract or not.
     *
     * @param string $model
     *
     * @throws \Exception
     */
    protected function isModelValid(string $model): void
    {
        $instance = new $model;

        if (!is_a($instance, Translation::class)) {
            throw new Exception($model . ' does not extend our ' . Translation::class . ' model.');
        }
    }

}
