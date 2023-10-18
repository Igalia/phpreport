#!/bin/bash

key_file="jwtRS256.key"

ssh-keygen -t rsa -b 4096 -m PEM -N "" -f $key_file

# Append the private key to .env with newline characters
private_key="$(cat $key_file)"
private_key="${private_key//$'\n'/'\n'}"
echo "JWT_PRIVATE_KEY=\"$private_key\"" >> .env

# Append the public key to .env with newline characters
public_key="$(cat ${key_file}.pub)"
public_key="${public_key//$'\n'/'\n'}"
echo "JWT_PUB_KEY=\"$public_key\"" >> .env
