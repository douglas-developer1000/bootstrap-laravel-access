<?php

declare(strict_types=1);

namespace App\View\Components\Molecules;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentCard;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Exchange;
use App\Models\StockExit;

final class UserMenuItems extends MenuItem
{
    public function __construct()
    {
        parent::__construct([
            'Clientes' => [
                route('customers.index'),
                ['viewAny', Customer::class]
            ],
            'Estoque' => [
                route('stocks.index'),
                ['viewAny', Product::class]
            ],
            'Fornecedores' => [
                route('suppliers.index'),
                ['viewAny', Supplier::class]
            ],
            'Categorias (Produto)' => [
                route('product-categories.index'),
                ['viewAny', ProductCategory::class]
            ],
            'Descontos' => [
                route('discounts.index'),
                ['viewAny', Discount::class]
            ],
            'Cartões' => [
                route('payment-cards.index'),
                ['viewAny', PaymentCard::class]
            ],
            'Vendas' => [
                route('sales.index'),
                ['viewAny', Sale::class]
            ],
            'Trocas' => [
                route('exchanges.index'),
                ['viewExchangeAny', StockExit::class]
            ],
            'Perdas' => [
                route('losses.index'),
                ['viewLossAny', StockExit::class]
            ],
        ]);
    }
}
