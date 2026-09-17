<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employercandidate extends Model
{
    use HasFactory;

    /**
     * Get the employerpluses that owns the Employercandidate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emp()
    {
        return $this->belongsTo(Employerplus::class);
    }

    /**
     * Get the cand that owns the Employercandidate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cand()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the proff that owns the Employercandidate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proff()
    {
        return $this->belongsTo(Profession::class);
    }

    public function candidate() {
        return $this->belongsTo(Candidate::class, 'cand_id', 'id');
    }

    /**
     * Get the base employer that owns the Employercandidate
     * (set instead of emp_id when the assignment came from the
     * base Employer screen rather than Employer Plus).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emp2()
    {
        return $this->belongsTo(Employer::class, 'emp2_id');
    }

}
