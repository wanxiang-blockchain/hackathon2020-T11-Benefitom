const TokenFactoryAbi = [
    {
        "constant": false,
        "inputs": [
            {
                "name": "_name",
                "type": "bytes32[10]"
            },
            {
                "name": "_symbol",
                "type": "bytes32[10]"
            },
            {
                "name": "_decimals",
                "type": "uint256"
            },
            {
                "name": "_fundingStartBlock",
                "type": "uint256"
            },
            {
                "name": "_fundingEndBlock",
                "type": "uint256"
            },
            {
                "name": "_initialExchangeRate",
                "type": "uint256"
            }
        ],
        "name": "create",
        "outputs": [
            {
                "name": "",
                "type": "address"
            }
        ],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    }
]
