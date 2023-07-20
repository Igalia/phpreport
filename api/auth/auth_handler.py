import time
import jwt
from decouple import config


JWT_SECRET = config("JWT_SECRET")
JWT_ALGORITHM = config("JWT_ALGORITHM")
JWT_AUDIENCE = config("JWT_AUDIENCE")


def token_response(token: str):
    return {"access_token": token}


def decode_token(token: str) -> dict:
    decoded_token = jwt.decode(token, JWT_SECRET, audience=JWT_AUDIENCE, algorithms=[JWT_ALGORITHM])
    return decoded_token if decoded_token["exp"] >= time.time() else None
