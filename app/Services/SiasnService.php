<?php

namespace App\Services;

use App\Models\SiasnConfig;
use SiASN\Sdk\SiasnClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SiasnService
{
    protected static $client;
    protected static $config;
    
    public static function client()
    {
        if (!self::$client) {
            $config = self::config();
            
            self::$client = new SiasnClient([
                'consumerKey'    => $config['consumerKey'],
                'consumerSecret' => $config['consumerSecret'],
                'clientId'       => $config['clientId'],
                'ssoAccessToken' => $config['ssoAccessToken'],
                'wsoAccessToken' => $config['wsoAccessToken'],
            ]);
        }
        
        return self::$client;
    }
    
    public static function config()
    {
        if (!self::$config) {
            $dbConfig = Cache::remember('siasn_config', 3600, fn() => SiasnConfig::first());
            
            if (!$dbConfig) {
                throw new \Exception('Konfigurasi SIASN tidak ditemukan.');
            }
            
            self::$config = $dbConfig;
        }
        
        return [
            'consumerKey'    => self::$config->cs_key,
            'consumerSecret' => self::$config->cs_sec,
            'clientId'       => self::$config->cs_id,
            'ssoAccessToken' => self::$config->csso,
            'wsoAccessToken' => self::freshToken(),
        ];
    }
    
    protected static function freshToken()
    {
        if (Cache::has('siasn_token') && self::tokenValid()) {
            return Cache::get('siasn_token');
        }
        
        return self::refreshToken();
    }
    
    protected static function tokenValid()
    {
        $config = self::$config;
        return $config->cwso && $config->cwso_exp && 
               now()->lessThan(Carbon::parse($config->cwso_exp));
    }
    
    protected static function refreshToken()
    {
        try {
            $config = self::$config;
            
            $tempClient = new SiasnClient([
                'consumerKey'    => $config->cs_key,
                'consumerSecret' => $config->cs_sec,
                'clientId'       => $config->cs_id,
                'ssoAccessToken' => $config->csso,
            ]);
            
            $token = $tempClient->authentication()->getWsoAccessToken();
            
            $config->update([
                'cwso' => $token,
                'cwso_exp' => now()->addHours(1)
            ]);
            
            Cache::put('siasn_token', $token, 3500);
            Cache::forget('siasn_config');
            self::$config = null; // Reset cache
            
            return $token;
            
        } catch (\Throwable $e) {
            Log::error('Token refresh gagal', ['error' => $e->getMessage()]);
            return self::$config->cwso ?? null;
        }
    }
    
    public static function getIPASN(string $nip)
    {
        return Cache::remember("ipasn_{$nip}", 3600, function() use ($nip) {
            return self::client()->pns()->nilaiIpAsn($nip);
        });
    }

    public static function getASN(string $nip)
    {
        return Cache::remember("asn_{$nip}", 3600, function() use ($nip) {
            return self::client()->pns()->dataUtama($nip);
        });
    }

    public static function getASNData(string $nip)
    {       
        return self::client()->pns()->dataUtama($nip);
    }
    
    public static function clearCache()
    {
        Cache::forget('siasn_config');
        Cache::forget('siasn_token');
        self::$config = null;
        self::$client = null;
    }
}