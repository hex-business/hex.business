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
    private function setUserAccount(string $address)
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
    private function getUserAccount():string
    {
        if(!isset($_SESSION[self::WALLET_ADDRESS])) {
            throw new InvalidArgumentException('No record of user wallet account found!');
        }

        $wallet = trim($_SESSION[self::WALLET_ADDRESS]);
        if (empty($wallet)) {
            throw new InvalidArgumentException('No record of user wallet account found!');
        }

        return $wallet;
    }
    public function init()
    {
      
      if(!isset($_POST['type']) && empty($_POST['type']))
      {
        throw new InvalidArgumentException('You should input type');
      }
      else{
        $type = $_POST['type'];
        $amount  = 0;
        if(isset($_POST['amount']) && !empty($_POST['amount']))
        {
          $amount = $_POST['amount'];
          if($type=='transform')
            $this->transform($amount);
          else if($type=='approve')
           $this->approve($amount);
          else if($type=='freeze')
            $this->freeze($amount);
        }
        if($type=='unfreeze')
          $this->unfreeze();

      }

      
    }
    private function approve($amount)
    {
        //$this->moneyInstance->call()->__maxSupply($this->getUserAccount());
        $this->tokenInstance->at($this->tokenAddress)->send('approve',$this->moneyAddress, $amount,['from'=>$this->getUserAccount()] ,function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
           echo 'success';
        });
    }
    private function transform($amount)
    {
      
      $this->moneyInstance->send('transformHEX',$amount,'0x0000000000000000000000000000000000000000',['from'=>$this->getUserAccount()], function($err,$result){
        if(!empty($err)) {
            throw new InvalidArgumentException($err);
        }
       echo 'success';
       });
    }
    private function freeze($amount)
    {
        //$this->moneyInstance->call()->__maxSupply($this->getUserAccount());
        $this->moneyInstance->send('FreezeTokens', $amount,['from',$amount,$this->getUserAccount()], function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
            echo 'success';
        });
    }
    private function unfreeze()
    {
        //$this->moneyInstance->call()->__maxSupply($this->getUserAccount());
        $this->tokenInstance->at($this->tokenAddress)->send('UnfreezeTokens',['from'=>$this->getUserAccount()], function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
            echo 'success';
        });
    }
}
$transaction = new Transaction(new Config);
$transaction->init();
 