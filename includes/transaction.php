<?php
 include_once __DIR__.'/base.php';
 require_once  __DIR__ . '/config.php';
  use Web3\Web3;
  use Web3\Contract;
  use Web3\Providers\HttpProvider;
  use Web3\RequestManagers\HttpRequestManager;
 
class Transaction// extends Base
{
    
    private const WALLET_ADDRESS = 'walletAddress';
    private $moneyInstance;
    private $tokenInstance;
    private $moneyAdderss;
    private $tokenAddress;
    private $account = '0x51C2609885753A1CB8B6901A933C15a0224CB57B';
   
    public function __construct(Config $config)
    {
      
     // $this->verifyToken();  
     
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

    public function init()
    {
      $this->getTokenFrozenBalances();
      $this->getFreezingReward();
      $this->getAllowance();
      $this->getLockedToken();
      $this->getFrzoneTokenBalance();
      $this->getHxyTransformed();
      $this->getTotalSupply();
      $this->getMaxSupply();
      $this->getAccountBalance();
      $this->getHeartsTransformed();
      $result = array();
    }
    private function approve($amount)
    {
        //$this->moneyInstance->call()->__maxSupply($this->getUserAccount());
        $this->tokenInstance->at($this->tokenAddress)->send('approve',$this->moneyAddress, $amount, function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
           return 'success';
        });
    }
    private function transform($amount)
    {
      $this->moneyInstance->send('transformHEX',$amount,'0x0000000000000000000000000000000000000000',['from',$this->account], function($err,$result){
        if(!empty($err)) {
            throw new InvalidArgumentException($err);
        }
       return 'success';
    });
    }
    private function freeze($amount)
    {
        //$this->moneyInstance->call()->__maxSupply($this->getUserAccount());
        $this->moneyInstance->send('FreezeTokens', $amount,['from',$this->account], function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
           return 'success';
        });
    }
    private function unfreeze()
    {
        //$this->moneyInstance->call()->__maxSupply($this->getUserAccount());
        $this->tokenInstance->at($this->tokenAddress)->send('UnfreezeTokens',['from',$this->account], function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
           return 'success';
        });
    }
}
$transaction = new Transaction(new Config);
$transaction->init();
 