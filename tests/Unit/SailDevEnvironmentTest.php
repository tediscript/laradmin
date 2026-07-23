<?php

use Symfony\Component\Yaml\Yaml;

describe('Laravel Sail dev environment', function () {
    function repoRoot(): string
    {
        return realpath(__DIR__.'/../..');
    }

    function sailComposePath(): string
    {
        return repoRoot().'/compose.yaml';
    }

    it('ships a Sail-managed compose file', function () {
        expect(file_exists(sailComposePath()))->toBeTrue(
            'compose.yaml must exist (Sail-managed Docker dev environment).',
        );

        $compose = file_get_contents(sailComposePath());

        // Built from Sail's runtime image, not a hand-rolled Dockerfile.
        expect($compose)->toContain('vendor/laravel/sail/runtimes/8.5')
            ->and($compose)->toContain('sail-8.5/app');
    });

    it('runs the composer dev orchestration inside the app container', function () {
        $compose = file_get_contents(sailComposePath());

        expect($compose)->toContain('composer dev');
    });

    it('isolates the container node_modules from the host (native bindings)', function () {
        $services = Yaml::parseFile(sailComposePath())['services'];

        expect($services['laravel.test']['volumes'])->toContain('sail-node-modules:/var/www/html/node_modules')
            ->and($services)->toHaveKey('node-setup');
    });

    it('is sqlite-only — no database/cache/mail services', function () {
        $services = Yaml::parseFile(sailComposePath())['services'];

        // Only the app container and its one-shot node bootstrap may exist.
        expect($services)->toHaveKeys(['laravel.test', 'node-setup'])
            ->and($services)->toHaveCount(2);

        collect(['mysql', 'mariadb', 'pgsql', 'mongodb', 'redis', 'valkey', 'memcached', 'mailpit', 'meilisearch', 'typesense', 'minio', 'selenium', 'rabbitmq'])
            ->each(fn ($service) => expect($services)->not->toHaveKey($service));
    });

    it('exposes the artisan serve and Vite ports', function () {
        $ports = collect(Yaml::parseFile(sailComposePath())['services']['laravel.test']['ports'])->implode("\n");

        expect($ports)->toContain('8000')
            ->and($ports)->toContain('5173');
    });

    it('does not retain any hand-rolled Docker artifacts', function () {
        // PR #10's hand-rolled approach (superseded by Sail) must be gone.
        collect(['Dockerfile', 'Dockerfile.dev', 'docker-compose.yml', 'docker-compose.yaml', '.dockerignore', 'docker-entrypoint.dev.sh'])
            ->each(fn ($artifact) => expect(file_exists(repoRoot().'/'.$artifact))
                ->toBeFalse("Hand-rolled Docker artifact [{$artifact}] must not exist — use Sail."));
    });

    it('requires laravel/sail as a dev dependency', function () {
        $composer = json_decode(file_get_contents(repoRoot().'/composer.json'), true);

        expect($composer['require-dev'])->toHaveKey('laravel/sail');
    });

    it('binds the dev servers on 0.0.0.0 so they are reachable from the host', function () {
        $composer = json_decode(file_get_contents(repoRoot().'/composer.json'), true);
        $devScript = collect($composer['scripts']['dev'])->implode(' ');

        expect($devScript)->toContain('php artisan serve --host=0.0.0.0');

        $vite = file_get_contents(repoRoot().'/vite.config.js');
        expect($vite)->toContain("host: '0.0.0.0'");
    });

    it('keeps sqlite as the testing database', function () {
        $phpunit = file_get_contents(repoRoot().'/phpunit.xml');

        expect($phpunit)->toContain('<env name="DB_CONNECTION" value="sqlite"/>')
            ->and($phpunit)->toContain('<env name="DB_DATABASE" value=":memory:"/>');
    });
});
