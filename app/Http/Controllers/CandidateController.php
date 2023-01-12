<?php

namespace App\Http\Controllers;

use App\Mail\HiredMail;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\HttpFoundation\Session\Storage\save;

class CandidateController extends Controller
{
    public function index(){
    $candidates = Candidate::all();
    $coins = Company::find(1)->coins;
    return view('candidates.index', compact('candidates', 'coins'));
}

    public function contact(Request $request){
        $candidate = Candidate::find($request->id);
        $company = Company::find(1);
        $coins = Wallet::where('company_id', 1)->first();
        if ($coins->coins > 4){
            $companyContact = new CompanyContact();
            $companyContact->candidate_id   = $candidate->id;
            $companyContact->company_id     = $company->id;
            $companyContact->save();

            $coins->coins                   = $coins->coins - 5;
            $coins->save();

            Mail::send('mail.contactedMail', ['candidate' => $candidate,'company' => $company,], function($message) use($candidate){
                $message->to($candidate->email);
                $message->subject('Get Contacted');
            });
            $candidates = Candidate::all();
            return \response($candidates);
        } else{
            $message = 'You have not enough coins';
            return \response($message);
        }
    }

    public function hire(Request $request){
        $candidate = Candidate::find($request->id);
        $company = Company::find(1);
        if ($candidate->companies->find($candidate->id)){
            if ($candidate->isHired ==  null){
                $candidate->isHired =   true;
                $candidate->update();

                $coins = Wallet::where('company_id', 1)->first();
                $coins->coins = $coins->coins + 5;
                $coins->save();
                Mail::send('mail.hiredMail', ['candidate' => $candidate,'company' => $company,], function($message) use($candidate){
                    $message->to($candidate->email);
                    $message->subject('Get Hired');
                });
            }
            $message = 'Candidate have already hired';
            return \response($message);
        }else{
            $message = 'You have not contact the candidate yet';
            return \response($message);
        }
        // @todo
        // Your code goes here...
    }
}
