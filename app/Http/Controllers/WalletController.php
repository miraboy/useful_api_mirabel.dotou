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
            'recipient_id' => 'required_without:recipient_email|integer|exists:users,id',
            'recipient_email' => 'required_without:recipient_id|email|exists:users,email',
            'amount' => 'required|numeric|min:0.01'
        ]);

   
        if ($request->has('recipient_id')) {
            $receiver = \App\Models\User::find($request->recipient_id);
        } else {
            $receiver = \App\Models\User::where('email', $request->recipient_email)->first();
        }
        
        // Check that it's not the same person
        if ($receiver->id === auth()->id()) {
            return response()->json(['message' => 'You cannot transfer money to yourself'], 422);
        }

        $senderWallet = auth()->user()->wallet;
        
        // Create sender's wallet if it doesn't exist
        if (!$senderWallet) {
            return response()->json(['message' => 'Insufficient balance'], 422);
        }
        
        if ($senderWallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 422);
        }

        $receiverWallet = Wallet::firstOrCreate(
            ['user_id' => $receiver->id],
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
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:10000'
        ]);

        $wallet = auth()->user()->wallet;
        
        if (!$wallet) {
            $wallet = auth()->user()->wallet()->create(['balance' => 0]);
        }

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
        $userId = auth()->id();
        
        $transactions = Transaction::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) use ($userId) {
                // Determine transaction type
                $type = $transaction->receiver_id === $userId ? 'credit' : 'debit';
                
                if ($transaction->sender_id === null) {
                    $description = 'Wallet top-up';
                } elseif ($type === 'credit') {
                    $sender = \App\Models\User::find($transaction->sender_id);
                    $description = 'Transfer received from ' . ($sender ? $sender->name : 'Unknown user');
                } else {
                    $receiver = \App\Models\User::find($transaction->receiver_id);
                    $description = 'Transfer sent to ' . ($receiver ? $receiver->name : 'Unknown user');
                }
                
                return [
                    'id' => $transaction->id,
                    'type' => $type,
                    'amount' => $transaction->amount,
                    'description' => $description,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->toIso8601String(),
                ];
            });
            
        return response()->json($transactions, 200);
    }


}   