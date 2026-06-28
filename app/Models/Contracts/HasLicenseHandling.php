<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use App\Models\License;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property-read null|License $activeLicense
 */
interface HasLicenseHandling
{
    public function licenses(): MorphMany;

    public function activeLicense(): MorphOne;
}
