<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends BaseController
{
    public function clientReport(Request $request){

        $data = [];

        $client = Client::with('case','country', 'city', 'area')
                        ->withCount('calls')
                        ->findOrFail($request->client_id);

        $data['client'] = $client;

        return $this->sendResponse($data);
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

        return $this->sendResponse($output);

    }

    public function todayReport($user){

        $output = [];
        
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

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at', [Carbon::now()->subMonth(3)->startOfMonth(), Carbon::now()->subMonth(3)->endOfMonth()])
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [Carbon::now()->subMonth(3)->startOfMonth(), Carbon::now()->subMonth(3)->endOfMonth()])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subMonth(3)->startOfMonth(), Carbon::now()->subMonth(3)->endOfMonth()])->get();

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subMonth(3)->startOfMonth(), Carbon::now()->subMonth(3)->endOfMonth()])
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

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at', [Carbon::now()->subMonth(6)->startOfMonth(), Carbon::now()->subMonth(6)->endOfMonth()])
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [Carbon::now()->subMonth(6)->startOfMonth(), Carbon::now()->subMonth(6)->endOfMonth()])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subMonth(6)->startOfMonth(), Carbon::now()->subMonth(6)->endOfMonth()])->get();

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subMonth(6)->startOfMonth(), Carbon::now()->subMonth(6)->endOfMonth()])
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

        $output['calls'] = Call::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                         ->whereBetween('created_at', [Carbon::now()->subMonth(9)->startOfMonth(), Carbon::now()->subMonth(9)->endOfMonth()])
                         ->count();

        $output['registered_clients'] = Client::where('created_by', $user->name_en)->orWhere('created_by', $user->name_ar)
                                        ->whereBetween('created_at', [Carbon::now()->subMonth(9)->startOfMonth(), Carbon::now()->subMonth(9)->endOfMonth()])
                                        ->count();
        
        // Getting user deals at this period
        $deals = Deal::whereBetween('created_at',[Carbon::now()->subMonth(9)->startOfMonth(), Carbon::now()->subMonth(9)->endOfMonth()])->get();

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
            
            $count = Client::whereBetween('created_at',[Carbon::now()->subMonth(9)->startOfMonth(), Carbon::now()->subMonth(9)->endOfMonth()])
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
