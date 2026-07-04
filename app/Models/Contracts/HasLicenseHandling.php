<?php

declare(strict_types=1);

namespace App\Models\Contracts;

use App\Models\License;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property-read null|License $activeLicense
 * @property-read null|License $pendingLicense
 * @property-read Collection<License> $licenses
 */
interface HasLicenseHandling
{
    public function licenses(): MorphMany;

    public function activeLicense(): MorphOne;

    public function pendingLicense(): MorphOne;
}
