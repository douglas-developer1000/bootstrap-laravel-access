<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\PaymentCard;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RouteModelBindingProvider extends ServiceProvider
{
    protected array $deletedBindings = [
        'prodCategoryDeleted' => ProductCategory::class,
        'payCardDeleted' => PaymentCard::class,
        'customerDeleted' => Customer::class,
        'discountDeleted' => Discount::class,
        'supplierDeleted' => Supplier::class,
        'productDeleted' => Product::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected function applyDeletedModelBinding(): void
    {
        foreach ($this->deletedBindings as $key => $model) {
            Route::bind(
                $key,
                fn(int|string $id) => $model::withTrashed()->find($id)
            );
        }
    }

    protected function applyModelListBindingRoute(): void
    {
        collect(File::allFiles(app_path('Models')))->map(
            fn($file) => 'App\\Models\\' . $file->getFilenameWithoutExtension()
        )->filter(
            fn($class) => class_exists($class)
        )->each(function ($class) {
            $routeKey = Str::of($class)->remove('App\\Models\\')->lcfirst()->append('List')->toString();

            Route::bind($routeKey, function ($value, $route) use ($class) {
                $inputKey = $route->parameter('key');
                if (!request()->has($inputKey)) {
                    throw new NotFoundHttpException();
                }

                /**
                 * @var int|string[] $idList
                 */
                $idList = request()->input($inputKey);
                if ($value === 'trashed') {
                    return $class::onlyTrashed()->findMany($idList)->all();
                }
                return $class::findMany($idList)->all();
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->applyModelListBindingRoute();
        $this->applyDeletedModelBinding();
    }
}
