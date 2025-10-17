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
        
        // Vérifier que ce n'est pas soi-même
        if ($receiver->id === auth()->id()) {
            return response()->json(['message' => 'Vous ne pouvez pas vous transférer de l\'argent à vous-même'], 422);
        }

        $senderWallet = auth()->user()->wallet;
        
        // Créer le wallet de l'expéditeur s'il n'existe pas
        if (!$senderWallet) {
            return response()->json(['message' => 'Solde insuffisant'], 422);
        }
        
        if ($senderWallet->balance < $request->amount) {
            return response()->json(['message' => 'Solde insuffisant'], 422);
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
                // Déterminer le type de transaction
                $type = $transaction->receiver_id === $userId ? 'credit' : 'debit';
                
                // Description basée sur le type
                if ($transaction->sender_id === null) {
                    $description = 'Recharge du wallet';
                } elseif ($type === 'credit') {
                    $sender = \App\Models\User::find($transaction->sender_id);
                    $description = 'Transfert reçu de ' . ($sender ? $sender->name : 'Utilisateur inconnu');
                } else {
                    $receiver = \App\Models\User::find($transaction->receiver_id);
                    $description = 'Transfert envoyé à ' . ($receiver ? $receiver->name : 'Utilisateur inconnu');
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