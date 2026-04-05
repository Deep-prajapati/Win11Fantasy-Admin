<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $title = "User List";
        // $users = User::where('role', '2')->paginate(env('PER_PAGE_RECORDS', 10));
        return view('users.index', compact('title'));
    }

    public function botsUser(Request $request)
    {
        $title = "Bot User List";
        $users = User::where('role', '3')->paginate(env('PER_PAGE_RECORDS', 10));
        return view('users.botuser.index', compact('title', 'users'));
    }
    
    public function block(Request $request, $user_id)
    {
        $user = User::where(['id' => $user_id, 'role' => 2, 'is_banned' => false])->first();
        if (!$user) {
            flash()->error('Invalid User details');
            return redirect()->route('admin.users.list');
        }
        $user->is_banned = true;
        $user->update();
        flash()->success('User blocked successfully.');
        return redirect()->route('admin.users.list');
    }

    public function unblock(Request $request, $user_id)
    {
        $user = User::where(['id' => $user_id, 'role' => 2, 'is_banned' => true])->first();
        if (!$user) {
            flash()->error('Invalid User details');
            return redirect()->route('admin.users.list');
        }
        $user->is_banned = false;
        $user->update();
        flash()->success('User unblocked successfully.');
        return redirect()->route('admin.users.list');
    }
    
    public function view($user_id)
    {
        $user = User::where(['id' => $user_id, 'role' => 2])->first();

        if (!$user) {
            flash()->error('Invalid User details');
            return redirect()->route('admin.users.list');
        }

        $user->load('account');
        // $matchesIds = JoinCrickContest::where(['user_id'=>$user_id])->groupby('match_id')->pluck('match_id');
        //    return $matches = Fixture::whereIn('fixture_id',$matchesIds)->orderby('starting_at','desc')->get();
        $title = "User";

        return view('users.view', compact('title', 'user'));
    }

    public function wallet(Request $request, $user_id)
    {
        $user = User::where(['id' => $user_id, 'role' => 2])->first();

        if (!$user) 
        {
            flash()->error('Invalid User details');
            return redirect()->route('admin.users.list');
        }

        $user->load('account');

        if ($request->isMethod('POST')) 
        {
            $request->validate([
                "balance" => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d+)?$/'],
                "winning" => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d+)?$/'],
                "bonus" => ['required', 'numeric', 'min:0', 'regex:/^\d+(\.\d+)?$/']
            ]);

            if ($user->account->winning != $request->winning) 
            {
                $diff = $request->winning - $user->account->winning;
                $user->account->winning = $request->winning;

                Transection::create([
                    'user_id' => $user->id,
                    'type' => ($diff > 0) ? 1 : 2,
                    'amount' =>  abs($diff),
                    'desc' => 'Winnings | App',
                ]);
            }

            if ($user->account->balance != $request->balance) 
            {
                $diff = $request->balance - $user->account->balance;
                $user->account->balance = $request->balance;

                Transection::create([
                    'user_id' => $user->id,
                    'type' => ($diff > 0) ? 1 : 2,
                    'amount' => abs($diff),
                    'desc' =>  'Balance | App',
                ]);
            }

            if ($user->account->bonus != $request->bonus) 
            {
                $diff = $request->bonus - $user->account->bonus;
                $user->account->bonus = $request->bonus;

                Transection::create([
                    'user_id' => $user->id,
                    'type' => ($diff > 0) ? 1 : 2,
                    'amount' => abs($diff),
                    'desc' => 'Bonus | App',
                ]);
            }

            $user->account->update();

            flash()->success('Wallet details updated');
            return redirect()->route('admin.users.wallet', $user_id);
        } else {
            $title = 'Wallet';
            return view('users.wallet', compact('title', 'user'));
        }
    }

    public function Update(Request $request, $user_id)
    {
        $user = User::where(['id' => $user_id, 'role' => 2])->first();

        if (!$user) 
        {
            flash()->error('Invalid User details');
            return redirect()->route('admin.users.list');
        }
        
        $request->validate([
            "email" => ['nullable', 'string', 'max:100'],
            "name" => ['required', 'string', 'max:100']
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->update();

        flash()->success('User details updated');
        return redirect()->route('admin.users.wallet', $user_id);
    }

    public function botsUserStatus($user_id)
    {
        $user = User::where(['id' => $user_id, 'role' => 3])->first();
        if (!$user) {
            flash()->error('Invalid bot User details');
            return redirect()->route('admin.users.bots.list');
        }
        $user->is_banned = ($user->is_banned) ?  false : true;
        $user->update();
        flash()->success('bot User status updated successfully.');
        return redirect()->route('admin.users.bots.list');
    }

    public function botsUseradd(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate([
                'name.*' => 'required|string|max:255',
                'email.*' => [
                    'nullable',
                    'email',
                    Rule::unique('users', 'email'),
                    'regex:/^[a-zA-Z0-9._%+-]+@bot\.com$/'
                ],
            ], [
                'email.*.regex' => 'The email must be in the format: example@bot.com',
            ]);
            foreach ($request->name as $index => $name) {
                $cleanName = str_replace(' ', '', strtolower($name));
                $email = $request->email[$index] ?? "$cleanName@bot.com";
                $originalEmail = $email;
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = "$cleanName$counter@bot.com";
                    $counter++;
                }
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'role' => 3,
                    'email_verified_at' => now(),
                    'is_kyc_complete' => true,
                    'mobile_number' => '0000000000',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            flash()->success('Bot Users added successfully.');
            return redirect()->route('admin.users.bots.list');
        } else {
            $title = "Add Bot User";
            return view('users.botuser.add', compact('title'));
        }
    }

    public function tnxlist()
    {
        $title = "Transaction List";
        return view('users.transaction', compact('title'));
    }
}