<?php

class ResponseStatus
{
    const INVALID_URL = 0;
    const SUCCESS_STATUS = 1;
    const INVALID_DOMAIN = 2;
    const REGISTRATION_ERROR = 3;
    const USER_EXISTS = 4;
    const PASSWORDS_NOT_MATCH = 5;
    const LOGIN_PASSWORD_INCORRECT = 6;
    const API_KEY_EXISTS = 7;
    const INVALID_LOGIN = 8;
    const INVALID_PASSWORD = 9;
    const INVALID_API_KEY = 10;
    const JSON_ERROR = 11;
    const URL_EXISTS = 12;
}