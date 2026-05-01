<?php

declare(strict_types=1);

return [
	'group' => env('PAGINATION_GROUP', 3),
	'groups' => env('PAGINATION_GROUPS', [3, 5, 10])
];
