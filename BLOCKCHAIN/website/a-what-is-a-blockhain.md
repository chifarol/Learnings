<!-- @format -->

## What Is a Blockchain

A blockchain is a distributed database or ledger shared across a computer network's nodes. it can be used to make data in any industry immutable it has been in use in different systems apart from cryptocurrency.
In cryptocurrency, the blockchain is useful in maintaining a secure and decentralized record of transactions

- Since a block can’t be changed, the only trust needed is at the point where **_a user or program enters data_**

## Blockchain as a database

- Blockchain is a type of **shared** database or spreadsheet
- The difference is that it stores data in blocks linked together via cryptography.
- Different types of information can be stored on a blockchain, but the most common use has been as a transaction ledger.
- Additionally, a blockchain consists of programs (called scripts) that automatically conducts the tasks like reading, writing, storing operations.

### how Bitcoin creates/populates a block

- The Bitcoin blockchain collects transaction information and enters it into a 4MB file called a block (different blockchains have different size blocks).
- Once the block is full, the block data is run through a cryptographic hash function, which creates a hexadecimal number called the block **header hash**.
- The hash is then entered into the following block header and encrypted with the other information in that block's header, creating a chain of blocks, hence the name “blockchain.”

### Transaction Process

- If you initiate a transaction using your cryptocurrency wallet — (the application that provides an interface for the blockchain) — it starts a sequence of events.
- A bitcoin node picks up your transaction, validates it against the network’s rules and history. Then broadcast it to a memory pool (mempool) of unconfirmed transactions waiting for miners to pick it up.
- it is then entered into a block and the block fills up with transactions, it is closed, and the **mining begins**.
- Miners now compete with each other to solve complex mathematical problems, allowing the successful node to complete proof-of-work consensus and add the new block then earn the reward. This takes about 10 mins on average

- The new block will be broadcast back to full nodes and checked for adherence to the network rules, including rules for creating new Bitcoin.

- Once a new block is validated and added to the blockchain, this updated version of the blockchain is not broadcast in its entirety. Rather, the new block itself is broadcast, and other nodes independently validate and add this block to their blockchain copies.

- Bitcoin mining process is based on proof-of-work (Etheruem is based on proof-of-stake)
