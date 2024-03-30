<?php

namespace App\Traits;

use App\Classes\Journey;


trait Display
{
  public function show($view)
  {
    $current = Journey::current();
    if ($current->agent->isMobile()) {
      return "web.mobile.$view";
    }
    return "web.$view";
  }


/**
 * return string covert from time to minute, hour, day , month, year.
 * @param timestamp $time
 */

  public function convertTimestamp($time){
      $minute = ceil($time/60);
      if($minute > 60){
          $hour = ceil($minute/60);
          if($hour > 24){
              $day = ceil($hour/24);
              if($day > 7){
                $week = ceil($day/7);
                if($week  >  4){
                  $month = ceil($week/30);
                  if($month > 12) return ceil($month/12).' năm trước';
                  return $month.' tháng trước';
                }
                return $week . ' tuần trước';
                  
              }
              return $day.' ngày trước';
          }
          return $hour.' giờ trước';
      }
      return $minute.' phút trước';
  }
}
