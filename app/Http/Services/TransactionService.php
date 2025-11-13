<?php

namespace App\Http\Services;

use App\Http\Interfaces\ITransactionRepository;

class TransactionService{

    protected $transactionRepository;

    public function __construct(ITransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
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
        $this->transactionRepository->performTransfer($compteEmetteurId, $compteDestinataireId, $montant);
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
        $this->transactionRepository->performPayment($compteEmetteurId, $compteMarchandId, $montant);
    }

    public function getTransactionsByComptePaginated(string $numeroCompte, ?string $typeTransaction = null, int $perPage = 15)
    {
        return $this->transactionRepository->getTransactionsByComptePaginated($numeroCompte, $typeTransaction, $perPage);
    }
}