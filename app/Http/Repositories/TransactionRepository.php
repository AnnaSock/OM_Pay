<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\ITransactionRepository;
use App\Models\Transaction;
use App\Enums\TypeTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionRepository implements ITransactionRepository
{
    /**
     * Crée une transaction de dépôt
     *
     * @param array $transactionData
     * @return Transaction
     */
    public function createDepositTransaction(array $transactionData): Transaction
    {
        $transactionData['type_transaction'] = TypeTransaction::DEPOT;
        return Transaction::create($transactionData);
    }

    /**
     * Crée une transaction de retrait
     *
     * @param array $transactionData
     * @return Transaction
     */
    public function createRetraitTransaction(array $transactionData): Transaction
    {
        $transactionData['type_transaction'] = TypeTransaction::RETRAIT;
        return Transaction::create($transactionData);
    }

    /**
     * Crée une transaction de paiement
     *
     * @param array $transactionData
     * @return Transaction
     */
    public function createPaymentTransaction(array $transactionData): Transaction
    {
        $transactionData['type_transaction'] = TypeTransaction::PAYEMENT;
        return Transaction::create($transactionData);
    }

    /**
     * Récupère les transactions d'un compte avec pagination et filtres
     *
     * @param string $numeroCompte
     * @param string|null $typeTransaction
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTransactionsByComptePaginated(string $numeroCompte, ?string $typeTransaction = null, int $perPage = 15)
    {
        $query = Transaction::whereHas('compte', function ($query) use ($numeroCompte) {
            $query->where('numero_compte', $numeroCompte);
        })->orderBy('created_at', 'desc');

        if ($typeTransaction) {
            $query->where('type_transaction', $typeTransaction);
        }

        return $query->paginate($perPage);
    }

    /**
     * Effectue un transfert atomique entre deux comptes
     *
     * @param string $compteEmetteurId
     * @param string $compteDestinataireId
     * @param float $montant
     * @return void
     */
    public function performTransfer(string $compteEmetteurId, string $compteDestinataireId, float $montant): void
    {
        DB::transaction(function () use ($compteEmetteurId, $compteDestinataireId, $montant) {
            // Créer la transaction de retrait pour l'émetteur
            $this->createRetraitTransaction([
                'montant' => $montant,
                'compte_id' => $compteEmetteurId,
            ]);

            // Créer la transaction de dépôt pour le destinataire
            $this->createDepositTransaction([
                'montant' => $montant,
                'compte_id' => $compteDestinataireId,
            ]);
        });
    }

    /**
     * Effectue un paiement atomique vers un marchand
     *
     * @param string $compteEmetteurId
     * @param string $compteMarchandId
     * @param float $montant
     * @return void
     */
    public function performPayment(string $compteEmetteurId, string $compteMarchandId, float $montant): void
    {
        DB::transaction(function () use ($compteEmetteurId, $compteMarchandId, $montant) {
            // Créer la transaction de paiement pour l'émetteur
            $this->createPaymentTransaction([
                'montant' => $montant,
                'compte_id' => $compteEmetteurId,
            ]);

            // Créer la transaction de dépôt pour le marchand
            $this->createDepositTransaction([
                'montant' => $montant,
                'compte_id' => $compteMarchandId,
            ]);
        });
    }
}