# Insurance Quote Generator
Technical task to create an insurance quote generator for a cycle insurance company.

## The task
A cycle insurance company, Assurance X, need a new section to their website to allow users to enter all their details into a guided page by page insurance quote builder. There should be 3 pages to collect the users details with the following fields, all are required.

1. Users details
  - Title
  - First Name
  - Surname
  - Email
  - Date of Birth
  - House Number/Name
  - Postcode
2. Bike Details
  - Manufacturer
  - Model
  - Market Value
3. Cover type
  - Policy Start date
  - Type of Cover Required (three levels of cover, with their price multiplier, see the calculation below)
    - bronze = 1
    - silver = 2
    - gold = 3

You should then show the resulting yearly premium and allow them to pay by PayPal.

## The Premium
To calculate the premium find a way to check the crime level in their postcode (http://data.police.uk/docs/). The premium should be calculated as: (total number of crimes in the postcode) * (Bike Market Value / 20) * (type of cover)

The system should store the quotes so that the telesales team can call up customers who abandon the process.
