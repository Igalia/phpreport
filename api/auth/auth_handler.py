import time
import jwt
from decouple import config


JWT_SECRET = config("JWT_SECRET")
JWT_ALGORITHM = config("JWT_ALGORITHM")


def token_response(token: str):
    return {"access_token": token}


def decode_token(token: str) -> dict:
    decoded_token = jwt.decode(token, JWT_SECRET, algorithms=[JWT_ALGORITHM])
    return decoded_token if decoded_token["expires"] >= time.time() else None
