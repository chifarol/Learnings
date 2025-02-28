<!-- @format -->

## Full Nodes and Mining Nodes

- Full Nodes are computers that run the bitcoin scripts
- Despite being critical Bitcoin network participants, full nodes don’t receive block rewards like miners
- Node operators usually run full nodes to
  1. support the network’s health and security, ensure privacy,
     OR
  2. for commercial reasons, such as exchanges or wallet services needing real-time, accurate blockchain data.
- The main difference between a full node and a miner is that while full nodes store and validate data, a miner is a type of node that can add blocks to a blockchain in additon to other capabilities. Without miners, no new transactions would get added to the blockchain.
- Full nodes can run on a standard computer, while miners need a more-than standard computer due to the sometimes heavy computation needed

### Other types of Bitcoin nodes include:

#### Light Nodes

- These nodes are also known as simplified payment verification (SPV) nodes.
- They run a version of Bitcoin software that stores a “lightweight” version of the blockchain containing only block headers.
- Light nodes must connect to full nodes to retrieve an entire block’s data.
- This setup allows them to verify transactions without needing the whole blockchain, making them suitable for devices with limited storage or processing power, like mobile wallets.

#### Lightning Nodes

- The Lightning Network is built on top of the Bitcoin network and allows faster and cheaper Bitcoin transactions coordinated by Lightning nodes.
- Lightning nodes form a network of payment channels that allows off-chain transactions, which are later settled on the Bitcoin blockchain.

#### Archive Nodes or Full Archival Nodes

- Archive nodes, or full archival nodes, keep an entire copy of the blockchain, including all transactions ever made.
- This allows them to provide historical data and serve other nodes that need to sync or verify the blockchain’s history.

#### Pruned nodes

- These nodes store the network’s history but only to a certain size.
- When reaching the size limit, they “prune” older data to store the latest blocks.

#### Mining pool nodes

- Mining pool nodes coordinate the resources of groups of miners.
- If a mining pool successfully verifies a block, the reward is distributed fairly between the pool participants.
