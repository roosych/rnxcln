<?php

namespace App\Services\Reviews;

use RuntimeException;

/** The external API call failed — network error, expired auth, rate limit. */
class ProviderSyncException extends RuntimeException {}
