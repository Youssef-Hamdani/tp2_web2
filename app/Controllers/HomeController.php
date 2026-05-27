<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\BaseController;
use App\Repositories\ProductRepository;
use App\Services\PricingService;

final class HomeController extends BaseController
{
    public function index(): void
    {
        $products = Auth::check() ? (new ProductRepository())->allAvailable() : [];
        $pricing = new PricingService();

        $this->view('home/index', [
            'products' => $products,
            'pricing' => $pricing,
        ]);
    }
}
