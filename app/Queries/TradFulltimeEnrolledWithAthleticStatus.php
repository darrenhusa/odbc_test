<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class TradFulltimeEnrolledWithAthleticStatus
{
    public static function get($term)
    {
      $sr_athlete = 'CCSJ_PROD.SR_STUD_TERM_ACT';
      // $at_athlete = 'CCSJ_PROD.AT_ADMI_ACTIV';

      $sr_term = 'CCSJ_PROD.SR_STUDENT_TERM';
      $sr_term_credits = 'CCSJ_PROD.SR_ST_TERM_CRED';
      $name = 'CCSJ_PROD.CCSJ_CO_V_NAME';

      $query = DB::table($sr_term)
      	 ->where($sr_term.'.TERM_ID', $term)
         ->whereIn($sr_term.'.STUD_STATUS', ['A', 'W'])
         ->where($sr_term.'.PRGM_ID1', 'like', 'TR%')
      	 ->join($name, $sr_term.'.NAME_ID', '=', $name.'.NAME_ID')
      	 ->join($sr_term_credits, function ($join) use ($sr_term, $sr_term_credits) {
        		 $join->on($sr_term.'.NAME_ID', '=', $sr_term_credits.'.NAME_ID');
             $join->on($sr_term.'.TERM_ID', '=', $sr_term_credits.'.TERM_ID');
      })
      ->leftJoin($sr_athlete, function ($join) use ($sr_term, $sr_athlete) {
        		$join->on($sr_term.'.NAME_ID', '=', $sr_athlete.'.NAME_ID');
              $join->on($sr_term.'.TERM_ID', '=', $sr_athlete.'.TERM_ID');
      })
      ->select($sr_term.'.TERM_ID',
               $name.'.DFLT_ID',
               $name.'.LAST_NAME',
               $name.'.FIRST_NAME',
               // $sr_term.'.STUD_STATUS',
               $sr_term.'.ETYP_ID',
               // $sr_term.'.PRGM_ID1',
      		     // $sr_term.'.MAMI_ID_MJ1',
              $sr_term_credits.'.TU_CREDIT_ENRL',
              $sr_athlete.'.ACTI_ID')
        ->where($sr_term_credits.'.TU_CREDIT_ENRL', '>=', 12)
        ->orderBy($name.'.LAST_NAME', 'asc')
        ->orderBy($name.'.FIRST_NAME', 'asc')
        ->get();

        // build IsSrAthlete field from ACTI_ID
        // If ACTI_ID is not null then TRUE ELSE FALSE
        $new = $query->map(function($item) {
          if ($item['ACTI_ID'])
          {
            $item['IsSrAthlete'] = 1;
          }
          else
          {
            $item['IsSrAthlete'] = 0;
          }
          return $item;
        });

        // build EntryTypeALt field from ETYP_ID
        // AH, HS = first-time
        // CS, RS = continuing or returning
        // TR, T2, T4 = transfer
        // others??

        $new2 = $new->map(function($item) {
          if ($item['ETYP_ID'] == 'AH' || $item['ETYP_ID'] == 'HS' || $item['ETYP_ID'] == 'GE')
          {
            $item['EntryTypeALt'] = 'first-time';
          }
          elseif ($item['ETYP_ID'] == 'TR' || $item['ETYP_ID'] == 'T2' || $item['ETYP_ID'] == 'T4' || $item['ETYP_ID'] == 'U2')
          {
            $item['EntryTypeALt'] = 'transfer';
          }
          elseif ($item['ETYP_ID'] == 'CS' || $item['ETYP_ID'] == 'RS')
          {
            $item['EntryTypeALt'] = 'continuing/returning';
          }
          else
          {
            $item['EntryTypeALt'] = 'OTHER';
          }
          return $item;
        });

        return $new2;
        // return $query;

    }
}
