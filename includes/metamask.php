<?php
  require_once  __DIR__ . '/config.php';

  use Web3\Web3;
  use Web3\Contract;
  use Web3\Providers\HttpProvider;
  use Web3\RequestManagers\HttpRequestManager;

  // $web3 = new Web3('https://mainnet.infura.io/v3/c0f4f68a12e84857bedb6b76ebf5c14e/');
//   $web3 = new Web3(new HttpProvider(new HttpRequestManager('https://mainnet.infura.io/v3/c0f4f68a12e84857bedb6b76ebf5c14e', 3)));

//   $config = new Config();
//   $moneyAddress = $config->getMoneyAddress();
//   $tokenaddress = $config->getEtherConfigAddress();

//   $tokenABI = $config->getTokenABI();
//   $moneyContractABI = $config->getMoneyABI();



// $contract = new Contract($web3->provider, $moneyContractABI);


// $contract->at($moneyAddress)->call('_maxSupply', null, function ($err, $result) {
//    return "A";
// });


class Ethernum
{
    
    private const WALLET_ADDRESS = 'walletAddress';
    private $moneyInstance;
    private $tokenInstance;
    private $moneyAdderss;
    private $tokenAddress;
    private $account = '0x51C2609885753A1CB8B6901A933C15a0224CB57B';
    public $frozenBalances = 0;
    public $freezeReward = 0;
    public $allowance = 0;
    public $lockedToken = 0;
    public $freezeTokenBalance = 0;
    public $hxyTransformed = 0;
    public $heartsTransformed = 0;
    public $totalSupply = 0;
    public $maxSupply = 0;
    public $accountBalance = 0;
    public function __construct(Config $config)
    {
      
       
      $provider = $config->getProvider();
      if (empty($provider)) {
        throw new InvalidArgumentException("ethernum node uri cannot be empty");
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
    private function sendResult($heartsTransformed)
    { 
      $this->heartsTransformed = $heartsTransformed;
      print_r ($this->heartsTransformed );
    }
    /**
     * This function checks if the user is already logged
     * @return bool
     */
    public function isLogged():bool
    { 
        try {
            $this->getUserAccount();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setUserAccount(string $address)
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
    public function getUserAccount():string
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
    function getTokenFrozenBalances(): void
    {
       $this->moneyInstance->call('tokenFrozenBalances',$this->getUserAccount(), function ($err, $result) {
        if(!empty($err)) 
          throw new InvalidArgumentException('No record of user wallet account found!');
        else
          $this->freezeTokenBalance = dechex($result[0]->value);
        
      });
    }
    function getFreezingReward(): void
    {
      $this->moneyInstance->call('calcFreezingRewards', ['from'=>$this->account], function ($err, $result) {
        if(!empty($err)) 
          throw new InvalidArgumentException($err);
        else
          $this->freezingReward =dechex($result[0]->value);
      });
    }

    function getAllowance(): void
    {
      $this->tokenInstance->at($this->moneyAddress)->call('allowance',$this->getUserAccount(), $this->moneyAddress, function ($err, $result) {
        if(!empty($err)) 
          throw new InvalidArgumentException('No record of user wallet account found!');
        else
          $this->allowance = dechex($result[0]->value);
      });
    }
    function getLockedToken(): void
    {
      $this->moneyInstance->call('lockedTokens', null, function ($err, $result) {
        if(!empty($err)) 
          throw new InvalidArgumentException('No record of user wallet account found!');
        else
          $this->lockedToken = dechex($result[0]->value);
      });
    }

    function getFrzoneTokenBalance(): void
    {
      $this->moneyInstance->call('totalFrozenTokenBalance', null, function ($err, $result) {
        if(!empty($err)) 
          throw new InvalidArgumentException('No record of user wallet account found!');
        else
          $this->frozenTokenBalance = dechex($result[0]->value);
      });
    }
    function getHxyTransformed(): void
    {
      $this->moneyInstance->call('totalHXYTransformed', null, function ($err, $result) {
        if(!empty($err)) 
          throw new InvalidArgumentException('No record of user wallet account found!');
        else
          $this->hxyTransformed = dechex($result[0]->value);
      });
    }
    function getHeartsTransformed(): void
    {
      $this->moneyInstance->call('totalHeartsTransformed', null, function ($err, $result) {
        if(!empty($err)) 
          throw new InvalidArgumentException('No record of user wallet account found!');
        else
        {
          $this->getheartsTransformed = dechex($result[0]->value);
          $this->sendResult($result);
        }
      });
    }
   
    public function getTotalSupply()
    {
        $this->contractCall()->call('totalSupply', null, function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }

            $this->totalySupply = dechex($result[0]->value);
        });
    }

    public function getMaxSupply()
    {
        $this->contractCall()->call('_maxSupply', null, function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }

            $this->maxSupply = dechex($result[0]->value);
        });
    }

    public function getAccountBalance()
    {
        //$this->contractCall()->call()->__maxSupply($this->getUserAccount());
        $this->contractCall()->call('balanceOf',$this->getUserAccount(), function($err,$result){
            if(!empty($err)) {
                throw new InvalidArgumentException($err);
            }
           $this->accountBalance = dechex($result[0]->value);
        });
    }
 
}
$obj = new Ethernum(new Config);
$obj->init();
 
