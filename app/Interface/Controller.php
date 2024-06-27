<?php

declare(strict_types=1);

namespace App\Interface;

interface Controller
{
    public function run(): void;
}