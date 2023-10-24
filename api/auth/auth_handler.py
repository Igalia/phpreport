import time
import jwt
from jwt import PyJWKClient
from datetime import datetime, timedelta
from decouple import config

JWT_SECRET = config("JWT_SECRET")
JWT_ALGORITHM = config("JWT_ALGORITHM")
JWT_AUDIENCE = config("JWT_AUDIENCE")
OIDC_CERTS_URL = config("OIDC_CERTS_URL")
JWT_PUB_KEY = config("JWT_PUB_KEY", None)
JWT_PRIVATE_KEY = config("JWT_PRIVATE_KEY", None)


def token_response(token: str):
    return {"access_token": token}


def decode_token(token: str) -> dict:
    if JWT_PUB_KEY:
        signing_key = str.encode(JWT_PUB_KEY)
    else:
        jwks_client = PyJWKClient(OIDC_CERTS_URL)
        test = jwks_client.get_signing_key_from_jwt(token)
        signing_key = test.key
    decoded_token = jwt.decode(token, signing_key, audience=JWT_AUDIENCE, algorithms=[JWT_ALGORITHM])
    return decoded_token if decoded_token["exp"] >= time.time() else None


def create_access_token(subject) -> str:
    expire = datetime.utcnow() + timedelta(minutes=5)
    to_encode = subject
    to_encode["exp"] = expire

    # FIXME for some reason, the decouple lib is adding \\n where
    # it should be \n when getting the key from the .env file, that's
    # why the replace is needed.
    key = str.encode(JWT_PRIVATE_KEY.replace("\\n", "\n"))

    encoded_jwt = jwt.encode(
        to_encode,
        key,
        algorithm=JWT_ALGORITHM,
    )

    return encoded_jwt
