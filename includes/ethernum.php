<?php
include_once __DIR__.'/session.php';
include_once __DIR__.'/base.php';
require_once  __DIR__ . '/config.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
 
class Ethernum extends Base
{
    private const WALLET_ADDRESS = 'walletAddress';
    private const MAX_SUPPLY = 'maxSupply';
    private const TOTAL_SUPPLY = 'totalSupply';
    private const FROZEN_BALANCES = 'frozenBalances';
    private const HEARTS_TRANSFORMED = 'heartsTransformed';
    private const HXY_TRANSFORMED = 'hxyTransformed';
    private const FREEZE_TOKENBALANCE = 'freezeTokenBalance';
    private const LOCKED_TOKEN = 'lockedToken';
    private const ACCOUNT_BALANCE = 'accountBalance';
    private const ALLOWANCE = 'allowance';
    private const FREEZING_REWARD = 'freezingReward';

    public function __construct(Config $config)
    {
      
      $this->verifyToken();  
     
      $provider = $config->getProvider();
      
      if (empty($provider)) {
        throw new InvalidArgumentException("ethernum node uri cannot be empty");
      }
     if(isset($_POST['account']) && !empty($_POST['account'])) {
        $this->setUserAccount($_POST['account']);
      }
      else if(isset($_SESSION[self::WALLET_ADDRESS]) && !empty($_SESSION[self::WALLET_ADDRESS])) {
        $this->setUserAccount($_SESSION[self::WALLET_ADDRESS]);
      }
      
      $web3 = new Web3(new HttpProvider(new HttpRequestManager($provider, 3)));
      $contract = new Contract($web3->provider, $config->getMoneyABI());
      $this->moneyAddress = $config->getMoneyAddress();
      $this->tokenAddress = $config->getEtherConfigAddress();
      $this->moneyInstance = $contract->at($this->moneyAddress);
      $this->tokenInstance = new Contract($web3->provider, $config->getTokenABI());

    }

    public function init(): void
    {

      if(isset($_POST['account'])&&!empty($_POST['account'])) {
        $this->setUserAccount($_POST['account']);
        
        if($this->isLogged())
        {
          $this->getTokenFrozenBalances();
          $this->getFreezingReward();
          $this->getAllowance();
          $this->getAccountBalance();
        }
       
        $this->getLockedToken();
        $this->getFrzoneTokenBalance();
        $this->getHxyTransformed();
        $this->getTotalSupply();
        $this->getHeartsTransformed();
        $this->getMaxSupply();
      }

      $this->sendResult();      
    }

    private function sendResult(): void
    { 

      $result = array();
      $result[self::FROZEN_BALANCES] = 0;
      $result[self::FREEZING_REWARD] = 0;
      $result[self::ALLOWANCE] = 0;
      $result[self::LOCKED_TOKEN] = 0;
      $result[self::FREEZE_TOKENBALANCE] = 0;
      $result[self::HXY_TRANSFORMED] = 0;
      $result[self::HEARTS_TRANSFORMED] = 0;
      $result[self::TOTAL_SUPPLY] = 0;
      $result[self::MAX_SUPPLY] = 0;
      $result[self::ACCOUNT_BALANCE] = 0;

      if( isset($_POST['account']) && !empty($_POST['account']) ) {

        $result[self::FROZEN_BALANCES] = $_SESSION[self::FROZEN_BALANCES];
        $result[self::FREEZING_REWARD] = $_SESSION[self::FREEZING_REWARD];
        $result[self::ALLOWANCE] = $_SESSION[self::ALLOWANCE];
        $result[self::LOCKED_TOKEN] = $_SESSION[self::LOCKED_TOKEN];
        $result[self::FREEZE_TOKENBALANCE] = $_SESSION[self::FREEZE_TOKENBALANCE];
        $result[self::HXY_TRANSFORMED] = $_SESSION[self::HXY_TRANSFORMED];
        $result[self::HEARTS_TRANSFORMED] = $_SESSION[self::HEARTS_TRANSFORMED];
        $result[self::TOTAL_SUPPLY] = $_SESSION[self::TOTAL_SUPPLY];
        $result[self::MAX_SUPPLY] = $_SESSION[self::MAX_SUPPLY];
        $result[self::ACCOUNT_BALANCE] = $_SESSION[self::ACCOUNT_BALANCE];
        
      }

      echo json_encode($result);
    }

    /**
     * This function checks if the user is already logged
     * @return bool
     */
    private function isLogged():bool
    { 
        try {
            $this->getUserAccount();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function setUserAccount(string $address): void
    {
        if (empty($address)) {
            throw new InvalidArgumentException("Wallet Address cannot be empty");
        }
        $_SESSION[self::WALLET_ADDRESS] = $address;
    }
     
    /**
     * This function returns the user wallet address
     * @return string
     */
    private function getUserAccount(): string
    {
        if(!isset($_SESSION[self::WALLET_ADDRESS])) {
            throw new InvalidArgumentException("No record wallet address");
        }

        $wallet = trim($_SESSION[self::WALLET_ADDRESS]);
        if (empty($wallet)) {
            throw new InvalidArgumentException('No record wallet address');
        }

        return $wallet;
    }

    private function getTokenFrozenBalances(): void
    {
      $this->moneyInstance->call('tokenFrozenBalances',$this->getUserAccount(), function ($err, $result) {

        if(!empty($err))  {
          throw new InvalidArgumentException($err);
        }
        else {

          if (count($result) > 0 && property_exists($result[0], 'value')){
            if (gettype($result[0]->value) == 'string') {
              $_SESSION[self::FROZEN_BALANCES] = $result[0]->value;
            }
            else {
              $_SESSION[self::FROZEN_BALANCES] = gmp_intval($result[0]->value); 
            }
          }
        }
        
      });
    }
    private function getFreezingReward(): void
    {
      $this->moneyInstance->call('calcFreezingRewards', ['from'=>$this->getUserAccount()], function ($err, $result) {
        if(!empty($err)) {
          throw new InvalidArgumentException($err);
        }
        else{
          
          if (count($result) > 0 && property_exists($result[0], 'value')){
            if (gettype($result[0]->value) == 'string') {
              $_SESSION[self::FREEZING_REWARD] = $result[0]->value;
            }
            else {
              $_SESSION[self::FREEZING_REWARD] = gmp_intval($result[0]->value); 
            }
          }

        }
      });
    }

    private function getAllowance(): void
    {
      $this->tokenInstance->at($this->tokenAddress)->call('allowance',$this->getUserAccount(), $this->moneyAddress, function ($err, $result) {
        if(!empty($err)) {
          throw new InvalidArgumentException($err);
        }
        else {

          if (count($result) > 0 && property_exists($result[0], 'value')){
            if (gettype($result[0]->value) == 'string') {
              $_SESSION[self::ALLOWANCE] = $result[0]->value;
            }
            else {
              $_SESSION[self::ALLOWANCE] = gmp_intval($result[0]->value); 
            }
          }

        }
      });
    }
    private function getLockedToken(): void
    {
      $this->moneyInstance->call('lockedTokens', null, function ($err, $result) {
        if(!empty($err)) {
          throw new InvalidArgumentException($err);
        }
        else {

          if (count($result) > 0 && property_exists($result[0], 'value')){
            if (gettype($result[0]->value) == 'string') {
              $_SESSION[self::LOCKED_TOKEN] = $result[0]->value;
            }
            else {
              $_SESSION[self::LOCKED_TOKEN] = gmp_intval($result[0]->value); 
            }
          }
        }
      });
    }

    private function getFrzoneTokenBalance(): void
    {
      $this->moneyInstance->call('totalFrozenTokenBalance', null, function ($err, $result) {
        if(!empty($err)) {
          throw new InvalidArgumentException($err);
        }
        else {

          if (count($result) > 0 && property_exists($result[0], 'value')){
            if (gettype($result[0]->value) == 'string') {
              $_SESSION[self::FREEZE_TOKENBALANCE] = $result[0]->value;
            }
            else {
              $_SESSION[self::FREEZE_TOKENBALANCE] = gmp_intval($result[0]->value); 
            }
          }    
        }
      });
    }

    private function getHxyTransformed(): void
    {
      $this->moneyInstance->call('totalHXYTransformed', null, function ($err, $result) {
        if(!empty($err)) {
          throw new InvalidArgumentException($err);
        }
        else {
          if (count($result) > 0 && property_exists($result[0], 'value')){
            if (gettype($result[0]->value) == 'string') {
              $_SESSION[self::HXY_TRANSFORMED] = $result[0]->value;
            }
            else {
              $_SESSION[self::HXY_TRANSFORMED] = gmp_intval($result[0]->value); 
            }
          }     
        }
      });
    }

    private function getHeartsTransformed(): void
    {
      $this->moneyInstance->call('totalHeartsTransformed', null, function ($err, $result) {

        if(!empty($err)) {
          throw new InvalidArgumentException($err);
        }
        else
        {
          if (count($result) > 0 && property_exists($result[0], 'value')){
            if (gettype($result[0]->value) == 'string') {
              $_SESSION[self::HEARTS_TRANSFORMED] = $result[0]->value;
            }
            else {
              $_SESSION[self::HEARTS_TRANSFORMED] = gmp_intval($result[0]->value); 
            }
          }          
        }
      });
    }
   
    private function getTotalSupply(): void
    {
        $this->moneyInstance->call('totalSupply', null, function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
            else {
              if (count($result) > 0 && property_exists($result[0], 'value')){
                if (gettype($result[0]->value) == 'string') {
                  $_SESSION[self::TOTAL_SUPPLY] = $result[0]->value;
                }
                else {
                  $_SESSION[self::TOTAL_SUPPLY] = gmp_intval($result[0]->value); 
                }
              }     
            }
            
        });
    }

    private function getMaxSupply(): void
    {
        $this->moneyInstance->call('_maxSupply', null, function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
            else {
              if (count($result) > 0 && property_exists($result[0], 'value')){
                if (gettype($result[0]->value) == 'string') {
                  $_SESSION[self::MAX_SUPPLY] = $result[0]->value;
                }
                else {
                  $_SESSION[self::MAX_SUPPLY] = gmp_intval($result[0]->value); 
                }
              }                
            }
        });
    }

    private function getAccountBalance(): void
    {
        $this->moneyInstance->call('balanceOf',$this->getUserAccount(), function($err,$result){
          if(!empty($err)) {
              throw new InvalidArgumentException($err);
          }
          else {
            if (count($result) > 0 && property_exists($result[0], 'value')){
             if (gettype($result[0]->value) == 'string') {
                $_SESSION[self::ACCOUNT_BALANCE] = $result[0]->value;
              }
              else {
                $_SESSION[self::ACCOUNT_BALANCE] = gmp_intval($result[0]->value); 
              }            
            }                              
          }
        });
    }
}

$ethernum = new Ethernum(new Config);
$ethernum->init();
 