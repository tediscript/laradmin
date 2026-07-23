<?php

describe('Sail documentation', function () {
    function sailDocsRepoRoot(): string
    {
        return realpath(__DIR__.'/../..');
    }

    function readmeContent(): string
    {
        return file_get_contents(sailDocsRepoRoot().'/README.md');
    }

    function contextContent(): string
    {
        return file_get_contents(sailDocsRepoRoot().'/CONTEXT.md');
    }

    function sailSection(): string
    {
        // Everything from the Sail heading to the next H2 section (or EOF).
        $readme = readmeContent();
        $start = strpos($readme, '## Docker (Laravel Sail)');
        expect($start)->not->toBeFalse('README must have a "## Docker (Laravel Sail)" section.');

        $tail = substr($readme, $start);
        $next = strpos($tail, "\n## ", 1);

        return $next === false ? $tail : substr($tail, 0, $next);
    }

    it('documents a Sail workflow as an equal alternative, host-first', function () {
        $readme = readmeContent();

        // Host Setup/Run/Test blocks come before the Sail section.
        expect(strpos($readme, '## Setup'))
            ->toBeLessThan(strpos($readme, '## Docker (Laravel Sail)'))
            ->and(strpos($readme, '## Run'))
            ->toBeLessThan(strpos($readme, '## Docker (Laravel Sail)'))
            ->and(strpos($readme, '## Test'))
            ->toBeLessThan(strpos($readme, '## Docker (Laravel Sail)'));
    });

    it('documents the host prerequisite before sail up (avoids the env circular dependency)', function () {
        $section = sailSection();

        expect($section)->toContain('composer install')
            ->and($section)->toContain('key:generate')
            ->and($section)->toContain('sail up');
    });

    it('ships a Sail-only cheat-sheet using canonical Sail command names', function () {
        $section = sailSection();

        expect($section)->toContain('sail up')
            ->and($section)->toContain('sail stop')
            ->and($section)->toContain('sail test')
            ->and($section)->toContain('sail artisan migrate')
            ->and($section)->toContain('migrate:fresh --seed')
            ->and($section)->toContain('sail tinker');
    });

    it('explains how this differs from stock Sail and links ADR #0004', function () {
        $section = sailSection();

        expect($section)->toContain('0004-adopt-laravel-sail.md')
            ->and($section)->toContain('composer dev');
    });

    it('suggests the sail alias and links the official Sail docs', function () {
        $section = sailSection();

        expect($section)->toContain("alias sail='./vendor/bin/sail'")
            ->and($section)->toContain('https://laravel.com/docs/sail');
    });

    it('keeps CONTEXT.md a pure glossary — no Getting started block', function () {
        expect(contextContent())
            ->not->toContain('Getting started')
            ->not->toContain('sail up')
            ->not->toContain('composer dev');
    });
});
