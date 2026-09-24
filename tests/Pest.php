<?php

declare(strict_types=1);
use Modules\Setting\Tests\TestCase;

/*
 * Bootstrap Pest — modulo Setting.
 * Ogni file test dichiara uses(\Modules\Setting\Tests\TestCase::class).
 */

pest()->extend(TestCase::class)->in(__DIR__.'/Unit', __DIR__.'/Feature');
