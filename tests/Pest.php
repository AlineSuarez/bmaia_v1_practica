<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Unit: solo el TestCase base
uses(TestCase::class)->in('Unit');

// Feature: TestCase + RefreshDatabase para TODOS los tests de Feature
uses(TestCase::class, RefreshDatabase::class)->in('Feature');
