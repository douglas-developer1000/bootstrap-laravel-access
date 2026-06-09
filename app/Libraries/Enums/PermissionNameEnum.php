<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum PermissionNameEnum: string
{
    case HEADER_SETTINGS = 'header-settings';


    case CUSTOMER_INDEX = 'customer-index';
    case CUSTOMER_CREATE = 'customer-create';
    case CUSTOMER_STORE = 'customer-store';
    case CUSTOMER_SHOW = 'customer-show';
    case CUSTOMER_EDIT = 'customer-edit';
    case CUSTOMER_UPDATE = 'customer-update';
    case CUSTOMER_DESTROY = 'customer-destroy';
    case CUSTOMER_RESTORE = 'customer-restore';


    case STOCK_ENTRY_CREATE = 'stock-entry-create';
    case STOCK_ENTRY_SPEND = 'stock-entry-spend';
    case STOCK_ENTRY_STORE = 'stock-entry-store';


    case PRODUCT_INDEX = 'product-index';
    case PRODUCT_SHOW = 'product-show';
    case PRODUCT_CREATE = 'product-create';
    case PRODUCT_EDIT = 'product-edit';
    case PRODUCT_STORE = 'product-store';
    case PRODUCT_UPDATE = 'product-update';
    case PRODUCT_DESTROY = 'product-destroy';
    case PRODUCT_RESTORE = 'product-restore';


    case PRODUCT_CATEGORY_INDEX = 'product-category-index';
    case PRODUCT_CATEGORY_SHOW = 'product-category-show';
    case PRODUCT_CATEGORY_EDIT = 'product-category-edit';
    case PRODUCT_CATEGORY_UPDATE = 'product-category-update';
    case PRODUCT_CATEGORY_CREATE = 'product-category-create';
    case PRODUCT_CATEGORY_STORE = 'product-category-store';
    case PRODUCT_CATEGORY_DESTROY = 'product-category-destroy';
    case PRODUCT_CATEGORY_RESTORE = 'product-category-restore';


    case SUPPLIER_INDEX = 'supplier-index';
    case SUPPLIER_CREATE = 'supplier-create';
    case SUPPLIER_STORE = 'supplier-store';
    case SUPPLIER_DESTROY = 'supplier-destroy';
    case SUPPLIER_EDIT = 'supplier-edit';
    case SUPPLIER_SHOW = 'supplier-show';
    case SUPPLIER_UPDATE = 'supplier-update';
    case SUPPLIER_RESTORE = 'supplier-restore';


    case DISCOUNT_INDEX = 'discount-index';
    case DISCOUNT_CREATE = 'discount-create';
    case DISCOUNT_SHOW = 'discount-show';
    case DISCOUNT_EDIT = 'discount-edit';
    case DISCOUNT_STORE = 'discount-store';
    case DISCOUNT_UPDATE = 'discount-update';
    case DISCOUNT_DESTROY = 'discount-destroy';
    case DISCOUNT_RESTORE = 'discount-restore';


    case PAYMENT_CARD_INDEX = 'payment-card-index';
    case PAYMENT_CARD_SHOW = 'payment-card-show';
    case PAYMENT_CARD_CREATE = 'payment-card-create';
    case PAYMENT_CARD_EDIT = 'payment-card-edit';
    case PAYMENT_CARD_UPDATE = 'payment-card-update';
    case PAYMENT_CARD_STORE = 'payment-card-store';
    case PAYMENT_CARD_DESTROY = 'payment-card-destroy';
    case PAYMENT_CARD_RESTORE = 'payment-card-restore';

    /** ================================================================
     *        Permissions for stock-exit as SALE
     * 
     *    Model prevalent:
     *    @see App\Models\Sale::class
     *  ================================================================
     */
    case SALE_INDEX = 'sale-index';
    case SALE_SHOW = 'sale-show';
    case SALE_CREATE = 'sale-create';
    case SALE_STORE = 'sale-store';
    case SALE_DESTROY = 'sale-destroy';

    /** ================================================================
     *        Permissions for stock-exit as EXCHANGE
     * 
     *    Model prevalent:
     *    @see App\Models\Exchange::class
     * 
     *  OBS: SHOW permission is not required because the exchanges are
     *  shown at a unique screen:
     *  @see view('pages.exchanges.index')
     *  ================================================================
     */

    case EXCHANGE_INDEX = 'exchange-index';
    case EXCHANGE_CREATE = 'exchange-create';
    case EXCHANGE_DESTROY = 'exchange-destroy';
    case EXCHANGE_STORE = 'exchange-store';

    /** ================================================================
     *        Permissions for stock-exit as PERSONAL_USE
     * 
     *  ================================================================
     */
    case PERSONAL_USE_SHOW = 'personal-use-show';
    case PERSONAL_USE_CREATE = 'personal-use-create';
    case PERSONAL_USE_STORE = 'personal-use-store';
    case PERSONAL_USE_DESTROY = 'personal-use-destroy';

    /** ================================================================
     *        Permissions for stock-exit as DEMONSTRATION
     * 
     *  ================================================================
     */
    case DEMONSTRATION_SHOW = 'demonstration-show';
    case DEMONSTRATION_CREATE = 'demonstration-create';
    case DEMONSTRATION_STORE = 'demonstration-store';
    case DEMONSTRATION_DESTROY = 'demonstration-destroy';

    /** ================================================================
     *        Permissions for stock-exit as LOSS
     * 
     *  ================================================================
     */
    case LOSS_SHOW = 'loss-show';
    case LOSS_CREATE = 'loss-create';
    case LOSS_STORE = 'loss-store';
    case LOSS_DESTROY = 'loss-destroy';

    /** ================================================================
     *        Permissions for stock-exit as:
     *  - PERSONAL_USE
     *  - DEMONSTRATION
     *  - LOSS
     * 
     *  ================================================================
     */
    case GARBAGE_INDEX = 'garbage-index';
}
