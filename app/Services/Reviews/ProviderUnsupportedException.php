<?php

namespace App\Services\Reviews;

use RuntimeException;

/**
 * Not a failure — the provider's official API doesn't allow fetching
 * reviews under the current access level (e.g. Yelp Fusion's review
 * endpoint requires special partner access most keys don't have). Thrown
 * instead of silently returning an empty list so the admin sees an honest
 * "Requires API Access" status rather than a misleading "0 new reviews".
 */
class ProviderUnsupportedException extends RuntimeException {}
