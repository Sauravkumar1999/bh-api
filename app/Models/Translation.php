<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{

    /**
     * @var array
     */
    public $translatable = [
        'text',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'namespace',
        'group',
        'text',
        'key',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'text' => 'array',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('translation.table'));
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        self::saved(function ($model) {
            $model->flushGroupCache();
        });

        self::updated(function ($model) {
            $model->flushGroupCache();
        });

        self::deleted(function ($model) {
            $model->flushGroupCache();
        });
    }

    /**
     * Fetches translation line from storage and stores it in cache.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     *
     * @return array
     */
    public static function getTranslationsForGroup(string $locale, string $group, ?string $namespace = null): array
    {
        return Cache::rememberForever(static::getCacheKey($locale, $group, $namespace), function () use ($group, $locale, $namespace) {
            return static::query()
                ->where(function ($query) use ($group, $locale, $namespace) {
                    if (!is_null($namespace) && ('*' !== $namespace)) {
                        $query->where('namespace', $namespace)
                            ->where('group', $group);
                    } else {
                        $query->where('group', $group);
                    }
                })
                ->get()
                ->reduce(function ($lines, self $line) use ($locale) {
                    $translation = $line->getTranslation($locale);

                    if ($translation !== null) {
                        Arr::set($lines, $line->key, $translation);
                    }

                    return $lines;
                }) ?? [];
        });
    }

    /**
     * Build appropriate cache key for translation file.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     *
     * @return string
     */
    public static function getCacheKey(string $locale, string $group, ?string $namespace = null): string
    {
        if (is_null($namespace) || ('*' === $namespace)) {
            $namespace = 'global';
        }

        return "ths-translations.{$namespace}." . str_replace('/', '.', $group) . ".{$locale}";
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getTranslation(string $locale)
    {
        if (!isset($this->text[$locale])) {
            $fallback = config('app.fallback_locale');

            return $this->text[$fallback] ?? null;
        }

        return $this->text[$locale];
    }

    /**
     * Overwrite translation for a specific locale.
     *
     * @param string $locale
     * @param string $value
     *
     * @return void
     */
    public function setTranslation(string $locale, string $value): void
    {
        $this->text = array_merge($this->text ?? [], [$locale => $value]);
    }

    /**
     * Flush translation cache.
     *
     * @return void;
     */
    protected function flushGroupCache(): void
    {
        foreach ($this->getTranslatedLocales() as $locale) {
            Cache::forget(static::getCacheKey($locale, $this->group, $this->namespace));
        }
    }

    /**
     * Get current translation locales.
     *
     * @return array
     */
    protected function getTranslatedLocales(): array
    {
        return array_keys($this->text);
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}
