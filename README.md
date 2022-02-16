<p align="center"><a href="https://aspireapp.com" target="_blank"><img src="https://global-uploads.webflow.com/5ed5b60be1889f546024ada0/5ed8a32c8e1f40c8d24bc32b_Aspire%20Logo%402x.png" width="128" alt="digital banking singapore" class="navbar-logo"></a></p>

## About

This is the REST API mini-project. It demonstrates a tiny portion of LMS (Loan Management System) that allows clients to apply for a loan. After filing a loan application, the reviewers can approve or reject the application. If the loan is approved, the repayments will have a weekly schedule for which users need to pay the due amount. In this, we are using fully amortized payment in which each rate will have the same value over time.

## Out of Scope

- The penalty feature adds a penalty record entry for late payments.
- The separate endpoints for pending/approved/rejected loans and paid/unpaid repayments.
- The interest rate based on business criteria and/or loan types.
- Multi-stage review process for the loan applications.
- The integration of a payment gateway.
- The different types of notifications to notify the platform users. 

## Prerequisites

Docker is required to install this project in one command.

## Installation

To install this project in a single command, please make sure that port `80` and `3306` is available before you execute the below script. If the ports are unavailable, you can set different ports in `APP_PORT` and `FORWARD_DB_PORT` variables respectively in the `.env.example` file.

```
$ ./init.sh
```

## How to Use

1. Hit **Register Reviewer** & **Register Client** endpoints, they will return `_token` for respective users that can be set in the `Authorization` header of Postman's `LMS` collection variables as `$reviewerToken` and `$clientToken` respectively to make subsequent authenticated requests
2. Hit **Loan Application** request to file a new loan application. By default, the loan status will be set as `PROCESSING`
3. Reviewers can update  the status using either **Approve Loan** or **Reject Loan** endpoints
   1. Execute **Approve Loan** request to approve the loan application and generate repayment records. On success, this will set the loan status to `APPROVED`
   2. Request **Reject Loan** to reject the loan application. The status will be `REJECTED`
4. **Submit Repayment** endpoint is used to pay for the installments

> In source code, there are a few comments prefixed with `NOTE:` and `TODO:` which describes the ideal/required flow.

> This project only uses contracts (`interfaces`) in the authentication section to demonstrate different ways to achieve the same results.

## Additional Endpoints
- **Login User:** Use this endpoint to generate a new token for existing users.
- **List Loans:** This can be used to list all the loans for each user type.
- **List Repayments:** It returns all the repayment entries for a particular loan.

## Postman Collection

<!-- TODO: Add postman collection link -->
Link to postman collection

## Test

```
$ docker compose exec aspire.localhost bash -c "php artisan test"
```

## License

Feel free to use this however you want.


<!-- TODO: test validation exception in login -->
<!-- TODO: test queues in event -->
<!-- TODO: test listeners -->
