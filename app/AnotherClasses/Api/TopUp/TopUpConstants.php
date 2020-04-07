<?php

namespace App\AnotherClasses\Api\TopUp;

class TopUpConstants
{
    const REQUEST_TEMPLATE = '<request><request-type>{request_type}</request-type><terminal-id>{terminal_id}</terminal-id><extra name="password">{password}</extra>{request_body}</request>';

    const CHECK_PAYMENT_REQUEST_BODY_TEMPLATE = '<status><payment><transaction-number>{transaction_number}</transaction-number><to><account-number>{account_number}</account-number></to></payment></status>';

    const CHECK_USER_REQUEST_BODY_TEMPLATE = '<extra name="phone">{phone_number}</extra><extra name="ccy">{currency}</extra>';

    const MAKE_PAYMENT_TO_BANK_CARD_BODY_TEMPLATE = '<auth><payment><transaction-number>{transaction_number}</transaction-number><from><ccy>{currency}</ccy></from><to><amount>{amount}</amount><ccy>{currency}</ccy><service-id>34020</service-id><account-number>{account_number}</account-number></to></payment></auth>';

    const MAKE_PAYMENT_TO_QIWI_WALLET_BODY_TEMPLATE = '<auth><payment><transaction-number>{transaction_number}</transaction-number><from><ccy>{currency}</ccy></from><to><amount>{amount}</amount><ccy>{currency}</ccy><service-id>99</service-id><account-number>{account_number}</account-number></to></payment></auth>';
}