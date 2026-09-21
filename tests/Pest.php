<?php

declare(strict_types=1);

/*
 * Bootstrap Pest — modulo Setting.
 * Ogni file test dichiara uses(\Modules\Setting\Tests\TestCase::class).
 */

pest()->extend(Modules\Setting\Tests\TestCase::class)->in(__DIR__.'/Unit', __DIR__.'/Feature');
