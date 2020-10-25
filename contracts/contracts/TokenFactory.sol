pragma solidity >=0.4.25 <0.7.0;

import "./BenifitToken.sol";

contract TokenFactory {

    function create(
        string memory _name,
        string memory _symbol,
        uint256 _decimals,
        uint256 _fundingStartBlock,
        uint256 _fundingEndBlock,
        uint256 _initialExchangeRate)
    public
    returns (address){
        BenifitToken tokenAddress = new BenifitToken(_name, _symbol,_decimals,_fundingStartBlock,_fundingEndBlock,_initialExchangeRate);
        return address(tokenAddress);
    }
}
