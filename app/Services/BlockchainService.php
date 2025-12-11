<?php

namespace App\Services;

use Web3\Web3;
use Web3\Contract;
use Web3\Utils;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
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

        // Setup Provider with RequestManager
        $requestManager = new HttpRequestManager($rpcUrl, 30); // 30 second timeout
        $this->web3 = new Web3(new HttpProvider($requestManager));

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
            // Validate inputs
            if (empty($recipients) || empty($uris)) {
                throw new Exception("Recipients and URIs cannot be empty");
            }
            
            if (count($recipients) !== count($uris)) {
                throw new Exception("Recipients and URIs must have the same length");
            }

            // Log the attempt
            Log::info("Minting batch: " . count($recipients) . " NFTs to " . count(array_unique($recipients)) . " recipients");

            // Get nonce and gas price
            $nonce = $this->getNonce();
            $gasPrice = $this->getGasPrice();

            // Use a fixed high gas limit for zkSync (gas estimation often fails)
            $gasLimit = '0x5B8D80'; // 6,000,000 gas

            // Encode function data manually using keccak256
            $functionSignature = 'mintBatch(address[],string[])';
            $functionSelector = substr(\kornrunner\Keccak::hash($functionSignature, 256), 0, 8);
            
            // Encode parameters
            $encodedParams = $this->encodeParameters($recipients, $uris);
            $data = '0x' . $functionSelector . $encodedParams;

            Log::info("Transaction data prepared, length: " . strlen($data));

            // Create raw transaction
            $transaction = new Transaction(
                Utils::toHex($nonce, true),
                Utils::toHex($gasPrice, true),
                $gasLimit,
                $this->contractAddress,
                '0x0',
                $data
            );

            // Sign transaction
            $signedTx = '0x' . $transaction->getRaw($this->privateKey, $this->chainId);

            // Broadcast
            $txHash = $this->sendRawTransaction($signedTx);

            Log::info("Transaction broadcast successful: " . $txHash);

            return $txHash;

        } catch (Exception $e) {
            Log::error("Blockchain Error: " . $e->getMessage());
            throw $e;
        }
    }

    // Encode parameters for mintBatch
    protected function encodeParameters(array $addresses, array $strings)
    {
        // Offset for first dynamic array (addresses)
        $offset1 = str_pad(dechex(64), 64, '0', STR_PAD_LEFT); // 0x40 = 64 bytes
        
        // Calculate offset for second dynamic array (strings)
        // It comes after: offset1 + offset2 + addresses array
        $addressesLength = count($addresses);
        $offset2Start = 64 + 32 + ($addressesLength * 32); // 2 offsets + length + addresses
        $offset2 = str_pad(dechex($offset2Start), 64, '0', STR_PAD_LEFT);
        
        // Encode addresses array
        $addressesEncoded = str_pad(dechex($addressesLength), 64, '0', STR_PAD_LEFT);
        foreach ($addresses as $address) {
            $addr = str_replace('0x', '', $address);
            $addressesEncoded .= str_pad($addr, 64, '0', STR_PAD_LEFT);
        }
        
        // Encode strings array
        $stringsCount = count($strings);
        $stringsEncoded = str_pad(dechex($stringsCount), 64, '0', STR_PAD_LEFT);
        
        // Calculate offsets for each string
        $currentOffset = $stringsCount * 32; // Start after all offset pointers
        $stringOffsets = '';
        $stringData = '';
        
        foreach ($strings as $str) {
            $stringOffsets .= str_pad(dechex($currentOffset), 64, '0', STR_PAD_LEFT);
            
            $strHex = bin2hex($str);
            $strLength = strlen($str);
            $strLengthHex = str_pad(dechex($strLength), 64, '0', STR_PAD_LEFT);
            $strPadded = str_pad($strHex, ceil(strlen($strHex) / 64) * 64, '0', STR_PAD_RIGHT);
            
            $stringData .= $strLengthHex . $strPadded;
            $currentOffset += 32 + (ceil(strlen($strHex) / 64) * 32);
        }
        
        $stringsEncoded .= $stringOffsets . $stringData;
        
        return $offset1 . $offset2 . $addressesEncoded . $stringsEncoded;
    }

    // Helpers

    protected function estimateGas($data)
    {
        $result = null;
        $error = null;

        // Cast to object for web3-php
        $params = (object)[
            'from' => $this->adminAddress,
            'to' => $this->contractAddress,
            'data' => $data,
        ];

        $this->web3->eth->estimateGas($params, function ($err, $res) use (&$result, &$error) {
            if ($err) $error = $err;
            else $result = $res;
        });

        if ($error) {
            Log::warning("Gas estimation failed, using default: " . $error->getMessage());
            return '0x4C4B40'; // 5000000 in hex as fallback
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