<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Queries\TradAorWEnrolled;

class TradReportController extends Controller
{
    public function index()
    {

        $results = TradAorWEnrolled::get('20191');
        // dd($results);
        // tinker($results);

        $all_count = $results->count();

        // filter for full-time
        $fulltime_count = $results
            ->filter(function ($item) {
                return $item['TU_CREDIT_ENRL'] >= 12;
              })
            ->count();

        $parttime_count = $results
            ->filter(function ($item) {
                return $item['TU_CREDIT_ENRL'] < 12;
              })
            ->count();

        $all_by_entry_type = $results
            ->groupBy('ETYP_ID');


          dd($all_by_entry_type);
          // dd($all_count, $fulltime_count, $parttime_count);


      // $results = DB::table($sr_term)
      //   ->where($sr_term . '.TERM_ID', '20201')
      //   ->count();

        // tinker($results);
      // dd($results);

      // $results->count();

      // ->join($right, $right.'.NAME_ID', '=', $name.'.NAME_ID')

      // example of join on composite keys!
      // $results = DB::table($sr_athlete)
      // 	->where($sr_athlete.'.TERM_ID', '20201')
      // 	// ->select($right.'.ETYP_ID', $left.'.TU_CREDIT_ENRL')
      //   ->get();

      //   })

      // ->whereIn('STUD_STATUS', ['A', 'W'])
      // ->where('PRGM_ID1', 'like', 'TR%')
      // ->select($right.'.ETYP_ID', $left.'.TU_CREDIT_ENRL')
      //   ->get();
    }
}
