<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandStatus extends Model
{
    use HasFactory;

    /**
     * Get the recruitment partner (partner office) assigned to this candidate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partneroffice()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the flight's departure city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flightFromCity()
    {
        return $this->belongsTo(City::class, 'flight_from_city');
    }

    /**
     * Get the flight's destination city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flightToCity()
    {
        return $this->belongsTo(City::class, 'flight_to_city');
    }
}
