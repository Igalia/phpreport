import time
import jwt
from jwt import PyJWKClient
from decouple import config


JWT_SECRET = config("JWT_SECRET")
JWT_ALGORITHM = config("JWT_ALGORITHM")
JWT_AUDIENCE = config("JWT_AUDIENCE")
OIDC_CERTS_URL = config("OIDC_CERTS_URL")


def token_response(token: str):
    return {"access_token": token}


def decode_token(token: str) -> dict:
    jwks_client = PyJWKClient(OIDC_CERTS_URL)
    signing_key = jwks_client.get_signing_key_from_jwt(token)
    decoded_token = jwt.decode(token, signing_key.key, audience=JWT_AUDIENCE, algorithms=[JWT_ALGORITHM])
    return decoded_token if decoded_token["exp"] >= time.time() else None
