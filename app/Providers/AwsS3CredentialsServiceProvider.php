<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Aws\Credentials\CredentialProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Visibility;

class AwsS3CredentialsServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('s3cached', function ($app, $config) {

            $s3Config = $config;
            $s3Config['version'] = 'latest';

            if (! empty($config['key']) && ! empty($config['secret'])) {
                $s3Config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
            } else {
                $provider = CredentialProvider::defaultProvider();
                $s3Config['credentials'] = CredentialProvider::memoize($provider);

            }

            $root = $s3Config['root'] ?? null;
            $options = $config['options'] ?? [];
            $streamReads = $config['stream_reads'] ?? false;

            $awsS3V3Adapter = new AwsS3V3Adapter(new S3Client($s3Config), $s3Config['bucket'], $root,
                new PortableVisibilityConverter(Visibility::PRIVATE), null, $options, $streamReads);

            return new FilesystemAdapter(new Filesystem($awsS3V3Adapter, $config), $awsS3V3Adapter, $config);
        });
    }

}
