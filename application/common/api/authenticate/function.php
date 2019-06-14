<?php

function authenticateFailedNull(){
    return json(['errcode'=>1, 'msg'=>'username or password is null']);
}

function haveUser(){
    return json(['errcode'=>2, 'msg'=>'the username have been']);
}

function notFoundUser(){
    return json(['errcode'=>3, 'msg'=>'not found the user']);
}

function passwordNotSame(){
    return json(['errcode'=>4, 'msg'=>'password not same']);
}

