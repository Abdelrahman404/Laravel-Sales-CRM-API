<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function clientReport(Request $request){

        $data = [];

        $client = Client::with('status','country', 'city', 'area')
                        ->withCount('calls')
                        ->findOrFail($request->client_id);

        $data['client'] = $client;

        return sendResponse($data);
    }

    public function sellerReport(Request $request){

        $user = User::findOrFail($request->seller_id)->makeVisible(['name_en', 'name_ar']);

        $durationCases = [ 
                '1' => 'todayReport',
                '2' => 'lastWeekReport',
                '3' => 'lastMonth',
                '4' => 'lastThreeMonths',
                '5' => 'lastSixMonths',
                '6' => 'lastNineMonths',
                '7' => 'lastYear',
        ] ;
        // Dynamically mapping choosen duration to an isolated method insead of making 7 if-else nesting or switch case.
        $choosenFunction = $durationCases[$request->duration];

        $output = $this->$choosenFunction($user);

        return $output;

    }

    public function todayReport($user){
        
        foreach($user->deals as $deal){

            dd($deal);
    }

        $output = [];

        $output['calls'] = Call::where('created_at', '>=', Carbon::today())
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->count();
        
        $output['registered_clients'] = Client::where('created_at', '>=', Carbon::today())
                                        ->where('created_by', $user->name_en)
                                        ->orWhere('created_by', $user->name_ar)
                                        ->count();
            
        $records = collect([]);

            // Seller deals done (ربح الثفقة)
  

        foreach($user->userInfo as $userInfo){

            // Seller information
            $records->push(collect([
                    'country' => $userInfo->country->name,
                    'target' => $userInfo->target,
                    'comission' => $userInfo->comission,
            ]));

            // Seller deals done (ربح الثفقة)
            foreach($user->deals as $deal){

                    dd($deal);
            }
            
            
        }

        $output['records'] = $records;

        return $output;
    }

    public function lastWeekReport(){

        return 'last week';
    }

    public function lastMonth(){

        return 'last month';
    }

    public function lastThreeMonths(){

        return 'last three months';
    }

    public function lastSixMonths(){

        return 'last six month';
    }
    
    public function lastNineMonth(){

        return 'last nine month';
    }

    public function lastYear(){

        return 'last year';
    }



}
