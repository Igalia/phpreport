from fastapi import Request, HTTPException
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials

from .auth_handler import decode_token


class BearerToken(HTTPBearer):
    def __init__(self, auto_error: bool = True):
        super(BearerToken, self).__init__(auto_error=auto_error)

    async def __call__(self, request: Request):
        credentials: HTTPAuthorizationCredentials = await super(BearerToken, self).__call__(request)
        if credentials:
            if not credentials.scheme == "Bearer":
                raise HTTPException(status_code=401, detail="Invalid authentication scheme.")
            if not self.verify_token(credentials.credentials):
                raise HTTPException(status_code=401, detail="Invalid or expired token.")
            return credentials.credentials
        else:
            raise HTTPException(status_code=401, detail="Invalid authorization code.")

    def verify_token(self, token: str) -> bool:
        payload = decode_token(token)
        return True if payload else False
