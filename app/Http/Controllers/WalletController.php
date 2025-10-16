<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Transaction;

class WalletController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        $wallet = $user->wallet ?? $user->wallet()->create(['balance' => 0]);

        return response()->json([
            'user_id' => $wallet->user_id,
            'balance' => $wallet->balance
        ]);
    }   
    

    public function transfer(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id|not_in:' . auth()->id(),
            'amount' => 'required|numeric|min:0.01'
        ]);

        $senderWallet = auth()->user()->wallet;
        if ($senderWallet->balance < $request->amount) {
            return response()->json(['error' => 'insufficient balance'], 400);
        }

        $receiverWallet = Wallet::firstOrCreate(
            ['user_id' => $request->receiver_id],
            ['balance' => 0]
        );

        $transaction = DB::transaction(function () use ($senderWallet, $receiverWallet, $request) {
            $senderWallet->decrement('balance', $request->amount);
            $receiverWallet->increment('balance', $request->amount);

            return Transaction::create([
                'sender_id' => $senderWallet->user_id,
                'receiver_id' => $receiverWallet->user_id,
                'amount' => $request->amount,
                'status' => 'success'
            ]);
        });

        return response()->json([
            'transaction_id' => $transaction->id,
            'sender_id' => $senderWallet->user_id,
            'receiver_id' => $receiverWallet->user_id,
            'amount' => $request->amount,
            'status' => 'success',
            'created_at' => $transaction->created_at->toIso8601ZuluString()
        ], 201);
    }   

    public function topup(Request $request)
    {
        if ($request->amount > 10000) {
            return response()->json(['error' => 'The amount cannot exceed 10,000.'], 400);
        }
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $wallet = auth()->user()->wallet;

        DB::transaction(function () use ($wallet, $request) {
            $wallet->increment('balance', $request->amount);
            Transaction::create([
                'sender_id' => null,
                'receiver_id' => $wallet->user_id,
                'amount' => $request->amount,
                'status' => 'success'
            ]);
        });

        return response()->json([
            'user_id' => $wallet->user_id,
            'balance' => $wallet->balance,
            'topup_amount' => $request->amount,
            'created_at' => now()->toIso8601ZuluString()
        ], 201);
    }

    public function transactions()
    {
        $transactions = Transaction::where('sender_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($transactions->makeHidden(['updated_at']), 200);
    }


}   