<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    /** A request that came in either from the contact form or the call-back widget. */
    public const SOURCE_CONTACT_FORM = 'contact_form';

    public const SOURCE_CALLBACK = 'callback';

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_BOOKED = 'booked';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'source',
        'name',
        'phone',
        'service',
        'zip',
        'preferred_date',
        'message',
        'status',
    ];
}
