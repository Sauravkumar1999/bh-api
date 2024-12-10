<?php

namespace App\Contracts;

interface TranslationLoaderContract
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
    public function load(string $locale, string $group, ?string $namespace = null): array;
}
