<?php

namespace App\Repositories;

use App\Models\Referrals;
use Illuminate\Support\Facades\DB;

class ReferralsRepository extends BaseRepository
{
    protected $rferrals;

    public function __construct(Referrals $referrals)
    {
        parent::__construct($referrals);
        $this->referrals = $referrals;
    }
}