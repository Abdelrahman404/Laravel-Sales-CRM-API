<?php

namespace App\Helpers;

use App\Models\Call;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Status;


class ReportHelper{

    public function fromToDurationReport($user, $startDate, $endDate){

        $output = [];
        
        $user->report_duration = $startDate . ' - ' . trans('messages.to') . ' - ' . $endDate;
    
        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);
    
        $output['user'] = $user;
    
        $output['calls'] = Call::where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->count();
    
        $output['registered_clients'] = Client::where('created_by', $user->name_en)
                                        ->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [$startDate, $endDate])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at', [$startDate, $endDate])->get();
    
        foreach($user->userInfo as $userInfo){
    
            // Seller information
            $collection = [];
    
            $collection['country'] = $userInfo->country->name;
            $collection['target'] = $userInfo->target;
            $collection['comission'] = $userInfo->comission;
            $collection['achived'] = 0;
    
            // Seller deals done (ربح الثفقة)
            foreach($deals as $deal){
    
                $achived = 0;
                // checking if deal done at the same country
                if($deal->country_id == $userInfo->country_id){
    
                    $achived = $achived + $deal->amount;
    
                    $collection['achived'] = $collection['achived'] + $achived;
    
                }
            }
        
        $collection['left'] = $userInfo->target - $collection['achived'];
        $collection['comission_value'] = $userInfo->comission / 100 * $collection['achived'];
    
       
                $records = collect([]);
                $records->push(collect($collection));
            
        }
        $output['records'] = $records;
    
        $cases = Status::all();     
    
        $casesCollection = collect();
    
        foreach($cases as $case){
            
            $count = Client::whereBetween('created_at', [$startDate, $endDate])
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('status', $case->id)
                            ->count();
    
            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
        
        $output['cases'] = $casesCollection;
        
        return $output;
    
    }
}