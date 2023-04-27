<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class ReportController extends BaseController
{
    public function clientReport(Request $request){

        $data = [];

        $client = Client::with('case','country', 'city', 'area','products')
                        ->withCount('calls')
                        ->findOrFail($request->client_id);

        $data['client'] = $client;

        return $this->sendResponse($data);
    }

    public function sellerReport(Request $request){

        $validator =  Validator::make($request->all(), [
            'seller_id' => 'required|exists:users,id',
            'from' => 'required|string',
            'to' => 'required|string'
        ]);

        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }

        $from = Carbon::parse($request->from);
        
        $to = Carbon::parse($request->to);

        $user = User::findOrFail($request->seller_id)->makeVisible(['name_en', 'name_ar']);

        $output = $this->fromToDurationReport($user, $from, $to);

        return $this->sendResponse($output);
    }

    public function fromToDurationReport($user, $startDate, $endDate){

        $output = [];
        
        $user->report_duration = $startDate . '-' . $endDate;

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

    public function sellerRegisteredClient(Request $request){

        $seller = User::findOrFail($request->seller_id);

        $clients = Client::where('created_by','like',"%{$seller->name_ar}%")->orWhere('created_by','like',"%{$seller->name_en}%")
                        ->whereActive(true)
                        ->with('country', 'city', 'area')
                        ->latest()
                        ->get();

        return $this->sendResponse($clients);
        
    }

    public function sellerRegisteredCalls($id){


    }

    public function oldSellerReport(Request $request){

        $validator =  Validator::make($request->all(), [
            'seller_id' => 'required|exists:users,id',
            'duration' => 'required|integer|between:1,7'
        ]);

        if ($validator->fails()){

            return $this->sendError($validator->errors());
        }

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

        return $this->sendResponse($output);

    }
    
    public function todayReport($user){

        $output = [];
        
        $user->report_duration = Carbon::today()->format('Y-m-d');

        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);

        $output['user'] = $user;

        $output['calls'] = Call::where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('created_at', '>=', Carbon::today())
                            ->count();
        
        $output['registered_clients'] = Client::where('created_by', $user->name_en)
                                        ->orWhere('created_by', $user->name_ar)
                                        ->where('created_at', '>=', Carbon::today())
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::where('created_at', '>=', Carbon::today())->get();

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
            
            $count = Client::where('created_at', '>=', Carbon::today())
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('status', $case->id)
                            ->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
        
        $output['cases'] = $casesCollection;
        
        return $output;
    }

    public function lastWeekReport($user){

        $output = [];

        $user->report_duration = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d') . ' - ' .  Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');

        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);

        $output['user'] = $user;

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at',[Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])        
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at',[Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])        
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])->get();        

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])        
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('status', $case->id)
                            ->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
        
        $output['cases'] = $casesCollection;

        return $output;
    }

    public function lastMonth($user){

        $output = [];

        $user->report_duration = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d') . ' - ' .   Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');

        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);

        $output['user'] = $user;

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->get();

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('status', $case->id)
                            ->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
        
        $output['cases'] = $casesCollection;

        return $output;
    }

    public function lastThreeMonths($user){

        $output = [];

        $user->report_duration = Carbon::now()->subMonth(3)->format('Y-m-d') . ' - ' .  Carbon::now()->format('Y-m-d');

        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);

        $output['user'] = $user;

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at', [Carbon::now()->subMonth(3), Carbon::now()])
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [Carbon::now()->subMonth(3), Carbon::now()])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subMonth(3), Carbon::now()])->get();

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subMonth(3), Carbon::now()])
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('status', $case->id)
                            ->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
        
        $output['cases'] = $casesCollection;

        return $output;
    }

    public function lastSixMonths($user){

        $output = [];
        
        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);

        $user->report_duration = Carbon::now()->subMonth(6)->format('Y-m-d') . ' - ' .  Carbon::now()->format('Y-m-d');

        $output['user'] = $user;

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()])
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subMonth(6), Carbon::now()])->get();

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subMonth(6), Carbon::now()])
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('status', $case->id)
                            ->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
        
        $output['cases'] = $casesCollection;

        return $output;
    }
    
    public function lastNineMonths($user){

        $output = [];

        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);

        $user->report_duration = Carbon::now()->subMonth(9)->format('Y-m-d') . ' - ' .  Carbon::now()->format('Y-m-d');

        $output['user'] = $user;

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at', [Carbon::now()->subMonth(9), Carbon::now()])
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [Carbon::now()->subMonth(9), Carbon::now()])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subMonth(9), Carbon::now()])->get();

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subMonth(9), Carbon::now()])
                            ->where('created_by', $user->name_en)
                            ->orWhere('created_by', $user->name_ar)
                            ->where('status', $case->id)
                            ->count();

            $casesCollection->push(collect(['id' => $case->id, 'name' => $case->name, 'count' => $count] ));
            
        }
        
        $output['cases'] = $casesCollection;

        return $output;
    }

    public function lastYear($user){
        
        $output = [];

        $user->makeHidden(['userInfo', 'name_en', 'name_ar', 'type',]);

        $user->report_duration = Carbon::now()->subMonth(12)->format('Y-m-d') . ' - ' .  Carbon::now()->format('Y-m-d');

        $output['user'] = $user;

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereYear('created_at', now()->subYear()->year)
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereYear('created_at', now()->subYear()->year)
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereYear('created_at', now()->subYear()->year)->get();

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
            
            $count = Client::whereYear('created_at', now()->subYear()->year)
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
