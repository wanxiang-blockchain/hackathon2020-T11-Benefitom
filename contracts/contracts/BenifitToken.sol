pragma solidity >=0.4.25 <0.7.0;

import "./StandardToken.sol";
import "./Ownable.sol";

contract BenifitToken is StandardToken, Ownable {
  // Token configurations
  string public name;
  string public symbol;
  uint256 public decimals;

  // uint256 public constant _fundingStartBlock = 500;
  // 604800 seconds is 7 days. about 5040 blocks
  // uint256 public constant _fundingEndBlock = 10000;
  // uint256 public constant _initialExchangeRate = 100;

  /// the founder address can set this to true to halt the crowdsale due to emergency
  bool public halted = false;

  // number of tokens sold
  uint256 public tokensSold;

  // Crowdsale parameters
  uint256 public fundingStartBlock;
  uint256 public fundingEndBlock;
  uint256 public initialExchangeRate;


  // Events
  event Mint(uint256 supply, address indexed to, uint256 amount);
  event TokenPurchase(address indexed purchaser, address indexed beneficiary, uint256 value, uint256 amount);

  // Modifiers
  modifier validAddress(address _address) {
    require(_address != address(0));
    _;
  }

  modifier validPurchase() {
    require(block.number >= fundingStartBlock);
    require(block.number <= fundingEndBlock);
//    require(msg.value > 0);
    _;
  }

  modifier validUnHalt(){
    require(halted == false);
    _;
  }

  constructor(
    string memory _name,
    string memory _symbol,
    uint256 _decimals,
    uint256 _fundingStartBlock,
    uint256 _fundingEndBlock,
    uint256 _initialExchangeRate)
  public Ownable()
  {
    require(_fundingStartBlock >= block.number);
    require(_fundingEndBlock >= _fundingStartBlock);
    require(_initialExchangeRate > 0);

    fundingStartBlock = _fundingStartBlock;
    fundingEndBlock = _fundingEndBlock;
    initialExchangeRate = _initialExchangeRate;

    name = _name;
    symbol = _symbol;
    decimals = _decimals;
  }

//  /// @notice Fallback function to purchase tokens
//  function() external payable {
//    buyTokens(msg.sender);
//  }


  function buyTokens(uint256 amount)
    public
    validPurchase
    validUnHalt
  {
    ERC20 qcash = ERC20(0x0001b6728ac4717530f3cc5953728662a9f2fcf1483);

    require(amount > 0, "You need buy at least one");
    uint256 allowance = qcash.allowance(msg.sender, address(this));

    uint256 tokenAmount = getTokenExchangeAmount(amount, initialExchangeRate, 8, decimals);

    require(allowance >= amount, "Check the token allowance");
    tokensSold = tokensSold.add(tokenAmount);

    // Ensure new token increment does not exceed the sale amount
//    assert(tokensSold <= saleAmount);
    qcash.transferFrom(msg.sender, owner, amount);
    //msg.sender.transfer(amount);
    mint(msg.sender, tokenAmount);
    emit TokenPurchase(msg.sender, msg.sender, amount, tokenAmount);

    //forwardFunds();
  }

  /// @notice Allows contract owner to mint tokens at any time
  /// @param _amount Amount of tokens to mint in lowest denomination of VEVUE
  function mintReservedTokens(uint256 _amount) public onlyOwner {
//    uint256 checkedSupply = totalSupply.add(_amount);
//    require(checkedSupply <= tokenTotalSupply);

    mint(owner, _amount);
  }


  function getTokenExchangeAmount(
    uint256 _Amount,
    uint256 _exchangeRate,
    uint256 _nativeDecimals,
    uint256 _decimals)
    public
    view
    returns(uint256)
  {
    require(_Amount > 0);

    uint256 differenceFactor = (10**8) / (10**_decimals);
    return _Amount.mul(_exchangeRate).div(differenceFactor);
  }

//  /// @dev Sends QCash to the contract owner
//  function forwardFunds() internal {
//    owner.transfer(msg.value);
//  }

  /// @dev Mints new tokens
  /// @param _to Address to mint the tokens to
  /// @param _amount Amount of tokens that will be minted
  /// @return Boolean to signify successful minting
  function mint(address _to, uint256 _amount) internal returns (bool) {
    totalSupply += _amount;
    balances[_to] = balances[_to].add(_amount);
    emit Mint(totalSupply, _to, _amount);
    return true;
  }

  /// Emergency Stop ICO
  function halt() public onlyOwner {
    halted = true;
  }

  function unhalt() public onlyOwner {
    halted = false;
  }
}
