# Financial Management

## Features

- [ ] User accounts
    - [ ] A person must be able to create a new user account with an unique email and password
        - [ ] A user must verify their email
        - [ ] A user can set a display name
        - [ ] A user can set a unique phone number
    - [ ] A user must be able to editing their user account
        - [ ] A user may change their password
        - [ ] A user may change their display name
        - [ ] A user may change their phone number
    - [ ] A user must be able to login to their user account using email and password
    - [ ] A user must be able to logout of their user account
- [ ] Transactions
    - [ ] A logged in user must be able to create a new transaction with at least type and amount
        - [ ] A user can set type of their transaction to either paid, received or transfered
        - [ ] A user may set a description on their transaction
        - [ ] A user may set a date on their transaction
    - [ ] A logged in user must be able to edit an existing transaction
        - [ ] A user may change the description of a transaction
        - [ ] A user may change the amount of a transaction
        - [ ] A user may change the date of a transaction
        - [ ] A user may change the type of a transaction
    - [ ] A user can view a list of their transactions in a paginated manner
        - [ ] A user can search through their transaction's descriptions
        - [ ] A user can filter their transactions by date range
        - [ ] A user can filter their transactions by amount range
        - [ ] A user can filter their transactions by one or more tags
- [ ] A logged in user must be able to create a new owe with at least type and amount
    - [ ] A user can set type of their owe to either debit or credit
    - [ ] A user can link an owe to a transaction
    - [ ] A user can specify a party for an owe
- [ ] Tags
    - [ ] A logged in user must be able to create a new tag with a peice of text
    - [ ] A logged in user can add one or more tags to a transaction
    - [ ] A logged in user can remove a tag from a transaction
    - [ ] A logged in user can remove an existing tag
