<?php

namespace App\Services;

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;
use Web3\Providers\HttpProvider;
use kornrunner\Ethereum\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class BlockchainService
{
    protected $web3;
    protected $contract;
    protected $privateKey;
    protected $adminAddress;
    protected $contractAddress;
    protected $chainId;

    public function __construct()
    {
        $rpcUrl = config('services.zksync.rpc_target');
        $this->chainId = hexdec(config('services.zksync.chain_id'));
        
        $this->privateKey = env('ZKSYNC_PRIVATE_KEY');
        $this->adminAddress = env('ZKSYNC_ADMIN_ADDRESS');
        $this->contractAddress = env('ZKSYNC_CONTRACT_ADDRESS');

        // Setup Provider
        $this->web3 = new Web3(new HttpProvider($rpcUrl, 30));

        // Load ABI
        $this->loadContract();
    }

    // Load Smart Contract ABI
    protected function loadContract()
    {
        $abiPath = storage_path('app/abi/JagaBumi.json');
        if (!file_exists($abiPath)) {
            throw new Exception("ABI file not found at: $abiPath");
        }
        $abiJson = file_get_contents($abiPath);
        $this->contract = new Contract($this->web3->provider, json_decode($abiJson, true));
    }

    // Mint Batch NFTs
    public function mintBatch(array $recipients, array $uris)
    {
        try {
            // 1. Get Blockchain Data (Synchronous style wrapper)
            $nonce = $this->getNonce();
            $gasPrice = $this->getGasPrice();

            // 2. Generate Transaction Data
            $data = $this->contract->getData('mintBatch', $recipients, $uris);

            // 3. Estimate Gas Limit
            $gasLimit = $this->estimateGas($data);
            
            // Add 20% buffer to gas limit for safety
            $gasLimit = gmp_strval(gmp_mul(gmp_init($gasLimit), gmp_init(120)));
            $gasLimit = gmp_strval(gmp_div(gmp_init($gasLimit), gmp_init(100)));

            // 4. Create raw transaction
            $transaction = new Transaction(
                Utils::toHex($nonce, true),
                Utils::toHex($gasPrice, true),
                Utils::toHex($gasLimit, true),
                $this->contractAddress,
                '0x0', // Value 0
                $data
            );

            // 4. Offline Signing
            $signedTx = '0x' . $transaction->getRaw($this->privateKey, $this->chainId);

            // 5. Broadcast to Network
            $txHash = $this->sendRawTransaction($signedTx);

            return $txHash;

        } catch (Exception $e) {
            Log::error("Blockchain Error: " . $e->getMessage());
            throw $e; // Rethrow error so Controller knows
        }
    }

    // Helpers

    protected function estimateGas($data)
    {
        $result = null;
        $error = null;

        $params = [
            'from' => $this->adminAddress,
            'to' => $this->contractAddress,
            'data' => $data,
        ];

        $this->web3->eth->estimateGas($params, function ($err, $res) use (&$result, &$error) {
            if ($err) $error = $err;
            else $result = $res;
        });

        if ($error) {
            Log::warning("Gagal estimate gas, menggunakan default: " . $error->getMessage());
            return '0x4C4B40'; // 5000000 in hex
        }

        return $result;
    }

    protected function getNonce()
    {
        $result = null;
        $error = null;

        $this->web3->eth->getTransactionCount($this->adminAddress, 'pending', function ($err, $res) use (&$result, &$error) {
            if ($err) $error = $err;
            else $result = $res;
        });

        if ($error) throw new Exception("Gagal mengambil Nonce: " . $error->getMessage());
        return $result;
    }

    protected function getGasPrice()
    {
        $result = null;
        $error = null;

        $this->web3->eth->gasPrice(function ($err, $res) use (&$result, &$error) {
            if ($err) $error = $err;
            else $result = $res;
        });

        if ($error) throw new Exception("Gagal mengambil Gas Price: " . $error->getMessage());
        return $result;
    }

    protected function sendRawTransaction($signedTx)
    {
        $txHash = null;
        $error = null;

        $this->web3->eth->sendRawTransaction($signedTx, function ($err, $hash) use (&$txHash, &$error) {
            if ($err) $error = $err;
            else $txHash = $hash;
        });

        if ($error) throw new Exception("Gagal Broadcast Transaksi: " . $error->getMessage());
        return $txHash;
    }

}